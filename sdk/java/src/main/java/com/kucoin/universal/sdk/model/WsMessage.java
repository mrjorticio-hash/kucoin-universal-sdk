package com.kucoin.universal.sdk.model;

import com.fasterxml.jackson.annotation.JsonIgnoreProperties;
import com.fasterxml.jackson.annotation.JsonProperty;
import lombok.Data;
import lombok.ToString;

@Data
@ToString(exclude = "data")
@JsonIgnoreProperties(ignoreUnknown = true)
public class WsMessage<T> {

  /** Unique message ID */
  @JsonProperty("id")
  private String id;

  /** Message type (e.g., "ping", "subscribe", etc.) */
  @JsonProperty("type")
  private String type;

  /** Sequence number */
  @JsonProperty("sn")
  private Long sn;

  /** The topic of the message */
  @JsonProperty("topic")
  private String topic;

  /** Subject of the message */
  @JsonProperty("subject")
  private String subject;

  /** Indicates if it is a private channel */
  @JsonProperty("privateChannel")
  private Boolean privateChannel;

  /** Indicates if the message is a response */
  @JsonProperty("response")
  private Boolean response;

  /** Raw message data */
  @JsonProperty("data")
  private T data;
}
