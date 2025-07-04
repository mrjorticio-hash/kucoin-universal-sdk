package com.kucoin.universal.sdk.plugin.service.impl;

import com.github.freva.asciitable.AsciiTable;
import com.github.freva.asciitable.Column;
import com.github.freva.asciitable.HorizontalAlign;
import com.kucoin.universal.sdk.plugin.model.Meta;
import com.kucoin.universal.sdk.plugin.service.NameService;
import com.kucoin.universal.sdk.plugin.service.OperationService;
import com.kucoin.universal.sdk.plugin.util.SpecificationUtil;
import com.kucoin.universal.sdk.plugin.util.TopicUtil;
import io.swagger.v3.oas.models.OpenAPI;
import io.swagger.v3.oas.models.Operation;
import io.swagger.v3.oas.models.PathItem;
import io.swagger.v3.oas.models.Paths;
import lombok.extern.slf4j.Slf4j;
import org.apache.commons.lang3.StringUtils;

import java.util.*;

/**
 * @author isaac.tang
 */
@Slf4j
public class OperationServiceImpl implements OperationService {
    public static String EXTENSION_SDK_SERVICE = "x-sdk-service";
    public static String EXTENSION_SDK_SUB_SERVICE = "x-sdk-sub-service";
    public static String EXTENSION_DOMAIN = "x-domain";
    public static String EXTENSION_SDK_METHOD = "x-sdk-method-name";
    public static String EXTENSION_WS_PRIVATE = "x-sdk-private";
    public static String EXTENSION_WS_TOPIC = "x-topic";
    public static String EXTENSION_EXTRA_COMMENT = "x-extra-comment";
    public static String EXTENSION_EXTRA_COMMENT_KV = "x-extra-comment-kv";
    public static String EXTENSION_DEPRECATED = "x-deprecated";
    public static String BROKER_KEY_NAME = "broker";
    public static String EXTENSION_OVERRIDE_METHOD = "x-original-method";

    private final Map<String, Meta> serviceMeta = new HashMap<>();

    private final OpenAPI openAPI;
    private final NameService nameService;

    public OperationServiceImpl(OpenAPI openAPI, NameService nameService) {
        this.openAPI = openAPI;
        this.nameService = nameService;
    }

    @Override
    public void parseOperation() {
        Paths paths = openAPI.getPaths();
        if (paths == null) {
            log.warn("no path found in openapi");
            return;
        }

        // check and update extensions
        checkUpdateExtension(openAPI);

        // group by extension
        serviceMeta.putAll(updateTags(paths));
    }

    @Override
    public Meta getMeta(String operationName) {
        return serviceMeta.get(operationName);
    }


    @Override
    public Map<String, Meta> getServiceMeta() {
        return serviceMeta;
    }


    public void checkUpdateExtension(OpenAPI openAPI) {
        openAPI.getPaths().forEach((s, pathItem) -> {
            if (pathItem.getPut() != null || pathItem.getOptions() != null || pathItem.getHead() != null) {
                throw new UnsupportedOperationException("not support put options or patch operation");
            }
            pathItem.readOperationsMap().forEach((method, operation) -> {
                checkUpdateExtension(s, method, operation);
            });
        });
    }


    private void checkUpdateExtension(String path, PathItem.HttpMethod httpMethod, Operation operation) {
        if (operation == null) {
            return;
        }
        String opTag = httpMethod.name() + "-" + path;
        Map<String, Object> extensions = operation.getExtensions();
        if (extensions == null) {
            throw new IllegalArgumentException("no extension found for operation " + opTag);
        }
        String service = (String) extensions.get(EXTENSION_SDK_SERVICE);
        String method = (String) extensions.get(EXTENSION_SDK_METHOD);
        String subService = (String) extensions.get(EXTENSION_SDK_SUB_SERVICE);
        String domain = (String) extensions.get(EXTENSION_DOMAIN);
        boolean broker = false;

        if (StringUtils.isEmpty(service) || StringUtils.isEmpty(subService) || StringUtils.isEmpty(method)) {
            throw new IllegalArgumentException("no extension found for operation " + opTag);
        }

        if (service.equalsIgnoreCase(BROKER_KEY_NAME)) {
            broker = true;
        }

        service = nameService.formatService(service);
        subService = nameService.formatService(subService);
        method = nameService.formatMethodName(method);

        if (domain != null) {
            domain = domain.toLowerCase();
        }

        Meta meta = null;

        // we use trace to mark websocket operation for now
        if (httpMethod == PathItem.HttpMethod.TRACE) {
            if (!extensions.containsKey(EXTENSION_WS_PRIVATE)) {
                throw new IllegalArgumentException("no extension found for operation " + opTag);
            }

            String topic = (String) extensions.get(EXTENSION_WS_TOPIC);

            meta = new Meta(httpMethod, domain, service, subService, method, nameService.formatService(method),
                    (boolean) extensions.get(EXTENSION_WS_PRIVATE), true, TopicUtil.parseTopic(nameService, topic), false);
        } else {
            meta = new Meta(httpMethod, domain, service, subService, method, nameService.formatService(method), false, false, null, broker);
        }

        // use patch to mark override method
        if (httpMethod == PathItem.HttpMethod.PATCH) {
            if (!extensions.containsKey(EXTENSION_OVERRIDE_METHOD)) {
                throw new RuntimeException("no original method found");
            }
        }

        String[][] data = {
                {"API-DOMAIN", operation.getExtensions().getOrDefault("x-domain", "NULL").toString().toUpperCase()},
                {"API-CHANNEL", operation.getExtensions().getOrDefault("x-api-channel", "NULL").toString().toUpperCase()},
                {"API-PERMISSION", operation.getExtensions().getOrDefault("x-api-permission", "NULL").toString().toUpperCase()},
                {"API-RATE-LIMIT-POOL", operation.getExtensions().getOrDefault("x-api-rate-limit-pool", "NULL").toString().toUpperCase()},
                {"API-RATE-LIMIT-WEIGHT", operation.getExtensions().getOrDefault("x-api-rate-limit-weight", "NULL").toString().toUpperCase()},
        };


        int maxKeyLength = Arrays.stream(data)
                .mapToInt(row -> row[0].length())
                .max().orElse(0);

        List<String> kvData = new ArrayList<>();
        for (String[] info : data) {
           kvData.add(String.format("%-" + (maxKeyLength + 2) + "s: %s", info[0], info[1]));
        }

        String[] extraComment = AsciiTable.getTable(AsciiTable.BASIC_ASCII_NO_DATA_SEPARATORS, new Column[]{
                new Column().header("Extra API Info").headerAlign(HorizontalAlign.LEFT).dataAlign(HorizontalAlign.LEFT),
                new Column().header("Value").headerAlign(HorizontalAlign.LEFT).dataAlign(HorizontalAlign.LEFT),
        }, data).split("\n");

        operation.getExtensions().put(EXTENSION_EXTRA_COMMENT, extraComment);
        operation.getExtensions().put(EXTENSION_EXTRA_COMMENT_KV, kvData);
        operation.getExtensions().put(SpecificationUtil.EXTENSION_MODEL_META_KEY, meta);
    }

    private void updateTags(Map<String, Meta> serviceMap, Operation operation) {
        Meta meta = SpecificationUtil.getMeta(operation);
        if (meta == null) {
            return;
        }
        operation.setTags(Collections.singletonList(meta.uniqueServiceName()));
        serviceMap.put(meta.uniqueServiceName(), meta);
    }

    private Map<String, Meta> updateTags(Paths paths) {
        Map<String, Meta> serviceMap = new HashMap<>();
        paths.forEach((path, pathItem) -> {
            updateTags(serviceMap, pathItem.getGet());
            updateTags(serviceMap, pathItem.getPost());
            updateTags(serviceMap, pathItem.getDelete());
            updateTags(serviceMap, pathItem.getPatch());
            updateTags(serviceMap, pathItem.getTrace());
        });
        return serviceMap;
    }

}
