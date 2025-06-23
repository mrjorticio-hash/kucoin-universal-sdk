package com.kucoin.universal.sdk.model;

import lombok.Getter;

/**
 * Exception representing a REST API error.
 */
@Getter
public class RestError extends Exception {

    private final RestResponse<?> response;
    private final Throwable cause;

    public RestError(RestResponse<?> response, Throwable cause) {
        super(cause != null ? cause.getMessage() : "unknown", cause);
        this.response = response;
        this.cause = cause;
    }

    @Override
    public String toString() {
        if (response != null) {
            return String.format(
                    "request error, server code: %s, server msg: %s, context msg: %s",
                    response.getCode(),
                    response.getMessage(),
                    cause != null ? cause.getMessage() : "unknown"
            );
        }
        return "request error, " + (cause != null ? cause.toString() : "unknown");
    }
}
