#!/bin/bash
set -e
LOG_FILE="${PWD}/test.log"
if [ -f "$LOG_FILE" ]; then
    rm "$LOG_FILE"
fi

cd /src
go mod tidy

cd "/src/pkg/generate" || exit 1

go test -json ./... 2>&1 | tee "$LOG_FILE" >/dev/null

printf "%-20s | %-10s | %-10s | %-10s\n" "Directory" "Total" "Success" "Fail"
printf "%-20s-+-%-10s-+-%-10s-+-%-10s\n" "--------------------" "----------" "----------" "----------"

overall_total=0
overall_success=0
overall_fail=0

for dir in */; do
    dir_name=${dir%/}

    total_tests=$(grep -E "\"Package\":\".*/$dir_name" "$LOG_FILE" | grep '"Action":"run"' | wc -l)
    successful_tests=$(grep -E "\"Package\":\".*/$dir_name" "$LOG_FILE" | grep '"Action":"pass"' | grep Test |  wc -l)
    failed_tests=$(grep -E "\"Package\":\".*/$dir_name" "$LOG_FILE" | grep '"Action":"fail"' | wc -l)

    printf "%-20s | %-10d | %-10d | %-10d\n" "$dir_name" "$total_tests" "$successful_tests" "$failed_tests"

    overall_total=$((overall_total + total_tests))
    overall_success=$((overall_success + successful_tests))
    overall_fail=$((overall_fail + failed_tests))
done

printf "%-20s-+-%-10s-+-%-10s-+-%-10s\n" "--------------------" "----------" "----------" "----------"
printf "%-20s | %-10d | %-10d | %-10d\n" "Overall Summary" "$overall_total" "$overall_success" "$overall_fail"