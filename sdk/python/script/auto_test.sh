#!/bin/bash
set -e
VENV_DIR=".venv-test"

if [ ! -d "$VENV_DIR" ] || [ -z "$(ls -A "$VENV_DIR" 2>/dev/null)" ]; then
    echo "Virtual environment not found. Creating a new one..."
    python3 -m venv "$VENV_DIR" || { echo "Failed to create virtual environment"; exit 1; }
    echo "Virtual environment created at $VENV_DIR."

    source "$VENV_DIR/bin/activate"
    echo "Installing dependencies..."
    pip install --upgrade pip || { echo "Failed to upgrade pip"; exit 1; }
    if [ -f requirements-test.txt ]; then
        pip install -r requirements-test.txt || { echo "Failed to install dependencies"; exit 1; }
    else
        echo "requirements-test.txt not found. Skipping dependency installation."
    fi
else
    echo "Activating existing virtual environment at $VENV_DIR..."
    source "$VENV_DIR/bin/activate"
fi

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PROJECT_ROOT="$SCRIPT_DIR"
export PYTHONPATH="$PROJECT_ROOT:$PYTHONPATH"
cd "$PROJECT_ROOT" || exit 1

LOG_FILE="$SCRIPT_DIR/test.log"
[ -f "$LOG_FILE" ] && rm "$LOG_FILE"

overall_total=0
overall_success=0
overall_fail=0

{
    printf "%-20s | %-10s | %-10s | %-10s\n" "Directory" "Total" "Success" "Fail"
    printf "%-20s-+-%-10s-+-%-10s-+-%-10s\n" "--------------------" "----------" "----------" "----------"
} | tee "$LOG_FILE"

for dir in kucoin_universal_sdk/generate/*/; do
    dir_name=${dir%/}
    dir_name=${dir_name##*/}

    test_output=$(pytest "$dir" --tb=short --disable-warnings --maxfail=10000 2>&1 | tee -a "$LOG_FILE")

    total_tests=$(echo "$test_output" | grep -oE '[0-9]+ passed|[0-9]+ failed' | awk '{sum+=$1} END {print sum}')
    failed_tests=$(echo "$test_output" | grep -oE '[0-9]+ failed' | awk '{sum+=$1} END {print sum}')
    successful_tests=$((total_tests - failed_tests))

    total_tests=${total_tests:-0}
    successful_tests=${successful_tests:-0}
    failed_tests=${failed_tests:-0}

    printf "%-20s | %-10d | %-10d | %-10d\n" "$dir_name" "$total_tests" "$successful_tests" "$failed_tests" | tee -a "$LOG_FILE"

    overall_total=$((overall_total + total_tests))
    overall_success=$((overall_success + successful_tests))
    overall_fail=$((overall_fail + failed_tests))
done

{
    printf "%-20s-+-%-10s-+-%-10s-+-%-10s\n" "--------------------" "----------" "----------" "----------"
    printf "%-20s | %-10d | %-10d | %-10d\n" "Overall Summary" "$overall_total" "$overall_success" "$overall_fail"
} | tee -a "$LOG_FILE"


deactivate