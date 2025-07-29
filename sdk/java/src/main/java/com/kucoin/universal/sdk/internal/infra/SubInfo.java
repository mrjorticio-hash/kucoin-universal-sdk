package com.kucoin.universal.sdk.internal.infra;

import com.kucoin.universal.sdk.internal.interfaces.WebSocketMessageCallback;
import java.util.*;
import java.util.stream.Collectors;
import lombok.Getter;
import lombok.RequiredArgsConstructor;

@RequiredArgsConstructor
@Getter
public final class SubInfo {

  private static final String SEP = "@@";
  private static final String EMPTY_ARGS_STR = "EMPTY_ARGS";

  private final String prefix;
  private final List<String> args;
  private final WebSocketMessageCallback callback;

  public String toId() {
    if (args == null || args.isEmpty()) {
      return prefix + SEP + EMPTY_ARGS_STR;
    }

    List<String> sorted = new ArrayList<>(args);
    Collections.sort(sorted);
    return prefix + SEP + String.join(",", sorted);
  }

  public static SubInfo fromId(String id) {
    String[] parts = id.split(SEP, -1);
    if (parts.length != 2) throw new IllegalArgumentException("invalid id: " + id);

    String prefix = parts[0];
    List<String> args;
    if (parts[1].equals(EMPTY_ARGS_STR)) {
      args = new LinkedList<>();
    } else {
      args = Arrays.asList(parts[1].split(","));
    }
    return new SubInfo(prefix, args, null);
  }

  public String subTopic() {
    return args == null || args.isEmpty() ? prefix : prefix + ":" + String.join(",", args);
  }

  public Set<String> topics() {
    if (args == null || args.isEmpty()) {
      return Collections.singleton(prefix);
    }
    return args.stream().map(a -> prefix + ":" + a).collect(Collectors.toSet());
  }
}
