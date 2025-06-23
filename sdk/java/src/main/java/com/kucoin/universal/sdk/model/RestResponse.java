package com.kucoin.universal.sdk.model;

import com.fasterxml.jackson.annotation.JsonProperty;
import lombok.Getter;

@Getter
public class RestResponse<T> {

    @JsonProperty("code")
    private String code;

    @JsonProperty("data")
    private T data;

    @JsonProperty("msg")
    private String message;

    @JsonProperty("rateLimit")
    private RestRateLimit rateLimit;

    public void checkError() throws RestError {
        //TODO
    }
}
