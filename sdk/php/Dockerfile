FROM php:8.1-cli AS builder
WORKDIR /src
RUN apt-get update && apt-get install -y unzip git jq \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY . .
ARG SDK_VERSION=0.0.1-alpha

RUN cp composer.json composer.json.bak \
    && jq --arg ver "$SDK_VERSION" '. + {version: $ver}' composer.json.bak > composer.json
RUN rm -f composer.lock
RUN composer install --no-dev --optimize-autoloader && \
    mkdir -p /build_out && \
    composer archive --format=tar --dir=/build_out

FROM php:8.1-cli
WORKDIR /app
RUN apt-get update && apt-get install -y unzip git  libzip-dev \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install zip
COPY . /src
COPY example /app/example
COPY script /app
COPY --from=builder /build_out /tmp