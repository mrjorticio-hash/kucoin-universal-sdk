package com.kucoin.example;

import com.fasterxml.jackson.databind.ObjectMapper;
import java.net.URI;
import java.net.URLEncoder;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.nio.charset.StandardCharsets;
import java.util.Base64;
import java.util.Map;
import java.util.UUID;
import java.util.stream.Stream;
import javax.crypto.Mac;
import javax.crypto.spec.SecretKeySpec;
import lombok.extern.slf4j.Slf4j;

@Slf4j
public class ExampleSign {

  public static class KcSigner {
    private final String apiKey;
    private final String apiSecret;
    private String apiPassphrase;

    public KcSigner(String apiKey, String apiSecret, String apiPassphrase) {
      this.apiKey = apiKey != null ? apiKey : "";
      this.apiSecret = apiSecret != null ? apiSecret : "";
      this.apiPassphrase = apiPassphrase != null ? apiPassphrase : "";

      if (!this.apiSecret.isEmpty() && !this.apiPassphrase.isEmpty()) {
        this.apiPassphrase = sign(apiPassphrase, apiSecret);
      }

      if (this.apiKey.isEmpty() || this.apiSecret.isEmpty() || this.apiPassphrase.isEmpty()) {
        log.warn("Warning: API credentials are empty. Public endpoints only.");
      }
    }

    private String sign(String plain, String key) {
      try {
        Mac mac = Mac.getInstance("HmacSHA256");
        SecretKeySpec secretKey =
            new SecretKeySpec(key.getBytes(StandardCharsets.UTF_8), "HmacSHA256");
        mac.init(secretKey);
        byte[] hash = mac.doFinal(plain.getBytes(StandardCharsets.UTF_8));
        return Base64.getEncoder().encodeToString(hash);
      } catch (Exception e) {
        log.error("Error signing data: {}", e.getMessage(), e);
        return "";
      }
    }

    public Map<String, String> headers(String payload) {
      String timestamp = String.valueOf(System.currentTimeMillis());
      String signature = sign(timestamp + payload, apiSecret);

      return Map.of(
          "KC-API-KEY", apiKey,
          "KC-API-PASSPHRASE", apiPassphrase,
          "KC-API-TIMESTAMP", timestamp,
          "KC-API-SIGN", signature,
          "KC-API-KEY-VERSION", "3",
          "Content-Type", "application/json");
    }
  }

  public static Map<String, String> processHeaders(
      KcSigner signer, String body, String rawUrl, String method) {
    String payload = method + rawUrl + body;
    return signer.headers(payload);
  }

  public static void getTradeFees(KcSigner signer, HttpClient client) {
    String endpoint = "https://api.kucoin.com";
    String path = "/api/v1/trade-fees";
    String method = "GET";
    String query = "symbols=" + URLEncoder.encode("BTC-USDT", StandardCharsets.UTF_8);
    String rawUrl = path + "?" + query;
    String url = endpoint + rawUrl;

    Map<String, String> headers = processHeaders(signer, "", rawUrl, method);

    try {
      HttpRequest request =
          HttpRequest.newBuilder()
              .uri(URI.create(url))
              .method(method, HttpRequest.BodyPublishers.noBody())
              .headers(
                  headers.entrySet().stream()
                      .flatMap(e -> Stream.of(e.getKey(), e.getValue()))
                      .toArray(String[]::new))
              .build();

      HttpResponse<String> response = client.send(request, HttpResponse.BodyHandlers.ofString());
      log.info("{}", response.body());
    } catch (Exception e) {
      log.error("Error fetching trade fees: {}", e.getMessage(), e);
    }
  }

  public static void addLimitOrder(KcSigner signer, HttpClient client) {
    String endpoint = "https://api.kucoin.com";
    String path = "/api/v1/hf/orders";
    String method = "POST";
    String url = endpoint + path;
    String rawUrl = path;

    Map<String, Object> bodyData =
        Map.of(
            "clientOid", UUID.randomUUID().toString(),
            "side", "buy",
            "symbol", "BTC-USDT",
            "type", "limit",
            "price", "10000",
            "size", "0.001");

    String bodyJson;
    try {
      bodyJson = new ObjectMapper().writeValueAsString(bodyData);
    } catch (Exception e) {
      log.error("Error serializing body: {}", e.getMessage(), e);
      return;
    }

    Map<String, String> headers = processHeaders(signer, bodyJson, rawUrl, method);

    try {
      HttpRequest request =
          HttpRequest.newBuilder()
              .uri(URI.create(url))
              .method(method, HttpRequest.BodyPublishers.ofString(bodyJson))
              .headers(
                  headers.entrySet().stream()
                      .flatMap(e -> Stream.of(e.getKey(), e.getValue()))
                      .toArray(String[]::new))
              .build();

      HttpResponse<String> response = client.send(request, HttpResponse.BodyHandlers.ofString());
      log.info("{}", response.body());
    } catch (Exception e) {
      log.error("Error placing limit order: {}", e.getMessage(), e);
    }
  }

  public static void main(String[] args) {
    String key = System.getenv("API_KEY");
    String secret = System.getenv("API_SECRET");
    String passphrase = System.getenv("API_PASSPHRASE");

    KcSigner signer = new KcSigner(key, secret, passphrase);
    HttpClient client = HttpClient.newHttpClient();

    getTradeFees(signer, client);
    addLimitOrder(signer, client);
  }
}
