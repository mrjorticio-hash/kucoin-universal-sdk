package com.kucoin.universal.sdk.test.robustness;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.lang.management.GarbageCollectorMXBean;
import java.lang.management.ManagementFactory;
import lombok.extern.slf4j.Slf4j;

@Slf4j
public class ResourceLeakStat {

  public static void stat(String label) {
    long memBytes = getUsedMemory();
    int threads = Thread.activeCount();
    long fds = getFdCount();
    long tcp = getTcpConnCount();
    GcInfo gc = getGcInfo();

    log.info(
        "[{}] Mem: {} MB, Threads: {}, FDs: {}, TCP: {}, GC: count={}, time={}ms",
        label,
        memBytes / 1024.0 / 1024,
        threads,
        fds,
        tcp,
        gc.count,
        gc.time);
  }

  private static long getUsedMemory() {
    Runtime rt = Runtime.getRuntime();
    return rt.totalMemory() - rt.freeMemory();
  }

  private static long getFdCount() {
    try {
      String pid = getPid();
      Process p = new ProcessBuilder("lsof", "-p", pid).start();
      return readLines(p);
    } catch (Exception e) {
      log.warn("FD count failed: {}", e.getMessage());
      return -1;
    }
  }

  public static long getTcpConnCount() {
    try {
      String pid = getPid();
      String[] cmd = {"sh", "-c", "lsof -iTCP -n -P | grep " + pid + " | wc -l"};
      Process process = new ProcessBuilder(cmd).start();
      try (BufferedReader reader =
          new BufferedReader(new InputStreamReader(process.getInputStream()))) {
        String line = reader.readLine();
        return line != null ? Long.parseLong(line.trim()) : 0;
      }
    } catch (Exception e) {
      log.warn("TCP connection count failed: {}", e.getMessage());
      return -1;
    }
  }

  private static long readLines(Process p) throws Exception {
    try (BufferedReader r = new BufferedReader(new InputStreamReader(p.getInputStream()))) {
      return r.lines().count();
    }
  }

  private static GcInfo getGcInfo() {
    long count = 0, time = 0;
    for (GarbageCollectorMXBean gc : ManagementFactory.getGarbageCollectorMXBeans()) {
      if (gc.getCollectionCount() >= 0) count += gc.getCollectionCount();
      if (gc.getCollectionTime() >= 0) time += gc.getCollectionTime();
    }
    return new GcInfo(count, time);
  }

  private static String getPid() {
    return ManagementFactory.getRuntimeMXBean().getName().split("@")[0];
  }

  private static class GcInfo {
    long count;
    long time;

    GcInfo(long count, long time) {
      this.count = count;
      this.time = time;
    }
  }

  public static void main(String[] args) {
    ResourceLeakStat.stat("Start");
  }

  public static void printAllThreadsWithStack() {
    Thread.getAllStackTraces()
        .forEach(
            (thread, stackTrace) -> {
              System.out.printf(
                  "=== Thread: %s (id=%d, state=%s) ===\n",
                  thread.getName(), thread.getId(), thread.getState());
              for (StackTraceElement element : stackTrace) {
                System.out.println("  at " + element);
              }
              System.out.println();
            });
  }
}
