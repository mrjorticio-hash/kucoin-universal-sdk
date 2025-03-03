package com.kucoin.universal.sdk.plugin.generator;

import com.kucoin.universal.sdk.plugin.model.EnumEntry;
import com.kucoin.universal.sdk.plugin.model.Meta;
import com.kucoin.universal.sdk.plugin.model.ModeSwitch;
import com.kucoin.universal.sdk.plugin.service.NameService;
import com.kucoin.universal.sdk.plugin.service.OperationService;
import com.kucoin.universal.sdk.plugin.service.SchemaService;
import com.kucoin.universal.sdk.plugin.service.impl.OperationServiceImpl;
import com.kucoin.universal.sdk.plugin.service.impl.SchemaServiceImpl;
import com.kucoin.universal.sdk.plugin.util.SpecificationUtil;
import com.opencsv.CSVReader;
import io.swagger.v3.oas.models.OpenAPI;
import io.swagger.v3.oas.models.Operation;
import io.swagger.v3.oas.models.media.Schema;
import io.swagger.v3.oas.models.servers.Server;
import lombok.extern.slf4j.Slf4j;
import org.apache.commons.lang3.StringUtils;
import org.openapitools.codegen.*;
import org.openapitools.codegen.languages.AbstractPythonCodegen;
import org.openapitools.codegen.model.ModelMap;
import org.openapitools.codegen.model.ModelsMap;
import org.openapitools.codegen.model.OperationMap;
import org.openapitools.codegen.model.OperationsMap;
import org.openapitools.codegen.utils.ModelUtils;

import javax.annotation.Nullable;
import java.io.File;
import java.io.FileReader;
import java.util.*;

import static org.openapitools.codegen.utils.CamelizeOption.LOWERCASE_FIRST_LETTER;
import static org.openapitools.codegen.utils.StringUtils.camelize;
import static org.openapitools.codegen.utils.StringUtils.underscore;

/**
 * @author isaac.tang
 */
@Slf4j
public class PythonSdkGenerator extends AbstractPythonCodegen implements NameService {
    private SchemaService schemaService;
    private OperationService operationService;
    private ModeSwitch modeSwitch;
    // map of set (model imports)
    private HashMap<String, HashSet<String>> circularImports = new HashMap<>();
    // map of codegen models
    private HashMap<String, CodegenModel> codegenModelMap = new HashMap<>();

    // model export(init.py)
    private Set<String> exports = new LinkedHashSet<>();
    private Set<String> serviceExports = new LinkedHashSet<>();

    private String service;
    private String subService;


    public CodegenType getTag() {
        return CodegenType.OTHER;
    }

    public String getName() {
        return "python-sdk";
    }

    public String getHelp() {
        return "Generates a python-sdk library.";
    }

    public PythonSdkGenerator() {
        super();
        cliOptions.add(ModeSwitch.option);
        typeMapping.put("Bigint", "int");
        typeMapping.put("bigint", "int");
    }

    @Override
    public void processOpts() {
        super.processOpts();
        modeSwitch = new ModeSwitch(additionalProperties);

        service = openAPI.getInfo().getTitle();
        subService = openAPI.getInfo().getDescription();

        switch (modeSwitch.getMode()) {
            case WS: {
                modelTemplateFiles.put("model_ws.mustache", ".py");
                apiTemplateFiles.put("api_ws.mustache", ".py");
                additionalProperties.put("WS_MODE", "true");
                supportingFiles.add(new SupportingFile("module.mustache", String.format("./%s/%s/__init__.py", service, formatPackage(subService))));
                break;
            }
            case WS_TEST: {
                apiTemplateFiles.put("api_ws_test.mustache", ".py");
                break;
            }
            case API: {
                modelTemplateFiles.put("model.mustache", ".py");
                apiTemplateFiles.put("api.mustache", ".py");
                supportingFiles.add(new SupportingFile("module.mustache", String.format("./%s/%s/__init__.py", service, formatPackage(subService))));
                break;
            }
            case TEST_TEMPLATE: {
                apiTemplateFiles.put("api_test_template.mustache", ".template");
                break;
            }
            case ENTRY: {
                apiTemplateFiles.put("api_entry.mustache", ".py");
                supportingFiles.add(new SupportingFile("module_service.mustache", String.format("./%s/__init__.py", "service")));
                break;
            }
            case TEST: {
                apiTemplateFiles.put("api_test.mustache", ".py");
                break;
            }
            default:
                throw new RuntimeException("unsupported mode");
        }

        supportingFiles.add(new SupportingFile("version.mustache", "version.py"));
        supportingFiles.add(new SupportingFile("module_empty.mustache", "__init__.py"));
        supportingFiles.add(new SupportingFile("module_empty.mustache", String.format("./%s/__init__.py", service)));

        templateDir = "python-sdk";

        // override parent properties
        enablePostProcessFile = true;
        packageName = "";


        inlineSchemaOption.put("SKIP_SCHEMA_REUSE", "true");
    }

    @Override
    public void preprocessOpenAPI(OpenAPI openAPI) {
        super.preprocessOpenAPI(openAPI);

        // parse and update operations and models
        schemaService = new SchemaServiceImpl(openAPI);
        operationService = new OperationServiceImpl(openAPI, this);

        operationService.parseOperation();
        schemaService.parseSchema();
    }

    @Override
    public String formatParamName(String name) {
        return toParamName(name);
    }

    @Override
    public String formatMethodName(String name) {
        return underscore(sanitizeName(name));
    }

    @Override
    public String formatService(String name) {
        return camelize(name);
    }

    @Override
    public String formatPackage(String name) {
        return name.toLowerCase();
    }

    @Override
    public String modelFileFolder() {
        switch (modeSwitch.getMode()) {
            case ENTRY:
                return outputFolder + File.separator + "service";
            default:
                return outputFolder + File.separator + service + File.separator + formatPackage(subService);
        }
    }

    @Override
    public String escapeReservedWord(String name) {
        if (this.reservedWordsMappings().containsKey(name)) {
            return this.reservedWordsMappings().get(name);
        }

        if (Character.isDigit(name.getBytes()[0])) {
            return "t_" + name;
        }

        return name + "_";
    }

    @Override
    public CodegenProperty fromProperty(String name, Schema p, boolean required) {
        if (p.getType() != null) {
            if (p.getType().equalsIgnoreCase("bigint")) {
                p.setType("integer");
            }

            if (p.getType().equalsIgnoreCase("anytype")) {
                p.setType(null);
            }
        }


        CodegenProperty prop = super.fromProperty(name, p, required);
        String cc = camelize(prop.name, LOWERCASE_FIRST_LETTER);
        if (isReservedWord(cc)) {
            cc = escapeReservedWord(cc);
        }
        prop.nameInCamelCase = cc;

        if (prop.isEnum) {
            List<EnumEntry> enums = new ArrayList<>();

            Map<String, String> builderVars = new HashMap<>();

            List<Map<String, Object>> enumList;
            if (prop.openApiType.equalsIgnoreCase("array")) {
                enumList = (List<Map<String, Object>>) prop.mostInnerItems.vendorExtensions.get("x-api-enum");

                // list[XX.TypeEnum]
                builderVars.put("prefix", "list[");
                builderVars.put("suffix", "." + prop.enumName + "]");

            } else {
                enumList = (List<Map<String, Object>>) prop.vendorExtensions.get("x-api-enum");

                // XX.TypeEnum
                builderVars.put("prefix", "");
                builderVars.put("suffix", "." + prop.datatypeWithEnum);
            }


            List<String> names = new ArrayList<>();
            List<String> values = new ArrayList<>();
            List<String> description = new ArrayList<>();

            enumList.forEach(e -> {
                Object enumValueOriginal = e.get("value");

                String enumValueNameGauss;
                if (enumValueOriginal instanceof Integer) {
                    enumValueNameGauss = "_" + e.get("value");
                } else if (enumValueOriginal instanceof String) {
                    enumValueNameGauss = enumValueOriginal.toString();
                } else {
                    throw new IllegalArgumentException("unknown enum value type..." + e.get("value"));
                }

                String enumName = (String) e.get("name");
                if (StringUtils.isEmpty(enumName)) {
                    enumName = enumValueNameGauss;
                }

                enumName = toVarName(enumName).toUpperCase();
                String enumValue = toEnumValue(enumValueOriginal.toString().trim(), typeMapping.get(p.getType()));

                names.add(enumName);
                values.add(enumValueOriginal.toString().trim());
                description.add(e.get("description").toString());

                enums.add(new EnumEntry(enumName, enumValue, enumValueOriginal, (String) e.get("description"), enumValueOriginal instanceof String));
            });

            // update internal enum support
            prop._enum = values;
            prop.allowableValues.put("values", values);
            prop.vendorExtensions.put("x-enum-varnames", names);
            prop.vendorExtensions.put("x-enum-descriptions", description);
            prop.vendorExtensions.put("x-enum-builder-vars", builderVars);

            prop.vendorExtensions.put("x-enums", enums);
        }

        return prop;
    }

    @Override
    public String toEnumName(CodegenProperty property) {
        return formatService(property.name + "Enum");
    }

    @Override
    public String toEnumDefaultValue(CodegenProperty property, String value) {
        // Use the datatype with the value.
        return toEnumDefaultValue(value, property.datatypeWithEnum);
    }

    @Override
    public String toModelName(String name) {
        return super.toModelName(schemaService.getGeneratedModelName(name));
    }

    @Override
    public String toApiName(String name) {
        return camelize(name + "_" + (modeSwitch.isWs() ? "WS" : "API"));
    }

    @Override
    public String toModelFilename(String name) {
        name = schemaService.getGeneratedModelName(name);
        name = "model_" + name;
        name = underscore(dropDots(name));
        return name;
    }

    @Override
    public String toApiFilename(String name) {
        String apiName = name.replaceAll("-", "_");
        switch (modeSwitch.getMode()) {
            case WS: {
                apiName = "ws_" + underscore(apiName);
                break;
            }
            case API:
            case ENTRY:
            case TEST_TEMPLATE: {
                apiName = "api_" + underscore(apiName);
                break;
            }
            case TEST:
            case WS_TEST: {
                apiName = "api_" + underscore(apiName) + "_test";
                break;
            }
        }

        return apiName;
    }

    @Override
    public String modelFilename(String templateName, String name) {
        String suffix = modelTemplateFiles().get(templateName);
        return modelFileFolder() + File.separator + toModelFilename(name) + suffix;
    }

    @Override
    public String apiFilename(String templateName, String tag) {
        String suffix = apiTemplateFiles().get(templateName);
        if (modeSwitch.isEntry()) {
            String entryType = service + "_api";
            return modelFileFolder() + File.separator + entryType + suffix;
        }
        return modelFileFolder() + File.separator + toApiFilename(tag) + suffix;
    }


    private String generateImport(String _package, String fileName, String... target) {
        return String.format("from %s.%s import %s", packageName, fileName, String.join(",", target));
    }

    private String generateImportSimple(String from, String... target) {
        return String.format("from %s import %s", from, String.join(",", target));
    }

    private Set<String> generateApiImport(Meta meta, boolean req) {
        Set<String> imports = new HashSet<>();
        switch (modeSwitch.getMode()) {
            case API:
            case TEST: {
                String suffix = "resp";
                if (req) {
                    suffix = "req";
                }
                imports.add(generateImport(meta.getService().toLowerCase(),
                        toModelFilename(meta.getMethod()) + "_" + suffix,
                        formatService(meta.getMethod() + camelize(suffix))));
                break;
            }
            case WS: {
                String suffix = "event";
                imports.add(generateImport(meta.getService().toLowerCase(),
                        toModelFilename(meta.getMethod()) + "_" + suffix,
                        formatService(meta.getMethod() + camelize(suffix + "Callback")),
                        formatService(meta.getMethod() + camelize(suffix + "CallbackWrapper"))));

                if ((((Map<String, Object>) meta.getOtherProperties().getParas().getType()).containsKey("array"))) {
                    imports.add(generateImportSimple("typing", "List"));
                }

                break;

            }
            case WS_TEST: {
                String suffix = "event";
                imports.add(generateImport(meta.getService().toLowerCase(),
                        toModelFilename(meta.getMethod()) + "_" + suffix,
                        formatService(meta.getMethod() + camelize(suffix))));

                if ((((Map<String, Object>) meta.getOtherProperties().getParas().getType()).containsKey("array"))) {
                    imports.add(generateImportSimple("typing", "List"));
                }

                break;
            }
            case ENTRY: {
                operationService.getServiceMeta().forEach((k, v) -> {
                    if (v.getService().equalsIgnoreCase(meta.getService())) {
                        imports.add(
                                generateImportSimple(String.format("kucoin_universal_sdk.generate.%s.%s.%s", formatPackage(v.getService()),
                                                formatPackage(v.getSubService()), toApiFilename(formatMethodName(k))),
                                        formatService(k + "API"),
                                        formatService(k + "APIImpl")));
                    }
                });
                break;
            }
            default: {
                throw new RuntimeException("unsupported mode");
            }
        }
        return imports;
    }

    @Override
    public CodegenOperation fromOperation(String path, String httpMethod, Operation operation, List<Server> servers) {
        CodegenOperation result = super.fromOperation(path, httpMethod, operation, servers);
        if (httpMethod.equalsIgnoreCase("patch")) {
            result.httpMethod = (String) operation.getExtensions().get("x-original-method");
        }
        return result;
    }

    @Override
    public OperationsMap postProcessOperationsWithModels(OperationsMap objs, List<ModelMap> allModels) {
        objs = super.postProcessOperationsWithModels(objs, allModels);

        OperationMap operationMap = objs.getOperations();

        Set<String> modelImport = new TreeSet<>();

        for (CodegenOperation op : operationMap.getOperation()) {
            Meta meta = SpecificationUtil.getMeta(op.vendorExtensions);
            if (meta != null) {
                switch (modeSwitch.getMode()) {
                    case ENTRY: {
                        // api entry
                        List<Map<String, String>> entryValue = new ArrayList<>();
                        operationService.getServiceMeta().forEach((k, v) -> {
                            if (v.getService().equalsIgnoreCase(meta.getService())) {
                                Map<String, String> kv = new HashMap<>();
                                kv.put("method", formatMethodName(k));
                                kv.put("target_service", formatService(k + "API"));
                                entryValue.add(kv);
                            }
                        });
                        Map<String, Object> apiEntryInfo = new HashMap<>();
                        apiEntryInfo.put("api_entry_name", formatService(meta.getService() + "Service"));
                        apiEntryInfo.put("api_entry_value", entryValue);
                        objs.put("api_entry", apiEntryInfo);
                        entryValue.forEach(m -> {
                            modelImport.addAll(generateApiImport(meta, false));
                        });
                        break;
                    }

                    case API:
                    case TEST: {
                        allModels.forEach(m-> {
                            String importName =  (String)m.get("importPath");
                            String fileName = m.getModel().getClassFilename();
                            // from .model_get_part_order_book_req import GetPartOrderBookReqBuilder
                            exports.add(String.format("from .%s import %s", fileName, importName));
                            if (m.getModel().getVendorExtensions().containsKey("x-request-model")) {
                                exports.add(String.format("from .%s import %s", fileName, importName+"Builder"));
                            }
                        });
                        exports.add(String.format("from .%s import %sAPI", toApiFilename(meta.getSubService()), formatService(meta.getSubService())));


                        if (op.hasParams) {
                            modelImport.addAll(generateApiImport(meta, true));
                        }
                        modelImport.addAll(generateApiImport(meta, false));

                        if (op.isDeprecated) {
                            modelImport.add(generateImportSimple("typing_extensions", "deprecated"));
                        }

                        break;
                    }
                    case WS:
                    case WS_TEST: {
                        allModels.forEach(m-> {
                            String importName =  (String)m.get("importPath");
                            String fileName = m.getModel().getClassFilename();
                            // from .model_get_part_order_book_req import GetPartOrderBookReqBuilder
                            exports.add(String.format("from .%s import %s", fileName, importName));
                        });
                        exports.add(String.format("from .%s import %sWS", toApiFilename(meta.getSubService()), formatService(meta.getSubService())));

                        modelImport.addAll(generateApiImport(meta, false));
                        break;
                    }
                    case TEST_TEMPLATE: {
                        String reqName = meta.getMethodServiceFmt().toLowerCase() + "Req";
                        allModels.stream().filter(m -> reqName.equalsIgnoreCase((String) m.get("importPath"))).
                                forEach(m -> op.vendorExtensions.put("x-request-model", m.getModel()));
                        break;
                    }
                }
            }
        }

        objs.put("imports", modelImport);

        return objs;
    }

    @Override
    public ModelsMap postProcessModels(ModelsMap objs) {
        objs = super.postProcessModels(objs);

        List<ModelMap> models = objs.getModels();
        if (models != null) {
            for (ModelMap model : models) {
                CodegenModel codegenModel = model.getModel();
                if (codegenModel != null) {

                    List<CodegenProperty> codegenProperties = null;
                    if (!codegenModel.oneOf.isEmpty()) {
                        codegenProperties = codegenModel.getComposedSchemas().getOneOf();
                    } else if (!codegenModel.anyOf.isEmpty()) {
                        codegenProperties = codegenModel.getComposedSchemas().getAnyOf();
                    } else {
                        // typical model
                        codegenProperties = codegenModel.vars;
                    }

                    codegenProperties.forEach(c -> {
                        c.required = false;
                    });


                    Meta meta = schemaService.getMeta(codegenModel.getName());
                    if (meta != null) {
                        objs.put(CodegenConstants.PACKAGE_NAME, formatPackage(meta.getService()));
                    }
                }
            }
        }
        return objs;
    }


    @Override
    public Map<String, ModelsMap> postProcessAllModels(Map<String, ModelsMap> objs) {
        final Map<String, ModelsMap> processed = super.postProcessAllModels(objs);

        for (Map.Entry<String, ModelsMap> entry : objs.entrySet()) {
            // create hash map of codegen model
            CodegenModel cm = ModelUtils.getModelByName(entry.getKey(), objs);
            codegenModelMap.put(cm.classname, ModelUtils.getModelByName(entry.getKey(), objs));
        }

        // create circular import
        for (String m : codegenModelMap.keySet()) {
            createImportMapOfSet(m, codegenModelMap);
        }

        for (Map.Entry<String, ModelsMap> entry : processed.entrySet()) {
            entry.setValue(postProcessModelsMap(entry.getValue()));
        }

        return processed;
    }

    /**
     * Update circularImports with the model name (key) and its imports gathered recursively
     *
     * @param modelName       model name
     * @param codegenModelMap a map of CodegenModel
     */
    void createImportMapOfSet(String modelName, Map<String, CodegenModel> codegenModelMap) {
        HashSet<String> imports = new HashSet<>();
        circularImports.put(modelName, imports);

        CodegenModel cm = codegenModelMap.get(modelName);

        if (cm == null) {
            log.warn("Failed to lookup model in createImportMapOfSet: " + modelName);
            return;
        }

        for (CodegenProperty cp : cm.vars) {
            String modelNameFromDataType = getModelNameFromDataType(cp);
            if (modelNameFromDataType != null) { // model
                imports.add(modelNameFromDataType); // update import
                // go through properties or sub-schemas of the model recursively to identify more (model) import if any
                updateImportsFromCodegenModel(modelNameFromDataType, codegenModelMap.get(modelNameFromDataType), imports);
            }
        }
    }

    private String getModelNameFromDataType(CodegenProperty cp) {
        if (cp.isArray) {
            return getModelNameFromDataType(cp.items);
        } else if (cp.isMap) {
            return getModelNameFromDataType(cp.items);
        } else if (!cp.isPrimitiveType || cp.isModel) {
            return cp.getDataType();
        } else {
            return null;
        }
    }


    private ModelsMap postProcessModelsMap(ModelsMap objs) {
        // process enum in models
        objs = postProcessModelsEnum(objs);

        TreeSet<String> modelImports = new TreeSet<>();
        TreeSet<String> postponedModelImports = new TreeSet<>();

        for (ModelMap m : objs.getModels()) {
            TreeSet<String> exampleImports = new TreeSet<>();
            TreeSet<String> postponedExampleImports = new TreeSet<>();
            List<String> readOnlyFields = new ArrayList<>();
            hasModelsToImport = false;
            int property_count = 1;

            PythonImports moduleImports = new PythonImports();
            CodegenModel model = m.getModel();

            if (modeSwitch.isWs()) {
                moduleImports.add("typing", "Callable");
            }

            PydanticType pydantic = new PydanticType(
                    modelImports,
                    exampleImports,
                    postponedModelImports,
                    postponedExampleImports,
                    moduleImports,
                    model.classname
            );

            // add Field import for response model
            if (m.getModel().getVendorExtensions() != null) {
                if (m.getModel().getVendorExtensions().containsKey("x-response-model")) {
                    moduleImports.add("pydantic", "Field");
                }
            }

            List<CodegenProperty> codegenProperties = model.vars;

            // if model_generic.mustache is used
            if (model.oneOf.isEmpty() && model.anyOf.isEmpty() && !model.isEnum) {
                moduleImports.add("typing", "ClassVar");
                moduleImports.add("typing", "Dict");
                moduleImports.add("typing", "Any");
                if (this.disallowAdditionalPropertiesIfNotPresent || model.isAdditionalPropertiesTrue) {
                    moduleImports.add("typing", "List");
                }
            }

            // if pydantic model
            if (!model.isEnum) {
                moduleImports.add("pydantic", "ConfigDict");
            }

            //loop through properties/schemas to set up typing, pydantic
            for (CodegenProperty cp : codegenProperties) {
                // is readOnly?
                if (cp.isReadOnly) {
                    readOnlyFields.add(cp.name);
                }

                if (cp.isEnum) {
                    moduleImports.add("enum", "Enum");
                }

                String typing = pydantic.generatePythonType(cp);
                cp.vendorExtensions.put("x-py-typing", typing);
            }

            // add parent model to import
            if (!model.isEnum) {
                moduleImports.add("pydantic", "BaseModel");
            }

            // set enum type in extensions and update `name` in enumVars
            if (model.isEnum) {
                for (Map<String, Object> enumVars : (List<Map<String, Object>>) model.getAllowableValues().get("enumVars")) {
                    if ((Boolean) enumVars.get("isString")) {
                        model.vendorExtensions.putIfAbsent("x-py-enum-type", "str");
                        // update `name`, e.g.
                        enumVars.put("name", toEnumVariableName((String) enumVars.get("value"), "str"));
                    } else {
                        model.vendorExtensions.putIfAbsent("x-py-enum-type", "int");
                        // Do not overwrite the variable name if already set through x-enum-varnames
                        if (model.vendorExtensions.get("x-enum-varnames") == null) {
                            enumVars.put("name", toEnumVariableName((String) enumVars.get("value"), "int"));
                        }
                    }
                }
            }

            // set the extensions if the key is absent
            model.getVendorExtensions().putIfAbsent("x-py-readonly", readOnlyFields);

            // remove the items of postponedModelImports in modelImports to avoid circular imports error
            if (!modelImports.isEmpty() && !postponedModelImports.isEmpty()) {
                modelImports.removeAll(postponedModelImports);
            }

            // import models one by one
            if (!modelImports.isEmpty()) {
                Set<String> modelsToImport = new TreeSet<>();
                for (String modelImport : modelImports) {
                    if (modelImport.equals(model.classname)) {
                        // skip self import
                        continue;
                    }
                    Meta meta = schemaService.getMeta(model.getName());
                    if (meta != null) {
                        modelsToImport.add(generateImport(meta.getService().toLowerCase(), toModelFilename(modelImport), modelImport));
                    }
                }

                if (!modelsToImport.isEmpty()) {

                    model.getVendorExtensions().put("x-py-model-imports", modelsToImport);
                }
            }

            if (!moduleImports.isEmpty()) {
                model.getVendorExtensions().put("x-py-other-imports", moduleImports.exports());
            }


            if (!postponedModelImports.isEmpty()) {
                Set<String> modelsToImport = new TreeSet<>();
                for (String modelImport : postponedModelImports) {
                    if (modelImport.equals(model.classname)) {
                        // skip self import
                        continue;
                    }
                    Meta meta = schemaService.getMeta(model.getName());
                    if (meta != null) {
                        modelsToImport.add(generateImport(meta.getService().toLowerCase(), toModelFilename(modelImport), modelImport));
                    }
                }

                model.getVendorExtensions().putIfAbsent("x-py-postponed-model-imports", modelsToImport);
            }
        }

        return objs;
    }


    @Override
    public Map<String, Object> postProcessSupportingFileData(Map<String, Object> objs) {
        Map<String, Object> data = super.postProcessSupportingFileData(objs);
        data.put("exports", exports);

        if (!modeSwitch.isEntry()) {
            return data;
        }

        String csvPath = (String) additionalProperties.get("CSV_PATH");
        if (csvPath == null) {
            log.error("no csv path found");
            return data;
        }

        String apiCsvFile = csvPath + "/apis.csv";

        Set<String> services = new TreeSet<>();
        try {

            CSVReader reader = new CSVReader(new FileReader(apiCsvFile));
            List<String[]> rows = reader.readAll();
            for (int i = 1; i < rows.size(); i++) {
                services.add(rows.get(i)[0].toLowerCase());
            }
        } catch (Exception e) {
            throw new RuntimeException("read csv fail", e);
        }

        services.forEach(s-> {
            Map<String,String> specialKeywords = Map.of("copytrading", "CopyTrading", "viplending", "VIPLending");
            String service = formatService(specialKeywords.getOrDefault(s, s));
            serviceExports.add(String.format("from .%s_api import %sService", s, service));
        });
        data.put("serviceExports", serviceExports);

        return data;
    }

    private PythonType getPydanticType(CodegenProperty cp,
                                       Set<String> modelImports,
                                       Set<String> exampleImports,
                                       Set<String> postponedModelImports,
                                       Set<String> postponedExampleImports,
                                       PythonImports moduleImports,
                                       String classname) {
        PydanticType pt = new PydanticType(
                modelImports,
                exampleImports,
                postponedModelImports,
                postponedExampleImports,
                moduleImports,
                classname
        );

        return pt.getType(cp);
    }

    /* Track the list of resources to imports from where.
     *
     * PythonImports are tracked as a set of modules to import from, and actual
     * resources (classes, functions, etc.) to import.
     *
     * The same resource can be safely "imported" many times from the same
     * module; during the rendering of the actual Python imports, duplicated
     * entries will be automatically removed.
     *
     * */
    class PythonImports {
        private Map<String, Set<String>> imports;

        public PythonImports() {
            imports = new HashMap<>();
        }

        /* Add a new import:
         *
         *      from $from import $what
         *
         */
        private void add(String from, String what) {
            // Fetch the set of all the objects already imported from `from` (if any).
            Set<String> allImportsFrom = imports.get(from);
            if (allImportsFrom == null) {
                allImportsFrom = new TreeSet<>();
            }
            // Just one more thing to import from `from`.
            allImportsFrom.add(what);
            imports.put(from, allImportsFrom);
        }

        /* Export a list of import statements as:
         *
         *      from $from import $what
         *
         */
        public Set<String> exports() {
            Set<String> results = new TreeSet<>();

            for (Map.Entry<String, Set<String>> entry : imports.entrySet()) {
                String importLine = String.format(
                        Locale.ROOT, "from %s import %s",
                        entry.getKey(), StringUtils.join(entry.getValue(), ", "));
                results.add(importLine);
            }

            return results;
        }

        public boolean isEmpty() {
            return imports.isEmpty();
        }
    }


    /* The definition for a Python type.
     *
     * This encapsulate all the type definition: the actual type, and potentially:
     *
     * * its type parameters if the actual type is a generic type
     * * the additional constraints on the type
     * * the additional annotations to give extra information about the type (description, alias, etc.)
     * * a default value for the variable associated with the type
     */
    class PythonType {
        private String type;
        private List<PythonType> typeParams;
        private Map<String, Object> annotations;
        private Map<String, Object> constraints;

        private String defaultValue;

        public PythonType() {
            this(null);
        }

        public PythonType(String type) {
            this.setType(type);
            this.defaultValue = null;
            this.typeParams = new ArrayList<>();
            this.annotations = new HashMap<>();
            this.constraints = new HashMap<>();
        }

        public PythonType setType(String type) {
            this.type = type;
            return this;
        }

        public PythonType setDefaultValue(boolean value) {
            if (value) {
                defaultValue = "True";
            } else {
                defaultValue = "False";
            }
            return this;
        }

        public PythonType setDefaultValue(Object value) {
            defaultValue = value.toString();
            return this;
        }

        public PythonType constrain(String name, String value) {
            return constrain(name, value, true);
        }

        public PythonType constrain(String name, String value, boolean quote) {
            if (quote) {
                // TODO:jon proper quoting
                value = "\"" + value + "\"";
            }
            constraints.put(name, value);
            return this;
        }

        public PythonType constrain(String name, boolean value) {
            if (value) {
                constraints.put(name, "True");
            } else {
                constraints.put(name, "False");
            }
            return this;
        }

        public PythonType constrain(String name, Object value) {
            constraints.put(name, value);
            return this;
        }

        /* Annotate a field with extra information.
         *
         * Annotation are made to add extra information about a field.
         *
         * If the information you need to add is a type constraint (to make the
         * type more specific), use `constrain` instead.
         */
        public PythonType annotate(String name, String value) {
            return annotate(name, value, true);
        }

        public PythonType annotate(String name, String value, boolean quote) {
            if (quote) {
                // TODO:jon proper quoting
                value = "\"" + value + "\"";
            }
            annotations.put(name, value);
            return this;
        }

        public PythonType annotate(String name, boolean value) {
            if (value) {
                annotations.put(name, "True");
            } else {
                annotations.put(name, "False");
            }
            return this;
        }

        public PythonType annotate(String name, Object value) {
            annotations.put(name, value);
            return this;
        }

        /* A "type param" is the parameter to a generic type (the `str` in `list[str]`).
         *
         * A Python type can have multiple type parameters: it assumes the
         * Python type on which the type parameter is added to is a generic
         * type.
         *
         * Type parameters can be as simple or as complex as needed. They are just
         * another list of `PythonType`.
         */
        public PythonType addTypeParam(PythonType typeParam) {
            this.typeParams.add(typeParam);
            return this;
        }

        /* The left-hand side of:
         *
         *      my_field: TypeConstraint = TypeAnnotations
         *
         *  A "type constraint" is a Python / Pydantic type, potentially
         *  annotated with extra constraints, such as "less than", "maximum
         *  number of items", etc.
         *
         *  The Python / Pydantic type can be as expressive as needed:
         *
         *  - it could simply be `str`
         *  - or something more complex like `Optional[List[Dict[str, List[int]]]]`.
         *
         *  Note that the default value (if available) and/or the metadata about
         *  the field / variable being defined are *not* part of the
         *  constraints but part of the "type value".
         */
        public String asTypeConstraint(PythonImports imports) {
            return asTypeConstraint(imports, false);
        }

        /* Generate the Python type, constraints + annotations
         *
         * This should be mostly used to build the type definition for a
         * function/method parameter, such as :
         *
         *      def f(my_param: TypeConstrainWithAnnotations):
         *          ...
         *
         *  Note that the default value is not managed here, but directly in
         *  the Mustache template.
         */
        public String asTypeConstraintWithAnnotations(PythonImports imports) {
            return asTypeConstraint(imports, true);
        }

        private String asTypeConstraint(PythonImports imports, boolean withAnnotations) {
            String typeParam = "";
            if (this.typeParams.size() > 0) {
                List<String> types = new ArrayList<>();
                for (PythonType t : this.typeParams) {
                    types.add(t.asTypeConstraint(imports));
                }
                typeParam = "[" + StringUtils.join(types, ", ") + "]";
            }

            String currentType = this.type + typeParam;


            // Build the parameters for the `Field`, possibly associated with
            // the type definition.
            // There can be no constraints nor annotations, in which case we
            // simply won't build a Field object.
            List<String> fieldParams = new ArrayList<>();
            for (Map.Entry<String, Object> entry : this.constraints.entrySet()) {
                String ans = entry.getKey() + "=";
                ans += entry.getValue().toString();
                fieldParams.add(ans);
            }

            if (withAnnotations) {
                for (Map.Entry<String, Object> entry : this.annotations.entrySet()) {
                    String ans = entry.getKey() + "=";
                    ans += entry.getValue().toString();
                    fieldParams.add(ans);
                }
            }

            if (fieldParams.size() > 0) {
                imports.add("pydantic", "Field");
                imports.add("typing_extensions", "Annotated");
                currentType = "Annotated[" + currentType + ", Field(" + StringUtils.join(fieldParams, ", ") + ")]";
            }

            return currentType;
        }

        /* The right-hand side of:
         *
         *      my_field: TypeConstraint = TypeValue
         *
         *  A "type value" is either:
         *
         *  * The default value of a field, if no other information is available
         *  * A Pydantic `Field`, containing potentially the default value,
         *    plus all the extra metadata that defines what a field is (description, alias, etc.).
         *
         *  Constraints on the type are *not* part of the "type value", but are part of the "type constraints".
         */
        @Nullable
        public String asTypeValue(PythonImports imports) {
            String defaultValue = this.defaultValue;

            if (this.annotations.size() > 0) {
                String typeValue = "";

                List<String> ants = new ArrayList<>();

                if (defaultValue != null) {
                    // Keep the default value first, if possible.
                    ants.add("default=" + defaultValue);
                }

                for (Map.Entry<String, Object> entry : this.annotations.entrySet()) {
                    String ans = entry.getKey() + "=";
                    ans += entry.getValue().toString();
                    ants.add(ans);
                }

                imports.add("pydantic", "Field");
                typeValue = "Field(" + StringUtils.join(ants, ", ") + ")";
                return typeValue;
            }

            return defaultValue;
        }
    }


    class PydanticType {
        private Set<String> modelImports;
        private Set<String> exampleImports;
        private Set<String> postponedModelImports;
        private Set<String> postponedExampleImports;
        private PythonImports moduleImports;
        private String classname;

        public PydanticType(
                Set<String> modelImports,
                Set<String> exampleImports,
                Set<String> postponedModelImports,
                Set<String> postponedExampleImports,
                PythonImports moduleImports,
                String classname
        ) {
            this.modelImports = modelImports;
            this.exampleImports = exampleImports;
            this.postponedModelImports = postponedModelImports;
            this.postponedExampleImports = postponedExampleImports;
            this.moduleImports = moduleImports;
            this.classname = classname;
        }

        private PythonType arrayType(IJsonSchemaValidationProperties cp) {
            PythonType pt = new PythonType();
            if (cp.getMaxItems() != null) {
                pt.constrain("max_length", cp.getMaxItems());
            }
            if (cp.getMinItems() != null) {
                pt.constrain("min_length", cp.getMinItems());
            }
            if (cp.getUniqueItems()) {
                // A unique "array" is a set
                // TODO: pydantic v2: Pydantic suggest to convert this to a set, but this has some implications:
                // https://github.com/pydantic/pydantic-core/issues/296
                // Also, having a set instead of list creates complications:
                // random JSON serialization order, unable to easily serialize
                // to JSON, etc.
                //pt.setType("Set");
                //moduleImports.add("typing", "Set");
                pt.setType("List");
                moduleImports.add("typing", "List");
            } else {
                pt.setType("List");
                moduleImports.add("typing", "List");
            }
            pt.addTypeParam(collectionItemType(cp.getItems()));
            return pt;
        }

        private PythonType collectionItemType(CodegenProperty itemCp) {
            PythonType itemPt = getType(itemCp);
            if (itemCp != null && !itemPt.type.equals("Any") && itemCp.isNullable) {
                moduleImports.add("typing", "Optional");
                PythonType opt = new PythonType("Optional");
                opt.addTypeParam(itemPt);
                itemPt = opt;
            }
            return itemPt;
        }

        private PythonType stringType(IJsonSchemaValidationProperties cp) {
            return new PythonType("str");
        }

        private PythonType mapType(IJsonSchemaValidationProperties cp) {
            moduleImports.add("typing", "Dict");
            PythonType pt = new PythonType("Dict");
            pt.addTypeParam(new PythonType("str"));
            pt.addTypeParam(collectionItemType(cp.getItems()));
            return pt;
        }

        private PythonType numberType(IJsonSchemaValidationProperties cp) {
            if (cp.getHasValidation()) {
                PythonType floatt = new PythonType("float");

                // e.g. confloat(ge=10, le=100, strict=True)
                if (cp.getMaximum() != null) {
                    if (cp.getExclusiveMaximum()) {
                        floatt.constrain("lt", cp.getMaximum(), false);
                    } else {
                        floatt.constrain("le", cp.getMaximum(), false);
                    }
                }
                if (cp.getMinimum() != null) {
                    if (cp.getExclusiveMinimum()) {
                        floatt.constrain("gt", cp.getMinimum(), false);
                    } else {
                        floatt.constrain("ge", cp.getMinimum(), false);
                    }
                }
                if (cp.getMultipleOf() != null) {
                    floatt.constrain("multiple_of", cp.getMultipleOf());
                }

                return floatt;
            } else {
                return new PythonType("float");
            }
        }

        private PythonType intType(IJsonSchemaValidationProperties cp) {
            if (cp.getHasValidation()) {
                PythonType pt = new PythonType("int");
                // e.g. conint(ge=10, le=100, strict=True)
                pt.constrain("strict", true);
                if (cp.getMaximum() != null) {
                    if (cp.getExclusiveMaximum()) {
                        pt.constrain("lt", cp.getMaximum(), false);
                    } else {
                        pt.constrain("le", cp.getMaximum(), false);
                    }
                }
                if (cp.getMinimum() != null) {
                    if (cp.getExclusiveMinimum()) {
                        pt.constrain("gt", cp.getMinimum(), false);
                    } else {
                        pt.constrain("ge", cp.getMinimum(), false);
                    }
                }
                if (cp.getMultipleOf() != null) {
                    pt.constrain("multiple_of", cp.getMultipleOf());
                }
                return pt;
            } else {
                return new PythonType("int");
            }
        }

        private PythonType binaryType(IJsonSchemaValidationProperties cp) {
            if (cp.getHasValidation()) {
                PythonType bytest = new PythonType("bytes");
                PythonType strt = new PythonType("str");

                // e.g. conbytes(min_length=2, max_length=10)
                bytest.constrain("strict", true);
                strt.constrain("strict", true);
                if (cp.getMaxLength() != null) {
                    bytest.constrain("max_length", cp.getMaxLength());
                    strt.constrain("max_length", cp.getMaxLength());
                }
                if (cp.getMinLength() != null) {
                    bytest.constrain("min_length", cp.getMinLength());
                    strt.constrain("min_length", cp.getMinLength());
                }
                if (cp.getPattern() != null) {
                    moduleImports.add("pydantic", "field_validator");
                    // use validator instead as regex doesn't support flags, e.g. IGNORECASE
                    //fieldCustomization.add(Locale.ROOT, String.format(Locale.ROOT, "regex=r'%s'", cp.getPattern()));
                }

                moduleImports.add("typing", "Union");
                PythonType pt = new PythonType("Union");
                pt.addTypeParam(bytest);
                pt.addTypeParam(strt);
                return pt;
            } else {
                // same as above which has validation
                moduleImports.add("pydantic", "StrictBytes");
                moduleImports.add("pydantic", "StrictStr");
                moduleImports.add("typing", "Union");

                PythonType pt = new PythonType("Union");
                pt.addTypeParam(new PythonType("StrictBytes"));
                pt.addTypeParam(new PythonType("StrictStr"));
                return pt;
            }
        }

        private PythonType boolType(IJsonSchemaValidationProperties cp) {
            return new PythonType("bool");
        }

        private PythonType decimalType(IJsonSchemaValidationProperties cp) {
            PythonType pt = new PythonType("Decimal");
            moduleImports.add("decimal", "Decimal");

            if (cp.getHasValidation()) {
                // e.g. condecimal(ge=10, le=100, strict=True)
                pt.constrain("strict", true);
                if (cp.getMaximum() != null) {
                    if (cp.getExclusiveMaximum()) {
                        pt.constrain("gt", cp.getMaximum(), false);
                    } else {
                        pt.constrain("ge", cp.getMaximum(), false);
                    }
                }
                if (cp.getMinimum() != null) {
                    if (cp.getExclusiveMinimum()) {
                        pt.constrain("lt", cp.getMinimum(), false);
                    } else {
                        pt.constrain("le", cp.getMinimum(), false);
                    }
                }
                if (cp.getMultipleOf() != null) {
                    pt.constrain("multiple_of", cp.getMultipleOf());
                }
            }

            return pt;
        }

        private PythonType anyType(IJsonSchemaValidationProperties cp) {
            moduleImports.add("typing", "Any");
            return new PythonType("Any");
        }

        private PythonType dateType(IJsonSchemaValidationProperties cp) {
            if (cp.getIsDate()) {
                moduleImports.add("datetime", "date");
            }
            if (cp.getIsDateTime()) {
                moduleImports.add("datetime", "datetime");
            }

            return new PythonType(cp.getDataType());
        }

        private PythonType uuidType(IJsonSchemaValidationProperties cp) {
            return new PythonType(cp.getDataType());
        }

        private PythonType fromCommon(CodegenProperty cp) {
            if (cp == null) {
                // if codegen property (e.g. map/dict of undefined type) is null, default to string
                log.warn("Codegen property is null (e.g. map/dict of undefined type). Default to typing.Any.");
                moduleImports.add("typing", "Any");
                return new PythonType("Any");
            }

            if (cp.getIsArray()) {
                return arrayType(cp);
            } else if (cp.getIsMap() || cp.getIsFreeFormObject()) {
                return mapType(cp);
            } else if (cp.getIsEnum()) {
                return new PythonType(cp.datatypeWithEnum);
            } else if (cp.getIsString()) {
                return stringType(cp);
            } else if (cp.getIsNumber() || cp.getIsFloat() || cp.getIsDouble()) {
                return numberType(cp);
            } else if (cp.getIsInteger() || cp.getIsLong() || cp.getIsShort() || cp.getIsUnboundedInteger()) {
                return intType(cp);
            } else if (cp.getIsBinary() || cp.getIsByteArray()) {
                return binaryType(cp);
            } else if (cp.getIsBoolean()) {
                return boolType(cp);
            } else if (cp.getIsDecimal()) {
                return decimalType(cp);
            } else if (cp.getIsAnyType()) {
                return anyType(cp);
            } else if (cp.getIsDate() || cp.getIsDateTime()) {
                return dateType(cp);
            } else if (cp.getIsUuid()) {
                return uuidType(cp);
            }

            return null;
        }

        public String generatePythonType(CodegenProperty cp) {
            PythonType pt = this.getType(cp);
            return this.finalizeType(cp, pt);
        }

        private PythonType getType(CodegenProperty cp) {
            PythonType result = fromCommon(cp);

            /* comment out the following since Literal requires python 3.8
               also need to put cp.isEnum check after isArray, isMap check
            if (cp.isEnum) {
                // use Literal for inline enum
                moduleImports.add("typing", "Literal");
                List<String> values = new ArrayList<>();
                List<Map<String, Object>> enumVars = (List<Map<String, Object>>) cp.allowableValues.get("enumVars");
                if (enumVars != null) {
                    for (Map<String, Object> enumVar : enumVars) {
                        values.add((String) enumVar.get("value"));
                    }
                }
                return String.format(Locale.ROOT, "%sEnum", cp.nameInPascalCase);
            } else*/

            if (result == null) {
                // TODO: Cleanup
                if (!cp.isPrimitiveType || cp.isModel) { // model
                    // skip import if it's a circular reference
                    if (classname == null) {
                        // for parameter model, import directly
                        hasModelsToImport = true;
                        modelImports.add(cp.getDataType());
                        exampleImports.add(cp.getDataType());
                    } else {
                        if (circularImports.containsKey(cp.getDataType())) {
                            if (circularImports.get(cp.getDataType()).contains(classname)) {
                                hasModelsToImport = true;
                                postponedModelImports.add(cp.getDataType());
                                postponedExampleImports.add(cp.getDataType());
                                // cp.getDataType() import map of set contains this model (classname), don't import
                                log.debug("Skipped importing {} in {} due to circular import.", cp.getDataType(), classname);
                            } else {
                                // not circular import, so ok to import it
                                hasModelsToImport = true;
                                modelImports.add(cp.getDataType());
                                exampleImports.add(cp.getDataType());
                            }
                        } else {
                            log.error("Failed to look up {} from the imports (map of set) of models.", cp.getDataType());
                        }
                    }
                    result = new PythonType(cp.getDataType());
                } else {
                    throw new RuntimeException("Error! Codegen Property not yet supported in getPydanticType: " + cp);
                }
            }

            return result;
        }

        private String finalizeType(CodegenProperty cp, PythonType pt) {
            if (!cp.required || cp.isNullable) {
                moduleImports.add("typing", "Optional");
                PythonType opt = new PythonType("Optional");
                opt.addTypeParam(pt);
                pt = opt;
            }

            if (!StringUtils.isEmpty(cp.description)) { // has description
                pt.annotate("description", cp.description);
            }

            // field
            if (cp.baseName != null && !cp.baseName.equals(cp.name)) { // base name not the same as name
                pt.annotate("alias", cp.baseName);
            }

            if (cp.vendorExtensions.containsKey("x-tag-path")) {
                pt.annotate("path_variable", "True");
            }

            /* TODO review as example may break the build
            if (!StringUtils.isEmpty(cp.getExample())) { // has example
                fields.add(String.format(Locale.ROOT, "example=%s", cp.getExample()));
            }*/

            //String defaultValue = null;
            if (!cp.required) { //optional
                if (cp.defaultValue == null) {
                    pt.setDefaultValue("None");
                } else {
                    if (cp.isArray || cp.isMap) {
                        // TODO handle default value for array/map
                        pt.setDefaultValue("None");
                    } else {
                        pt.setDefaultValue(cp.defaultValue);
                    }
                }
            }

            String typeConstraint = pt.asTypeConstraint(moduleImports);
            String typeValue = pt.asTypeValue(moduleImports);

            if (typeValue == null) {
                return typeConstraint;
            } else {
                return typeConstraint + " = " + typeValue;
            }
        }
    }

}
