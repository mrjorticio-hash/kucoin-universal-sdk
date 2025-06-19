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

ls

echo "Run TS tests...."
npx tsx src/regression/run_service_test.ts
npx tsx src/ts/example_api.ts
npx tsx src/ts/example_get_started.ts
npx tsx src/ts/example_sign.ts
npx tsx src/ts/example_ws.ts

echo "Run JS tests...."
node src/js/example_api.js
node src/js/example_get_started.js
node src/js/example_sign.js
node src/js/example_ws.js

