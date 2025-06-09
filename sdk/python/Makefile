
.PHONY: build
build:
	docker build -t python-sdk-image:latest .

.PHONY: auto-test
auto-test: build
	docker run -v venv-test-cache:/src/.venv-test --rm python-sdk-image:latest bash /src/auto_test.sh

.PHONY: before-release-test
before-release-test: build
	docker run --rm \
	  -e API_KEY="$$API_KEY" \
	  -e API_SECRET="$$API_SECRET" \
	  -e API_PASSPHRASE="$$API_PASSPHRASE" \
	  -e USE_LOCAL="true" \
	  python-sdk-image:latest \
	  bash /src/release_test.sh

.PHONY: after-release-test
after-release-test: build
	docker run --rm \
	  -e API_KEY="$$API_KEY" \
	  -e API_SECRET="$$API_SECRET" \
	  -e API_PASSPHRASE="$$API_PASSPHRASE" \
	  python-sdk-image:latest \
	  bash /src/release_test.sh

.PHONY: run-forever-test
run-forever-test: build
	docker run -idt \
	  -e API_KEY="$$API_KEY" \
	  -e API_SECRET="$$API_SECRET" \
	  -e API_PASSPHRASE="$$API_PASSPHRASE" \
	  -e USE_LOCAL="true" \
	  python-sdk-image:latest \
	  bash /src/run_forever_test.sh