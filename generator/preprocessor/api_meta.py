import codecs
import json
import re
import sys

apiPath = 'https://www.kucoin.com/docs-new/api-'
docPath = 'https://www.kucoin.com/docs-new/doc-'


class ApiMetaUtil:
    doc_id = []

    @staticmethod
    def collect_api(collection, result: list):
        if type(collection) is list:
            for c in collection:
                ApiMetaUtil.collect_api(c, result)

        if 'items' in collection:
            ApiMetaUtil.collect_api(collection['items'], result)

        for c in collection:
            if type(c) is dict and 'api' in c:
                result.append(c)

    @staticmethod
    def group_api(api_collection, entry) -> dict:
        result = {}

        for api in api_collection:
            if 'api' not in api:
                raise Exception("illegal api" + api)

            api_name = api['name']
            api_data = api['api']
            api_doc = ApiMetaUtil.gen_doc_api_url(api_data['id'], False)
            if 'customApiFields' not in api_data:
                raise Exception("customApiFields not found in meta json")

            api_fields = json.loads(api_data['customApiFields'])
            if len(api_fields) == 0:
                raise Exception("illegal customApiFields" + api_name)

            x_fields = {
                'x-api-doc': api_doc,
            }
            for k in api_fields:
                x_fields[f'x-{k}'] = api_fields[k]

            if 'x-domain' not in x_fields:
                raise Exception('no domain field found for ' + api_name)

            api['x-fields'] = x_fields

            service = x_fields['x-sdk-service'].lower().strip()
            sub_service = x_fields['x-sdk-sub-service'].lower().strip()

            service_id = (service, sub_service)
            if entry:
                service_id = service

            if service_id not in result:
                result[service_id] = []

            result[service_id].append(api)

        return result

    @staticmethod
    def parse(file_path):
        try:
            with open(file_path, "r") as file:
                file_content = file.read()
            json_content = json.loads(file_content)

            ApiMetaUtil.doc_id = json_content['doc_id']
            api_list = list()

            for collection in json_content['apiCollection']:
                ApiMetaUtil.collect_api(collection, api_list)

            return api_list


        except Exception as e:
            print(f"An error occurred when read meta file: {e}")
            sys.exit(1)

    @staticmethod
    def gen_doc_api_url(id, doc: bool):
        if doc:
            return f'{docPath}{id}'
        return f'{apiPath}{id}'

    @staticmethod
    def escape_url(desc, doc_id):
        pattern = r"apidog://link/(pages|endpoint)/(\d+)"
        match = re.search(pattern, desc)
        if match:
            number = match.group(2)
            url = ''
            if int(number) in doc_id:
                url = ApiMetaUtil.gen_doc_api_url(number, True)
            else:
                url = ApiMetaUtil.gen_doc_api_url(number, False)
            desc = re.sub(pattern, url, desc)
        return desc

    @staticmethod
    def update_doc_desc(schema):
        if schema and isinstance(schema, dict) and 'properties' in schema:
            for p in schema['properties'].values():
                if isinstance(p, dict) and 'description' in p:
                    p['description'] = ApiMetaUtil.escape_url(p['description'], ApiMetaUtil.doc_id)

    @staticmethod
    def update_tuple(schema):
        if type(schema) is dict and schema.get('type') == 'array':
            if 'items' in schema:
                if 'anyOf' in schema['items']:
                    anyOf = schema['items']['anyOf']
                    simple_type = False
                    for i in anyOf:
                        if i['type'] in ['boolean', 'integer', 'number', 'string']:
                            simple_type = True

                    if simple_type:
                        schema['items'] = {
                            'type': 'AnyType'
                        }

    @staticmethod
    def update_response_schema_required(schema):

        if type(schema) is dict:
            if 'required' in schema:
                schema['required'] = []
            for k in schema:
                ApiMetaUtil.update_response_schema_required(schema[k])

        if type(schema) is list:
            for s in schema:
                ApiMetaUtil.update_response_schema_required(s)

    @staticmethod
    def update_schema(schema):
        ApiMetaUtil.update_doc_desc(schema)
        ApiMetaUtil.update_tuple(schema)

        if type(schema) is dict:
            if 'examples' in schema:
                schema['example'] = schema['examples']
                del schema['examples']
            for k in schema:
                ApiMetaUtil.update_schema(schema[k])

        if type(schema) is list:
            for s in schema:
                ApiMetaUtil.update_schema(s)

    @staticmethod
    def gen_default_value_for_example(schema):
        if 'type' not in schema:
            raise RuntimeError('unexcepted schema' + schema)

        schema_type = schema['type']
        if schema_type == 'integer':
            if 'schema' in schema and 'enum' in schema['schema']:
                return schema['schema']['enum'][0]
            if 'schema' in schema and 'default' in schema['schema']:
                return schema['schema']['default']
            if 'schema' in schema and 'maximum' in schema['schema']:
                return schema['schema']['maximum']
            return 123456
        elif schema_type == 'number':
            return 123456.0
        elif schema_type == 'string':
            if 'schema' in schema and 'enum' in schema['schema']:
                return schema['schema']['enum'][0]
            return 'example_string_default_value'
        elif schema_type == 'boolean':
            return True
        elif schema_type == 'array':
            if schema['schema']['items']['type'] == 'string':
                return ["mock_a", "mock_b"]
            elif schema['schema']['items']['type'] == 'integer':
                return [123456, 123456]
            elif schema['schema']['items']['type'] == 'number':
                return [123456.0, 123456.0]
            elif schema['schema']['items']['type'] == 'boolean':
                return [True, False]
            else:
                raise RuntimeError('unexcepted schema type' + schema['schema']['type'])
        else:
            raise RuntimeError('unexcepted schema type' + schema_type)

    @staticmethod
    def generate_path_operation(api):
        api_name = api['name']
        api_data = api['api']
        api_fields = api['x-fields']

        has_parameters = False

        path_operation = {
            'summary': api_name,
            'deprecated': api_fields['x-abandon'] == 'abandon',
            'description': api_fields['x-sdk-method-description'],
            'tags': [],
            'parameters': [],
            # "requestBody": {},
            'responses': {},
        }

        path_operation.update(api_fields)

        # request example
        req_example = {}

        # parameters
        for i in api_data['parameters']:
            if i == 'path':
                for path_para in api_data['parameters']['path']:
                    has_parameters = True
                    result = {
                        'name': path_para['name'],
                        'in': 'path',
                        'description': ApiMetaUtil.escape_url(path_para['description'], ApiMetaUtil.doc_id),
                        'required': path_para['required'],
                        'schema': {
                            'type': path_para['type'],
                        }
                    }
                    path_operation['parameters'].append(result)

                    example_value = None
                    if 'example' in path_para:
                        example_value = path_para['example']
                    else:
                        example_value = ApiMetaUtil.gen_default_value_for_example(path_para)
                    req_example[path_para['name']] = example_value
            if i == 'query':
                for query_para in api_data['parameters']['query']:
                    has_parameters = True
                    schema = {}

                    if 'schema' in query_para:
                        ApiMetaUtil.update_schema(query_para['schema'])
                        schema = query_para['schema']
                    elif 'type' in query_para:
                        schema = {
                            'type': query_para['type'],
                        }

                    result = {
                        'name': query_para['name'],
                        'in': 'query',
                        'description': ApiMetaUtil.escape_url(query_para['description'], ApiMetaUtil.doc_id),
                        'required': query_para['required'],
                        'schema': schema,
                    }

                    name = query_para['name']
                    if 'example' in query_para and query_para['example'] != '':
                        if 'type' in schema and schema['type'] != 'string':
                            if schema['type'] == 'integer':
                                req_example[name] = int(query_para['example'])
                            elif schema['type'] == 'number':
                                req_example[name] = float(query_para['example'])
                            elif schema['type'] == 'boolean':
                                req_example[name] = bool(query_para['example'])
                        else:
                            req_example[name] = query_para['example']

                    if 'schema' in query_para and 'example' in query_para['schema']:
                        schema = query_para['schema']
                        example = schema['example']

                        if isinstance(example, list) and schema['type'] != 'array':
                            example = example[0]
                        req_example[name] = example

                    if name not in req_example:
                        req_example[name] = ApiMetaUtil.gen_default_value_for_example(query_para)

                    path_operation['parameters'].append(result)

        # requestBody
        if 'requestBody' in api_data and api_data['requestBody']['type'] == 'application/json':

            if api_data['method'] != 'post':
                path_operation['x-request-force-json'] = True

            body_data = api_data['requestBody']

            example_data = None
            if 'examples' in body_data:
                examples = body_data['examples']
                if len(examples) > 0:
                    example = examples[0]
                    if isinstance(example, dict):
                        example_data = example['value']



            ApiMetaUtil.update_schema(body_data['jsonSchema'])
            # ApiMetaUtil.update_response_schema_required(body_data)
            path_operation['requestBody'] = {
                'content': {
                    'application/json': {
                        'schema': body_data['jsonSchema'],
                        'example': example_data,
                    }
                }
            }
            try:
                example_raw = example_data
                filtered_data = "\n".join(
                    line for line in example_raw.splitlines() if not line.strip().startswith("//"))

                j = json.loads(filtered_data)
                if len(req_example) == 0 and isinstance(j, list):
                    req_example = j
                else:
                    req_example.update(j)
            except Exception as e:
                print(e)
                pass

        # responses
        if 'responses' in api_data:
            for response_data in api_data['responses']:
                if response_data['code'] == 200:
                    ApiMetaUtil.update_schema(response_data['jsonSchema'])
                    path_operation['responses'] = {
                        '200': {
                            'description': response_data['description'],
                            'content': {
                                'application/json': {
                                    'schema': response_data['jsonSchema'],
                                }
                            }
                        }
                    }

        if 'responseExamples' in api_data:
            examples = api_data['responseExamples']
            for ex in examples:
                if ex.get('name') == 'Success':
                    example_raw = ex['data']
                    filtered_data = "\n".join(
                        line for line in example_raw.splitlines() if not line.strip().startswith("//"))
                    data = codecs.encode(filtered_data, 'unicode_escape').decode('utf-8')
                    data = data.replace('"', '\\"')
                    path_operation['x-response-example'] = data
                    break

        if len(req_example) > 0:
            try:
                data = codecs.encode(json.dumps(req_example), 'unicode_escape').decode('utf-8')
                data = data.replace('"', '\\"')
                path_operation['x-request-example'] = data
            except Exception as e:
                pass

        return path_operation

    @staticmethod
    def generator_openapi(service, api_collection, entry):
        paths = {}
        schemas = {}

        api_idx = 0
        for api in api_collection:
            api_idx = api_idx + 1
            path_operation = ApiMetaUtil.generate_path_operation(api)
            path_operation['operationId'] = "{:03d}".format(api_idx)
            method = api['api']['method']
            path = api['api']['path']

            if path not in paths:
                paths[path] = {}

            if method in paths[path]:
                if 'patch' in paths[path]:
                    # support multiple method collision when necessary
                    raise RuntimeError(f"same path for same service twice!! {method} {path}")
                paths[path]['patch'] = path_operation
                path_operation["x-original-method"] = method
            else:
                paths[path][method] = path_operation

        openapi = {
            "openapi": "3.0.1",
            "info": {
                "title": service if entry else service[0],
                "description": '' if entry else service[1],
                "version": "1.0.0"
            },
            "tags": [],
            "paths": paths,
            "components": {
                "schemas": schemas
            }
        }
        return openapi

    @staticmethod
    def write(out_path, openapi):
        try:
            with open(out_path, 'w', encoding='utf-8') as file:
                json.dump(openapi, file, ensure_ascii=False, indent=4)
        except Exception as e:
            print(f"An error occurred when generate ws specification file: {e}")
            sys.exit(1)
