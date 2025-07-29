package com.kucoin.universal.sdk.internal.infra;

import com.kucoin.universal.sdk.internal.interfaces.Response;
import com.kucoin.universal.sdk.internal.interfaces.Transport;
import com.kucoin.universal.sdk.internal.interfaces.WsToken;
import com.kucoin.universal.sdk.internal.interfaces.WsTokenProvider;
import com.kucoin.universal.sdk.model.RestResponse;
import java.util.Collections;
import java.util.List;
import java.util.Objects;
import lombok.Data;
import lombok.ToString;

public class DefaultWsTokenProvider implements WsTokenProvider {

  private static final String PATH_PRIVATE = "/api/v1/bullet-private";
  private static final String PATH_PUBLIC = "/api/v1/bullet-public";

  private final Transport transport;
  private final String domainType;
  private final boolean isPrivate;

  public DefaultWsTokenProvider(Transport transport, String domainType, boolean isPrivate) {
    this.transport = Objects.requireNonNull(transport, "transport");
    this.domainType = Objects.requireNonNull(domainType, "domainType");
    this.isPrivate = isPrivate;
  }

  @Override
  public List<WsToken> getToken() {
    String path = isPrivate ? PATH_PRIVATE : PATH_PUBLIC;

    TokenResponse result =
        transport.call(domainType, false, "POST", path, null, TokenResponse.class, false);

    if (result == null
        || result.getToken() == null
        || result.getInstanceServers() == null
        || result.getInstanceServers().isEmpty()) {
      return Collections.emptyList();
    }

    result.getInstanceServers().forEach(s -> s.setToken(result.getToken()));
    return result.getInstanceServers();
  }

  @Override
  public void close() {
    transport.close();
  }

  @Data
  @ToString
  private static class TokenResponse
      implements Response<TokenResponse, RestResponse<TokenResponse>> {
    private String token;
    private List<WsToken> instanceServers;

    @Override
    public void setCommonResponse(RestResponse<TokenResponse> response) {}
  }
}
