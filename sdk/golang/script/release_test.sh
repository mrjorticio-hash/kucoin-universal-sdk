#!/bin/bash
set -e
echo "USE_LOCAL = ${USE_LOCAL:-<unset>}"

go mod init github.com/kucoin/sdk-test-runner
if [[ "${USE_LOCAL,,}" == "true" ]]; then
    echo "Using local Go SDK..."
    cp -r /src /go/src/kucoin-universal-sdk
    echo 'require github.com/Kucoin/kucoin-universal-sdk/sdk/golang v0.0.0' >> go.mod
    echo 'replace github.com/Kucoin/kucoin-universal-sdk/sdk/golang => /go/src/kucoin-universal-sdk' >> go.mod
else
    echo "Installing kucoin-universal-sdk from remote..."
    go get github.com/Kucoin/kucoin-universal-sdk/sdk/golang
fi

cat go.mod
go mod tidy
go run main.go example_api.go example_get_started.go example_sign.go example_ws.go