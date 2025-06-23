package com.kucoin.universal.sdk.internal.interfaces;

import com.kucoin.universal.sdk.model.RestResponse;

public interface Response<T> {

    void setCommonResponse(RestResponse<T> response);
}
