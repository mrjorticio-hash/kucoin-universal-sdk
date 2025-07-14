package com.kucoin.universal.sdk.e2e.rest.account;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.Client;
import com.kucoin.universal.sdk.api.DefaultClient;
import com.kucoin.universal.sdk.generate.account.account.*;
import com.kucoin.universal.sdk.model.ClientOption;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.TransportOption;
import lombok.extern.slf4j.Slf4j;
import org.junit.jupiter.api.BeforeAll;

@Slf4j
public class AccountApiTest {

  private static AccountApi api;

  public static ObjectMapper mapper = new ObjectMapper();

  @BeforeAll
  public static void setUp() {

    String key = System.getenv("API_KEY");
    String secret = System.getenv("API_SECRET");
    String passphrase = System.getenv("API_PASSPHRASE");

    TransportOption httpTransport = TransportOption.builder().build();

    ClientOption clientOpt =
        ClientOption.builder()
            .key(key)
            .secret(secret)
            .passphrase(passphrase)
            .spotEndpoint(Constants.GLOBAL_API_ENDPOINT)
            .futuresEndpoint(Constants.GLOBAL_FUTURES_API_ENDPOINT)
            .brokerEndpoint(Constants.GLOBAL_BROKER_API_ENDPOINT)
            .transportOption(httpTransport)
            .build();

    Client client = new DefaultClient(clientOpt);
    api = client.getRestService().getAccountService().getAccountApi();
  }
}
