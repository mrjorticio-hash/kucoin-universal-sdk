package com.kucoin.universal.sdk.test.e2e.rest.margin;

import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.kucoin.universal.sdk.api.DefaultKucoinClient;
import com.kucoin.universal.sdk.api.KucoinClient;
import com.kucoin.universal.sdk.generate.margin.risklimit.GetMarginRiskLimitReq;
import com.kucoin.universal.sdk.generate.margin.risklimit.GetMarginRiskLimitResp;
import com.kucoin.universal.sdk.generate.margin.risklimit.RiskLimitApi;
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
import org.junit.jupiter.api.BeforeAll;
import org.junit.jupiter.api.Test;

@Slf4j
public class RiskLimitApiTest {

  private static RiskLimitApi api;

  public static ObjectMapper mapper = new ObjectMapper();

  @BeforeAll
  public static void setUp() {

    String key = System.getenv("API_KEY");
    String secret = System.getenv("API_SECRET");
    String passphrase = System.getenv("API_PASSPHRASE");

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
            .spotEndpoint(Constants.GLOBAL_API_ENDPOINT)
            .futuresEndpoint(Constants.GLOBAL_FUTURES_API_ENDPOINT)
            .brokerEndpoint(Constants.GLOBAL_BROKER_API_ENDPOINT)
            .transportOption(httpTransport)
            .build();

    KucoinClient kucoinClient = new DefaultKucoinClient(clientOpt);
    api = kucoinClient.getRestService().getMarginService().getRiskLimitApi();
  }

  /** getMarginRiskLimit Get Margin Risk Limit /api/v3/margin/currencies */
  @Test
  public void testGetMarginRiskLimit() throws Exception {
    GetMarginRiskLimitReq.GetMarginRiskLimitReqBuilder builder = GetMarginRiskLimitReq.builder();
    builder.isIsolated(false).currency("BTC");
    GetMarginRiskLimitReq req = builder.build();
    GetMarginRiskLimitResp resp = api.getMarginRiskLimit(req);
    resp.getData()
        .forEach(
            item -> {
              try {
                log.info("resp: {}", mapper.writeValueAsString(item));
              } catch (JsonProcessingException e) {
                throw new RuntimeException(e);
              }
            });

    log.info("resp: {}", mapper.writeValueAsString(resp));
  }
}
