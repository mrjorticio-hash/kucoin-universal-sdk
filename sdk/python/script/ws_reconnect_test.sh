#!/bin/bash
set -e
echo "Installing dependencies..."
echo "USE_LOCAL = ${USE_LOCAL:-<unset>}"

if [[ "${USE_LOCAL,,}" == "true" ]]; then
    echo "Installing local wheel(s)…"
    ls /tmp/*.whl 1>/dev/null 2>&1
    pip install --no-cache-dir /tmp/*.whl
else
    echo "Installing kucoin-universal-sdk from PyPI…"
    pip install --no-cache-dir kucoin-universal-sdk
fi

python test/regression/run_reconnect_test.py