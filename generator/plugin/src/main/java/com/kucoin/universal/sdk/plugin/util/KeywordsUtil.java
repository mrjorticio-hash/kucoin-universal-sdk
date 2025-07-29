package com.kucoin.universal.sdk.plugin.util;

import java.util.Map;

/**
 * @author isaac.tang
 */
public class KeywordsUtil {

    private static Map<String, String> specialKeywords =
            Map.of("copytrading", "CopyTrading", "viplending", "VIPLending", "ndbroker", "NDBroker", "apibroker", "APIBroker");


    public static String getKeyword(String key) {
        return specialKeywords.getOrDefault(key.toLowerCase(), key);
    }

}
