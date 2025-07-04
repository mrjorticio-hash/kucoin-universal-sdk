FROM maven:3.9.10-amazoncorretto-17-debian-bookworm  AS builder
WORKDIR /src
COPY . .
RUN --mount=type=cache,target=/root/.m2,sharing=locked mvn clean install -DskipTests

FROM maven:3.9.10-amazoncorretto-17-debian-bookworm
WORKDIR /app
COPY . /src
COPY script /app
COPY --from=builder /src/target/*.jar /tmp/