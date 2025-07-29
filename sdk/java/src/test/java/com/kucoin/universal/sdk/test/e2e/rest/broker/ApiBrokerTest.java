package com.kucoin.universal.sdk.test.e2e.rest.broker;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.broker.apibroker.APIBrokerApi;
import com.kucoin.universal.sdk.generate.broker.apibroker.GetRebaseReq;
import com.kucoin.universal.sdk.generate.broker.apibroker.GetRebaseResp;
import com.kucoin.universal.sdk.model.ClientOption;
import com.kucoin.universal.sdk.model.Constants;
import com.kucoin.universal.sdk.model.TransportOption;
import java.io.IOException;
import java.util.Collections;
import lombok.extern.slf4j.Slf4j;
import okhttp3.Interceptor;
import okhttp3.Request;
import okhttp3.Response;
import org.jetbrains.annotations.NotNull;
import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.BeforeAll;
import org.junit.jupiter.api.Test;

@Slf4j
public class ApiBrokerTest {

  private static APIBrokerApi api;

  public static ObjectMapper mapper = new ObjectMapper();

  @BeforeAll
  public static void setUp() {

    String key = System.getenv("API_KEY");
    String secret = System.getenv("API_SECRET");
    String passphrase = System.getenv("API_PASSPHRASE");
    String brokerName = System.getenv("BROKER_NAME");
    String brokerPartner = System.getenv("BROKER_PARTNER");
    String brokerKey = System.getenv("BROKER_KEY");

    TransportOption httpTransport =
        TransportOption.builder()
            .interceptors(
                Collections.singleton(
                    new Interceptor() {
                      @NotNull @Override
                      public Response intercept(@NotNull Chain chain) throws IOException {
                        Request request = chain.request();

                        System.out.println("========== Request ==========");
                        System.out.println(request.method() + " " + request.url());

                        Response response = chain.proceed(request);

                        System.out.println("========== Response ==========");
                        System.out.println("Status Code: " + response.code());
                        System.out.println("Message: " + response.message());
                        return response;
                      }
                    }))
            .build();

    ClientOption clientOpt =
        ClientOption.builder()
            .key(key)
            .secret(secret)
            .passphrase(passphrase)
            .brokerName(brokerName)
            .brokerPartner(brokerPartner)
            .brokerKey(brokerKey)
            .spotEndpoint(Constants.GLOBAL_API_ENDPOINT)
            .futuresEndpoint(Constants.GLOBAL_FUTURES_API_ENDPOINT)
            .brokerEndpoint(Constants.GLOBAL_BROKER_API_ENDPOINT)
            .transportOption(httpTransport)
            .build();

    KucoinClient kucoinClient = new DefaultKucoinClient(clientOpt);
    api = kucoinClient.getRestService().getBrokerService().getAPIBrokerApi();
  }

  /** getRebase Get Broker Rebate /api/v1/broker/api/rebase/download */
  @Test
  public void testGetRebase() throws Exception {
    GetRebaseReq.GetRebaseReqBuilder builder = GetRebaseReq.builder();
    builder.begin("20240610").end("20241010").tradeType(GetRebaseReq.TradeTypeEnum._1);
    GetRebaseReq req = builder.build();
    GetRebaseResp resp = api.getRebase(req);
    Assertions.assertNotNull(resp.getUrl());
    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
