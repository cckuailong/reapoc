FROM python:3.9-alpine as compile
WORKDIR /opt
RUN apk add --no-cache gcc musl-dev python3-dev libffi-dev openssl-dev cargo
RUN python3 -m pip install virtualenv
RUN python -m venv venv
ENV PATH="/opt/venv/bin:$PATH"
RUN wget https://github.com/SecureAuthCorp/impacket/archive/refs/tags/impacket_0_9_23.tar.gz -O impacket.tar.gz
RUN tar x -zvf impacket.tar.gz
RUN mv impacket-impacket_0_9_23 impacket
RUN pip install impacket/


FROM python:3.9-alpine

COPY --from=compile /opt/venv /opt/venv
COPY smb.conf main.py my_rprn.py /app/

WORKDIR /app

RUN apk add --no-cache samba && mkdir "/share"

EXPOSE 445
VOLUME /share

ENV PATH="/opt/venv/bin:$PATH"