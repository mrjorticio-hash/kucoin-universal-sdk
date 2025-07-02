#!/bin/bash
set -e
VENV_DIR=".venv"

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

pip install pytest

pytest -q test/regression/run_service_test.py
python example/example_api.py
python example/example_get_started.py
python example/example_sign.py
python example/example_ws.py