
.PHONY: build
build:
	docker build -t php-sdk-image:latest .

.PHONY: auto-test
auto-test: build
	docker run --rm php-sdk-image:latest bash /app/auto_test.sh

.PHONY: before-release-test
before-release-test: build
	docker run --rm \
	  -e API_KEY="$$API_KEY" \
	  -e API_SECRET="$$API_SECRET" \
	  -e API_PASSPHRASE="$$API_PASSPHRASE" \
	  -e USE_LOCAL="true" \
	  php-sdk-image:latest \
	  bash /app/release_test.sh

.PHONY: after-release-test
after-release-test: build
	docker run --rm \
	  -e API_KEY="$$API_KEY" \
	  -e API_SECRET="$$API_SECRET" \
	  -e API_PASSPHRASE="$$API_PASSPHRASE" \
	  php-sdk-image:latest \
	  bash /app/release_test.sh

.PHONY: run-forever-test
run-forever-test: build
	docker rm -f php-run-forever-test
	docker run -idt \
	  -e API_KEY="$$API_KEY" \
	  -e API_SECRET="$$API_SECRET" \
	  -e API_PASSPHRASE="$$API_PASSPHRASE" \
	  --name php-run-forever-test \
	  php-sdk-image:latest \
	  bash /app/run_forever_test.sh

.PHONY: reconnect-test
reconnect-test: build
	docker rm -f php-reconnect-test
	docker run -idt \
	  -e API_KEY="$$API_KEY" \
	  -e API_SECRET="$$API_SECRET" \
	  -e API_PASSPHRASE="$$API_PASSPHRASE" \
	  --name php-reconnect-test --network isolated_net \
	  php-sdk-image:latest \
	  bash /app/ws_reconnect_test.sh


VERSIONS = 7.4 8.0 8.1 8.2
.PHONY: php-version-test
php-version-test:
	@for v in $(VERSIONS); do \
    	  echo "---- PHP $$v ----"; \
    	  docker build --build-arg PHP_RUNTIME=$$v -t php-sdk-image:$$v . ; \
    	  docker run --rm \
    	    -e API_KEY="$$API_KEY" \
    	    -e API_SECRET="$$API_SECRET" \
    	    -e API_PASSPHRASE="$$API_PASSPHRASE" \
    	    -e USE_LOCAL="true" \
    	    php-sdk-image:$$v bash /app/release_test.sh || exit $$? ; \
    done