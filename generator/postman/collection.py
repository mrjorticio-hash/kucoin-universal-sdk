import json
import re
import sys
from json import JSONDecodeError

import script
from costant import docPath, apiPath, metaPath, outputPath, brokerFolderName


class Collection:

    def __init__(self):
        self.path_var = set()
        self.title = ""
        self.doc_id = set()

    def generate_collection(self, subdir, title):
        self.title = title

        meta_data = self.parse(metaPath, subdir)

        collection_data = self.gen_data(meta_data)

        collection = self.gen_postman(collection_data)

        self.write(f"{outputPath}/collection-{subdir}.json", collection)

    def parse(self, file_path, subdir):
        try:
            with open(file_path, "r") as file:
                file_content = file.read()
            meta_data = json.loads(file_content)

            self.doc_id = set(meta_data['doc_id'])
            if 'apiCollection' not in meta_data:
                raise RuntimeError("incomplete meta data")

            api_collection = meta_data['apiCollection'][0]
            if 'items' not in api_collection:
                raise RuntimeError("incomplete meta data")

            for item in api_collection['items']:
                if item['name'] == subdir:
                    return item

            raise RuntimeError("no rest subdir found")

        except Exception as e:
            print(f"An error occurred when read meta file: {e}")
            sys.exit(1)

    def write(self, out_path, postman):
        try:
            with open(out_path, 'w', encoding='utf-8') as file:
                json.dump(postman, file, ensure_ascii=False, indent=4)
        except Exception as e:
            print(f"An error occurred when generate postman specification file: {e}")
            sys.exit(1)

    def gen_api(self, item):
        if 'api' not in item:
            raise RuntimeError(f"incomplete data for api {item}")

        api = item['api']
        path = [part for part in api['path'].split('/') if part]
        x_tags = json.loads(api['customApiFields'])
        if 'domain' not in x_tags:
            raise RuntimeError(f'no domain tag found in {api}')
        domain = x_tags['domain'].lower()

        parameters = api['parameters']
        request = api['requestBody']
        responses = api['responseExamples']

        description_detail = self.gen_markdown(api)

        query = []
        path_var = []

        # update query
        if parameters['query']:
            for p in parameters['query']:
                query.append({
                    'key': p['name'],
                    'value': p['example'] if 'example' in p else None,
                    'description': self.escape_url(p['description'])
                })

        # update path
        if parameters['path']:
            for p in parameters['path']:
                path_var_name = p['name']
                path_var_name_ext = "{" + path_var_name + "}"
                path_var.append(path_var_name)
                self.path_var.add(path_var_name)

                if path_var_name_ext in path:
                    index = path.index(path_var_name_ext)
                    path[index] = "{{" + path_var_name + "}}"




        # update request
        req_body = {}
        if 'type' in request and request['type'] == 'application/json':

            example_data = None
            if 'examples' in request:
                examples = request['examples']
                if len(examples) > 0:
                    example = examples[0]
                    if isinstance(example, dict):
                        example_data = example['value']

            req_body = {
                "mode": "raw",
                "raw": example_data,
                "options": {
                    "raw": {
                        "language": "json"
                    }
                }
            }

        # update response
        resp_body_data = ''
        for resp_example in responses:
            if resp_example['name'] == 'Success':
                try:
                    resp_body_data = json.dumps(json.loads(resp_example['data']), ensure_ascii=False, indent=4)
                except JSONDecodeError as e:
                    resp_body_data = resp_example['data']
                    pass

        postman_obj = {
            "name": item['name'],
            "request": {
                "method": api['method'].upper(),
                "header": [],
                "url": {
                    "raw": "",
                    "protocol": "https",
                    "host": [
                        "{{%s}}" % (domain + "_endpoint")
                    ],
                    "path": path,
                    'query': query,
                },
                "description": description_detail,
                "body": req_body,
            },
            "response": [
                {
                    "name": "Successful Response",
                    "originalRequest": {
                        "method": api['method'].upper(),
                        "header": [],
                        "url": {
                            "raw": "",
                            "protocol": "https",
                            "host": [
                                "{{%s}}" % (domain + "_endpoint")
                            ],
                            "path": path,
                            'query': query
                        }
                    },
                    "status": "OK",
                    "code": 200,
                    "_postman_previewlanguage": "json",
                    "header": [
                        {
                            "key": "Content-Type",
                            "value": "application/json",
                            "name": "Content-Type",
                            "description": {
                                "content": "",
                                "type": "text/plain"
                            },
                        },
                        {
                            "key": "gw-ratelimit-remaining",
                            "value": 1997,
                            "name": "gw-ratelimit-remaining"
                        },
                        {
                            "key": "gw-ratelimit-limit",
                            "value": 2000,
                            "name": "gw-ratelimit-limit"
                        },
                        {
                            "key": "gw-ratelimit-reset",
                            "value": 29990,
                            "name": "gw-ratelimit-reset"
                        }
                    ],
                    "cookie": [],
                    "body": resp_body_data,
                }
            ],

        }

        return postman_obj

    def properties_to_markdown_table(self, prop):
        if not prop:
            return ""

        headers = prop[0].keys()

        markdown_table = "| " + " | ".join(headers) + " |\n"
        markdown_table += "| " + " | ".join(["---" for _ in headers]) + " |\n"

        # Populate the rows
        for item in prop:
            row = [str(item.get(header, "")) for header in headers]
            markdown_table += "| " + " | ".join(row) + " |\n"

        return markdown_table

    def gen_doc_api_url(self, id, doc: bool):
        if doc:
            return f'{docPath}{id}'
        return f'{apiPath}{id}'

    def escape_markdown(self, markdown):
        markdown = markdown.replace('\n', '<br>')
        markdown = markdown.replace('|', '\\|')
        return markdown

    def escape_url(self, markdown):
        pattern = r"apidog://link/(pages|endpoint)/(\d+)"
        match = re.search(pattern, markdown)
        if match:
            number = match.group(2)
            url = ''
            if int(number) in self.doc_id:
                url = self.gen_doc_api_url(number, True)
            else:
                url = self.gen_doc_api_url(number, False)
            markdown = re.sub(pattern, url, markdown)
        return markdown

    def generate_markdown_schema(self, parent, order, schema) -> dict:

        markdown_sections = []
        prop = []

        if schema['type'] == 'array':
            sections = self.generate_markdown_schema(parent, order + 1, schema['items'])
            if len(sections) > 0:
                markdown_sections.extend(sections)

        if schema['type'] == 'object':
            for name, value in schema['properties'].items():
                ref = False
                if value['type'] == 'object' or value['type'] == 'array':
                    inner = self.generate_markdown_schema(name, order + 1, value)
                    markdown_sections.extend(inner)
                    ref = True

                description = value['description'] if 'description' in value else ''
                if ref:
                    description = f'Refer to the schema section of {name}'

                description = self.escape_url(description)
                description = self.escape_markdown(description)

                prop.append({
                    'name': name,
                    'type': value['type'],
                    'description': description,
                })
            markdown_sections.append({
                'parent': parent,
                'value': self.properties_to_markdown_table(prop),
                'order': order,
            })

        return markdown_sections

    def gen_markdown(self, api):
        api_doc_addr = self.gen_doc_api_url(api['id'], False)
        api_doc = api['description']

        request = api['requestBody']
        response = None
        for r in api['responses']:
            if r['code'] == 200:
                response = r

        def gen_inner(obj, title):
            markdown_section = {}
            if 'jsonSchema' in obj:
                schema = obj['jsonSchema']
                markdown_section = self.generate_markdown_schema('root', 0, schema)
            else:
                markdown_section = [{'parent': 'root', 'value': '', 'order': 0}]

            markdown_section = sorted(markdown_section, key=lambda x: x['order'])
            nonlocal api_doc
            api_doc += f'\n**{title} Body**\n\n'

            parent = ''
            api_doc += '---\n'
            for section in markdown_section:
                if parent == '' and section['value'] == '':
                    api_doc += f'  **None**  \n'
                    break
                if parent == '':
                    parent = f'root'
                else:
                    parent = f'{parent}.{section["parent"]}'
                api_doc += f'**{parent} Schema**\n\n'
                api_doc += f'{section["value"]}\n'
            api_doc += '---\n'

        api_doc = f'# API Description\n\nFor the complete API documentation, please refer to [doc]({api_doc_addr})\n\n' + api_doc
        api_doc += f'\n# API Schema\n\n'
        api_doc += f'## Request Schema\n\n'
        gen_inner(request, "Request")
        api_doc += f'## Response Schema\n\n'
        gen_inner(response, "Response")

        return api_doc

    def gen_data(self, meta_data):
        postman_item = []

        if 'items' in meta_data:
            for item in meta_data['items']:
                is_dir = 'items' in item

                postman_obj = {}
                if is_dir:
                    postman_obj = {
                        'name': item['name'],
                        'item': self.gen_data(item),
                        "description": '',
                    }

                    if item['name'] == brokerFolderName:
                        postman_obj["event"] = [
                            {
                                "listen": "prerequest",
                                "script": {
                                    "type": "text/javascript",
                                    "packages": {},
                                    "exec": [
                                        script.get_script(True)
                                    ]
                                }
                            },
                            {
                                "listen": "test",
                                "script": {
                                    "type": "text/javascript",
                                    "packages": {},
                                    "exec": [
                                        ""
                                    ]
                                }
                            }
                        ]
                else:
                    postman_obj = self.gen_api(item)

                postman_item.append(postman_obj)

        return postman_item

    def gen_postman(self, postman_data):

        variables = []
        for var in sorted(self.path_var):
            variables.append({
                "key": var,
                "value": "",
            })

        postman = {
            "info": {
                "_postman_id": "e22e2bc3-0147-4151-9872-e9f6bda04a9c",
                "name": self.title,
                "description": f"For the complete API documentation, please refer to https://www.kucoin.com/docs-new",
                "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json",
            },
            "item": postman_data,
            "variable": variables,
            "event": [
                {
                    "listen": "prerequest",
                    "script": {
                        "type": "text/javascript",
                        "packages": {},
                        "exec": [
                            script.get_script(False)
                        ]
                    }
                },
                {
                    "listen": "test",
                    "script": {
                        "type": "text/javascript",
                        "packages": {},
                        "exec": [
                            ""
                        ]
                    }
                }
            ],
        }
        return postman
