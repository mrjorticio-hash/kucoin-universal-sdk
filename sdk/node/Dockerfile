FROM node:20-slim AS builder

WORKDIR /src

COPY package*.json ./
RUN npm ci --ignore-scripts

COPY src ./src
COPY tsconfig.json .
RUN npm run build
RUN mkdir -p /pkg_out && \
    npm pack --pack-destination /pkg_out

FROM node:20-slim

WORKDIR /app

RUN apt-get update && \
    apt-get install -y --no-install-recommends procps jq && \
    rm -rf /var/lib/apt/lists/*

COPY . /src
COPY example  ./example
COPY script/* ./

COPY --from=builder /pkg_out/*.tgz /tmp/