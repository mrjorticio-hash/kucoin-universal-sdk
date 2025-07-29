package com.kucoin.universal.sdk.internal.infra;

import java.util.concurrent.ConcurrentHashMap;
import java.util.concurrent.ConcurrentMap;
import java.util.function.BiConsumer;

final class TopicManager {

  private final ConcurrentMap<String, CallbackManager> map = new ConcurrentHashMap<>();

  CallbackManager getCallbackManager(String topic) {
    String[] parts = topic.split(":", 2);
    String prefix = topic;
    if (parts.length == 2 && !"all".equals(parts[1])) {
      prefix = parts[0];
    }

    return map.computeIfAbsent(prefix, CallbackManager::new);
  }

  void forEach(BiConsumer<String, CallbackManager> action) {
    map.forEach(action);
  }

  boolean isEmpty() {
    return map.isEmpty();
  }
}
