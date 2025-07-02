#!/bin/bash
cd /src

npm install

printf "%-20s | %-10s | %-10s | %-10s\n" "Directory" "Total" "Success" "Fail"
printf "%-20s-+-%-10s-+-%-10s-+-%-10s\n" "--------------------" "----------" "----------" "----------"

overall_total=0
overall_success=0
overall_fail=0

JEST_OUTPUT=$(npx jest --json src/generate --testLocationInResults --silent 2>/dev/null)

RESULTS=$(echo "$JEST_OUTPUT" | jq -c '
  .testResults
  | group_by(.name | split("/")[-2])
  | map({
      dir: .[0].name | split("/")[-2],
      total: (map(.assertionResults | length) | add // 0),
      success: (map(.assertionResults | map(select(.status == "passed")) | length) | add // 0),
      fail: (map(.assertionResults | map(select(.status == "failed")) | length) | add // 0)
    })
  | .[]
')

for test_result in $RESULTS; do
    dir_name=$(echo "$test_result" | jq -r '.dir')
    total_tests=$(echo "$test_result" | jq -r '.total // 0')
    successful_tests=$(echo "$test_result" | jq -r '.success // 0')
    failed_tests=$(echo "$test_result" | jq -r '.fail // 0')

    printf "%-20s | %-10d | %-10d | %-10d\n" "$dir_name" "$total_tests" "$successful_tests" "$failed_tests"

    ((overall_total += total_tests))
    ((overall_success += successful_tests))
    ((overall_fail += failed_tests))
done

printf "%-20s-+-%-10s-+-%-10s-+-%-10s\n" "--------------------" "----------" "----------" "----------"
printf "%-20s | %-10d | %-10d | %-10d\n" "Overall Summary" "$overall_total" "$overall_success" "$overall_fail"
