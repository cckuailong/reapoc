FROM alpine:3.15.0 as build

RUN apk add --no-cache build-base

WORKDIR /build

COPY exploit.c /build

RUN gcc exploit.c -o exploit

FROM alpine:3.15.0

RUN adduser -g 100 -D 100

USER 100

WORKDIR /home/100

COPY --from=build --chown=100:100 /build/exploit /home/100/exploit

CMD /bin/sh
