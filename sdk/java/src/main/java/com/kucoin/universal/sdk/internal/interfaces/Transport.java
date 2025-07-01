package com.kucoin.universal.sdk.internal.interfaces;

import com.kucoin.universal.sdk.model.RestResponse;

public interface Transport {

    /**
     * Executes a remote call using the specified method, path, and request data,
     * and returns the deserialized response object.
     *
     * @param domain         Which endpoint to use (e.g., spot, futures, broker)
     * @param broker         Whether this is a broker service request
     * @param method         HTTP method such as GET, POST, etc.
     * @param path           Path or endpoint of the request
     * @param requestObj     The request payload (can be null)
     * @param responseClass  The class of the expected response object
     * @param requestAsJson  Whether to serialize the request as JSON
     * @param <T>            Type of response expected
     * @return               Parsed response object of type T
     */
    <T extends Response<T, RestResponse<T>>> T call(
            String domain,
            boolean broker,
            String method,
            String path,
            Request requestObj,
            Class<T> responseClass,
            boolean requestAsJson
    );

    /**
     * Clean up resources or close the connection, if necessary.
     */
    void close();
}
