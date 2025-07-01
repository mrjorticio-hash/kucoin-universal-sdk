#!/bin/bash
set -e

cd example

npm pkg delete dependencies

echo "Installing dependencies…"
echo "USE_LOCAL = ${USE_LOCAL:-<unset>}"

npm install

if [[ "${USE_LOCAL,,}" == "true" ]]; then
  echo "Installing local package(s)…"
  cp /tmp/*.tgz ./
  npm install *.tgz
else
  echo "Installing kucoin-universal-sdk from npm registry…"
  npm install kucoin-universal-sdk
fi
npm i -D tsx typescript

cat package.json

echo "Run TS tests...."
npx tsx src/regression/run_forever_test.ts

