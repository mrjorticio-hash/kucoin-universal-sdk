#!/bin/bash

cd /src
composer install

LOG_FILE="/src/test.log"
[ -f "$LOG_FILE" ] && rm "$LOG_FILE"

overall_total=0
overall_success=0
overall_fail=0

{
  printf "%-20s | %-10s | %-10s | %-10s\n" "Directory" "Total" "Success" "Fail"
  printf "%-20s-+-%-10s-+-%-10s-+-%-10s\n" "--------------------" "----------" "----------" "----------"
} | tee "$LOG_FILE"

for dir in src/Generate/*/; do
  dir_name=${dir%/}
  dir_name=${dir_name##*/}

  test_output=$(vendor/bin/phpunit "$dir" --colors=never 2>&1 || true)
  echo "$test_output" >> "$LOG_FILE"

  summary_line=$(echo "$test_output" | grep -E "OK \(|Tests:|No tests executed")

  if [[ $summary_line == OK* ]]; then
    # OK (5 tests, 12 assertions)
    total_tests=$(echo "$summary_line" | grep -oE '[0-9]+' | head -n1)
    failed_tests=0
  elif [[ $summary_line == Tests:* ]]; then
    # Tests: 7, Assertions: 12, Failures: 2, Errors: 1
    total_tests=$(echo "$summary_line" | grep -oE 'Tests:[[:space:]]*[0-9]+' | grep -oE '[0-9]+')
    failed_tests=$(echo "$summary_line" | grep -oE 'Failures:[[:space:]]*[0-9]+' | grep -oE '[0-9]+')
    failed_tests=${failed_tests:-0}
  else
    # No tests executed!
    total_tests=0
    failed_tests=0
  fi

  successful_tests=$((total_tests - failed_tests))

  printf "%-20s | %-10d | %-10d | %-10d\n" \
    "$dir_name" "$total_tests" "$successful_tests" "$failed_tests" | tee -a "$LOG_FILE"

  overall_total=$((overall_total + total_tests))
  overall_success=$((overall_success + successful_tests))
  overall_fail=$((overall_fail + failed_tests))
done

{
  printf "%-20s-+-%-10s-+-%-10s-+-%-10s\n" "--------------------" "----------" "----------" "----------"
  printf "%-20s | %-10d | %-10d | %-10d\n" \
    "Overall Summary" "$overall_total" "$overall_success" "$overall_fail"
} | tee -a "$LOG_FILE"

exit "$overall_fail"
