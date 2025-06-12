import json
import sys


class MetaTools:

    @staticmethod
    def clear_field(obj):
        clean_key = []
        rename_key = []

        if type(obj) is dict:
            for k in obj:
                if type(k) is str and k.startswith('x-') and k.endswith('-orders'):
                    clean_key.append(k)
                elif type(k) is str and k.startswith('x-') and k.endswith('-mock'):
                    clean_key.append(k)
                elif type(k) is str and k.startswith('x-') and k.endswith('-enum') and k != 'x-api-enum':
                    clean_key.append(k)
                    rename_key.append(k)
                else:
                    MetaTools.clear_field(obj[k])
            for k in rename_key:
                obj['x-api-enum'] = obj[k]
            for k in clean_key:
                del obj[k]
        if type(obj) is list:
            for o in obj:
                MetaTools.clear_field(o)

    @staticmethod
    def clear_developing(obj):
        if 'apiCollection' in obj:
            api_collection = obj['apiCollection']
            if isinstance(api_collection,list) and 'items' in api_collection[0]:
                for item in api_collection[0]['items']:
                    if 'name' in item and item['name'] == 'Developing':
                        item['items'] = []

    @staticmethod
    def clear_api_collection(data):
        if type(data) is list:
            for d in data:
                MetaTools.clear_api_collection(d)

        # common
        if type(data) is dict and 'items' in data:
            clear_keys = []
            for k in data:
                if k not in {'name', 'id', 'items', 'description'}:
                    clear_keys.append(k)

            for k in clear_keys:
                del data[k]

            for d in data['items']:
                MetaTools.clear_api_collection(d)

        # apis
        if type(data) is dict and 'api' in data and 'name' in data:
            clear_keys = []
            for k in data['api']:
                if k not in {'name', 'id', 'customApiFields', 'method', 'path', 'description', 'parameters', 'requestBody', 'responses', 'responseExamples'}:
                    clear_keys.append(k)

            for k in clear_keys:
                del data['api'][k]

    def gen_doc_id_collection(self, collection):
        id = set()

        if 'items' in collection:
            for item in collection['items']:
                id.add(item['id'])

        for doc in collection['children']:
            id.update(self.gen_doc_id_collection(doc))

        return id



    def clear_garbage(self):
        MetaTools.clear_api_collection(self.data['apiCollection'])

        if 'docCollection' in self.data:
            doc_id = self.gen_doc_id_collection(self.data['docCollection'][0])
        else:
            doc_id = self.data['doc_id']

        self.data = {
            'apiCollection' : self.data['apiCollection'],
            'schemaCollection': self.data['schemaCollection'],
            'doc_id': list(doc_id),
        }


    def __init__(self, file_path:str):
        try:
            with open(file_path, "r") as file:
                file_content = file.read()
                self.data = json.loads(file_content)
                self.file_path = file_path

        except Exception as e:
            print(f"An error occurred when read meta file: {e}")
            sys.exit(1)

    def clean(self):
        self.clear_garbage()
        MetaTools.clear_developing(self.data)
        MetaTools.clear_field(self.data)

    def write(self):
        with open(self.file_path, 'w', encoding='utf-8') as file:
            json.dump(self.data, file, ensure_ascii=False, indent=4)






