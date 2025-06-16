# build
FROM python:3.12-slim AS builder
WORKDIR /src
COPY . .
RUN pip install --upgrade pip build && \
    python -m build --wheel --outdir /wheel_out

FROM python:3.12-slim
WORKDIR /app
RUN apt-get update && \
    apt-get install -y --no-install-recommends procps && \
    rm -rf /var/lib/apt/lists/*

COPY . /src
COPY test /app/test
COPY example  /app/example
COPY ./script /app
COPY --from=builder /wheel_out/*.whl /tmp/