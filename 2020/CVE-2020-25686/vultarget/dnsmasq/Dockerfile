FROM alpine:3.13

WORKDIR /build
RUN apk --no-cache add linux-headers nettle-dev alpine-sdk
ADD http://www.thekelleys.org.uk/dnsmasq/dnsmasq-2.82.tar.gz /build

RUN tar zxvf dnsmasq-2.82.tar.gz
RUN cd dnsmasq-2.82 && \
    sed -ie 's/TIMEOUT 10/TIMEOUT 1800/' src/config.h && \
#    sed -ie 's/FTABSIZ 150/FTABSIZ 2000/' src/config.h && \
#    sed -ie 's/RANDOM_SOCKS 64/RANDOM_SOCKS 500/' src/config.h && \
    make && make install

VOLUME /etc/dnsmasq

EXPOSE 53 53/udp

ENTRYPOINT ["dnsmasq", "-k", "--log-facility=-"]
