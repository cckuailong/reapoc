# 10.0.0 ~ 10.23.0
# 12.0.0 ~ 12.20.0
# 14.0.0 ~ 14.15.3
# 15.0.0 ~ 15.5.0
FROM node:15.5.0

WORKDIR /poc

COPY package.json package.json
COPY package-lock.json package-lock.json
RUN npm ci

COPY ./src .
