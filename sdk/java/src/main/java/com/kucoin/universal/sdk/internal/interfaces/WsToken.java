package com.kucoin.universal.sdk.internal.interfaces;

import com.fasterxml.jackson.annotation.JsonProperty;
import lombok.AllArgsConstructor;
import lombok.Builder;
import lombok.Data;
import lombok.NoArgsConstructor;

/** Web-socket token object returned by the “bullet” API. */
@Data
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class WsToken {

  /** Token string used when establishing the socket. */
  @JsonProperty("$token")
  private String token;

  /** WebSocket domain URL (may change, always use the latest one). */
  @JsonProperty("endpoint")
  private String endpoint;

  /** Whether the endpoint is encrypted – currently only <code>wss://</code> is supported. */
  @JsonProperty("encrypt")
  private boolean encrypt;

  /** Network protocol, e.g. <code>websocket</code>. */
  @JsonProperty("protocol")
  private String protocol;

  /** Recommended ping interval in milliseconds. */
  @JsonProperty("pingInterval")
  private int pingInterval;

  /** Heart-beat timeout in milliseconds. */
  @JsonProperty("pingTimeout")
  private int pingTimeout;
}
