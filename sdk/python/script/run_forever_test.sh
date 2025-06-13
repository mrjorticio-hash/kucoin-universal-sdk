#!/bin/bash
set -e
VENV_DIR=".venv"

echo "Virtual environment not found. Creating a new one..."
python3 -m venv "$VENV_DIR" || { echo "Failed to create virtual environment"; exit 1; }
echo "Virtual environment created at $VENV_DIR."

source "$VENV_DIR/bin/activate"
echo "Installing dependencies..."

echo "USE_LOCAL = ${USE_LOCAL:-<unset>}"

if [[ "${USE_LOCAL,,}" == "true" ]]; then
    echo "📦 Installing local wheel(s)…"
    ls /tmp/*.whl 1>/dev/null 2>&1
    pip install --no-cache-dir /tmp/*.whl
else
    echo "📦 Installing kucoin-universal-sdk from PyPI…"
    pip install --no-cache-dir kucoin-universal-sdk
fi

python test/regression/run_forever_test.py
deactivate