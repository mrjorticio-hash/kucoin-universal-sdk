package com.kucoin.universal.sdk.plugin.generator;

import com.kucoin.universal.sdk.plugin.model.Meta;
import com.kucoin.universal.sdk.plugin.model.ModeSwitch;
import com.kucoin.universal.sdk.plugin.model.TopicMeta;
import com.kucoin.universal.sdk.plugin.service.NameService;
import com.kucoin.universal.sdk.plugin.service.OperationService;
import com.kucoin.universal.sdk.plugin.service.SchemaService;
import com.kucoin.universal.sdk.plugin.service.impl.OperationServiceImpl;
import com.kucoin.universal.sdk.plugin.service.impl.SchemaServiceImpl;
import com.kucoin.universal.sdk.plugin.util.SpecificationUtil;
import io.swagger.v3.oas.models.OpenAPI;
import io.swagger.v3.oas.models.Operation;
import io.swagger.v3.oas.models.media.Schema;
import io.swagger.v3.oas.models.servers.Server;
import lombok.extern.slf4j.Slf4j;
import org.openapitools.codegen.*;
import org.openapitools.codegen.languages.AbstractGoCodegen;
import org.openapitools.codegen.model.ModelMap;
import org.openapitools.codegen.model.ModelsMap;
import org.openapitools.codegen.model.OperationMap;
import org.openapitools.codegen.model.OperationsMap;
import org.openapitools.codegen.utils.ModelUtils;

import java.io.File;
import java.util.*;

import static org.openapitools.codegen.utils.CamelizeOption.LOWERCASE_FIRST_LETTER;
import static org.openapitools.codegen.utils.StringUtils.camelize;
import static org.openapitools.codegen.utils.StringUtils.underscore;

/**
 * @author isaac.tang
 */
@Slf4j
public class GolangSdkGenerator extends AbstractGoCodegen implements NameService {
    private SchemaService schemaService;
    private OperationService operationService;
    private ModeSwitch modeSwitch;

    private String service;
    private String subService;

    public CodegenType getTag() {
        return CodegenType.OTHER;
    }

    public String getName() {
        return "golang-sdk";
    }

    public String getHelp() {
        return "Generates a golang-sdk library.";
    }

    public GolangSdkGenerator() {
        super();
        cliOptions.add(ModeSwitch.option);
        typeMapping.put("bigint", "int64");
        typeMapping.put("number", "float64");
    }

    @Override
    public void processOpts() {
        super.processOpts();
        modeSwitch = new ModeSwitch(additionalProperties);

        switch (modeSwitch.getMode()) {
            case API: {
                modelTemplateFiles.put("model.mustache", ".go");
                apiTemplateFiles.put("api.mustache", ".go");
                break;
            }
            case TEST: {
                apiTemplateFiles.put("api_test.mustache", ".go");
                break;
            }
            case TEST_TEMPLATE: {
                apiTemplateFiles.put("api_test_template.mustache", ".template");
                break;
            }
            case ENTRY: {
                apiTemplateFiles.put("api_entry.mustache", ".go");
                break;
            }
            case WS: {
                modelTemplateFiles.put("model_ws.mustache", ".go");
                apiTemplateFiles.put("api_ws.mustache", ".go");
                additionalProperties.put("WS_MODE", "true");
                break;
            }
            case WS_TEST: {
                apiTemplateFiles.put("api_ws_test.mustache", ".go");
                break;
            }
            default:
                throw new RuntimeException("unsupported mode");
        }

        supportingFiles.add(new SupportingFile("version.mustache", "version.go"));

        templateDir = "golang-sdk";

        // override parent properties
        generateMarshalJSON = false;
        generateUnmarshalJSON = false;
        enablePostProcessFile = true;

        service = openAPI.getInfo().getTitle();
        subService = openAPI.getInfo().getDescription();

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
        return camelize(name);
    }

    @Override
    public String formatService(String name) {
        return camelize(name);
    }

    @Override
    public String formatPackage(String name) {
        return formatService(name).toLowerCase();
    }

    @Override
    public CodegenProperty fromProperty(String name, Schema p, boolean required) {
        CodegenProperty prop = super.fromProperty(name, p, required);
        String cc = camelize(prop.name, LOWERCASE_FIRST_LETTER);
        if (isReservedWord(cc)) {
            cc = escapeReservedWord(cc);
        }
        prop.nameInCamelCase = cc;
        return prop;
    }

    public boolean isDataTypeString(String dataType) {
        return "string".equals(dataType);
    }

    @Override
    public String toEnumDefaultValue(String value, String datatype) {
        return "\"" + value + "\"";
    }

    @Override
    public String toEnumDefaultValue(CodegenProperty property, String value) {
        if (property.isString) {
            return "\"" + property.getDefaultValue() + "\"";
        }

        return property.defaultValue;
    }

    @Override
    public String toDefaultValue(Schema p) {
        p = ModelUtils.getReferencedSchema(this.openAPI, p);
        if (ModelUtils.isStringSchema(p)) {
            Object defaultObj = p.getDefault();
            if (defaultObj != null) {
                return "\"" + escapeText(String.valueOf(defaultObj)) + "\"";
            }
            return null;
        }

        return super.toDefaultValue(p);
    }

    @Override
    public String toModelName(String name) {
        return formatService(schemaService.getGeneratedModelName(name));
    }

    @Override
    public String toApiName(String name) {
        return camelize(name + "_" + (modeSwitch.isWs() ? "WS" : "API"));
    }

    @Override
    public String toModelFilename(String name) {
        name = schemaService.getGeneratedModelName(name);
        name = toModel("types_" + name);
        return name;
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
    public String toApiFilename(String name) {
        String apiName;
        String api = name.replaceAll("-", "_");
        api = "api_" + underscore(api);
        apiName = api;

        switch (modeSwitch.getMode()) {
            case TEST:
            case WS_TEST: {
                apiName = apiName + "_test";
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
        Set<String> imports = new HashSet<>();

        switch (modeSwitch.getMode()) {
            case ENTRY: {
                objs.put(CodegenConstants.PACKAGE_NAME, formatPackage("service"));
                Map<String, Object> apiEntryInfo = new HashMap<>();
                List<Map<String, Object>> entries = new ArrayList<>();
                String suffix = "API";
                apiEntryInfo.put("api_entry_name", formatService(service) + "Service");
                apiEntryInfo.put("api_entry_value", entries);
                operationService.getServiceMeta().forEach((k, v) -> {
                    Map<String, Object> entryValue = new HashMap<>();
                    imports.add(String.format("github.com/Kucoin/kucoin-universal-sdk/sdk/golang/pkg/generate/%s/%s",
                            v.getService().toLowerCase(), v.getSubService().toLowerCase()));
                    entryValue.put("api_entry_target_package", v.getSubService().toLowerCase());
                    entryValue.put("api_entry_target_api", formatService(v.getSubService()) + suffix);
                    entries.add(entryValue);
                });
                objs.put("api_entry", apiEntryInfo);
                objs.put("imports", imports);
                break;
            }
            case WS: {
                objs.put(CodegenConstants.PACKAGE_NAME, formatPackage(subService));
                boolean needStringsImport = false;
                for (CodegenOperation op : operationMap.getOperation()) {
                    Meta meta = SpecificationUtil.getMeta(op.vendorExtensions);
                    if (meta != null) {
                        TopicMeta topicMeta = meta.getOtherProperties();
                        if (topicMeta != null && ((Map) (topicMeta.getParas().getType())).containsKey("object")) {
                            needStringsImport = true;
                            break;
                        }
                    }
                }
                objs.put("api_import", needStringsImport);
                break;
            }
            case TEST_TEMPLATE: {
                for (CodegenOperation op : operationMap.getOperation()) {
                    Meta meta = SpecificationUtil.getMeta(op.vendorExtensions);
                    String reqName = meta.getMethod().toLowerCase() + "Req";
                    allModels.stream().filter(m -> reqName.equalsIgnoreCase((String) m.get("importPath"))).
                            forEach(m -> op.vendorExtensions.put("x-request-model", m.getModel()));
                }
                objs.put(CodegenConstants.PACKAGE_NAME, formatPackage(subService));
                break;
            }
            default: {
                objs.put(CodegenConstants.PACKAGE_NAME, formatPackage(subService));
            }
        }
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
                    Meta meta = schemaService.getMeta(codegenModel.getName());
                    if (meta != null) {
                        objs.put(CodegenConstants.PACKAGE_NAME, formatPackage(subService));
                    }
                }
            }
        }
        return objs;
    }
}
