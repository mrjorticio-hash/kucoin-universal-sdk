.PHONY: build
build:
	docker build -t golang-sdk-image:latest -f Dockerfile .

.PHONY: auto-test
auto-test: build
	docker run --rm golang-sdk-image:latest bash /app/auto_test.sh

.PHONY: before-release-test
before-release-test: build
	docker run --rm \
	  -e API_KEY="$$API_KEY" \
	  -e API_SECRET="$$API_SECRET" \
	  -e API_PASSPHRASE="$$API_PASSPHRASE" \
	  -e USE_LOCAL="true" \
	  golang-sdk-image:latest \
	  bash /app/release_test.sh

.PHONY: after-release-test
after-release-test: build
	docker run --rm \
	  -e API_KEY="$$API_KEY" \
	  -e API_SECRET="$$API_SECRET" \
	  -e API_PASSPHRASE="$$API_PASSPHRASE" \
	  golang-sdk-image:latest \
	  bash /app/release_test.sh

.PHONY: run-forever-test
run-forever-test: build
	docker rm -f golang-run-forever-test || true
	docker run -idt \
	  -e API_KEY="$$API_KEY" \
	  -e API_SECRET="$$API_SECRET" \
	  -e API_PASSPHRASE="$$API_PASSPHRASE" \
	  --name golang-run-forever-test \
	  golang-sdk-image:latest \
	  bash /app/run_forever_test.sh

.PHONY: reconnect-test
reconnect-test: build
	docker rm -f golang-reconnect-test || true
	docker run -idt \
	  -e API_KEY="$$API_KEY" \
	  -e API_SECRET="$$API_SECRET" \
	  -e API_PASSPHRASE="$$API_PASSPHRASE" \
	  --name golang-reconnect-test --network isolated_net \
	  golang-sdk-image:latest \
	  bash /app/ws_reconnect_test.sh
