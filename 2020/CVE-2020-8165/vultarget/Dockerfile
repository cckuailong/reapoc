FROM ruby:2.5.1-alpine

RUN apk add \
	bash \
	git \
	nodejs \
	sqlite-dev \
	tzdata

ADD ./app /app

WORKDIR /app

RUN apk add --no-cache --virtual .build-deps \
		linux-headers \
		build-base && \
	bundle install && \
	apk del .build-deps

ENTRYPOINT ["bash", "-c"]

CMD ["bundle", "exec", "rails", "server", "--binding", "0.0.0.0", "--port", "3000"]
