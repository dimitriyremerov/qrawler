SHELL=/bin/bash
TARGET=target
HOSTNAME=qrawler.qask.ru

all: init build-image build image

init:
	mkdir -p $(TARGET)
	cp -r docker/build/* $(TARGET)/

build-image:
	docker build -t qrawler/php-build .
	$(MAKE) -C ui build-image

build: php
	TARGET=`realpath $(TARGET)` $(MAKE) -C ui build

php:
	docker rm -f qrawler-php-build || true
	docker run -it --name qrawler-php-build qrawler/php-build
	docker cp qrawler-php-build:/app $@
	mkdir -p $(TARGET)/php/var
	rm -rf $(TARGET)/php/var/www
	mv $@ $(TARGET)/php/var/www
	mkdir -p $(TARGET)/mysql/docker-entrypoint-initdb.d
	docker cp qrawler-php-build:/qrawler.sql $(TARGET)/mysql/docker-entrypoint-initdb.d/qrawler.sql

image: init
	docker build -t qrawler/mysql $(TARGET)/mysql
	docker build -t qrawler/nginx $(TARGET)/nginx
	docker build -t qrawler/php $(TARGET)/php

clean-all: clean-container-all clean clean-image

clean: clean-container
	rm -rf $(TARGET)
	docker rm -f qrawler-php-build || true
	$(MAKE) -C ui clean

clean-container:
	docker rm -f qrawler qrawler-fpm qrawler-daemon || true

clean-container-all: clean-container
	docker rm -f qrawler-db || true

clean-image:
	docker rmi qrawler/mysql qrawler/nginx qrawler/php || true

recreate: clean-container build image start

update: clean all start

start:
	@docker network create -d bridge --subnet 10.0.0.0/24 --gateway 10.0.0.1 qrawler 2>/dev/null || true
	@docker start qrawler-db 2>/dev/null || docker run -d --network=qrawler --name qrawler-db qrawler/mysql
	@docker start qrawler-fpm 2>/dev/null || docker run -d --network=qrawler --name qrawler-fpm qrawler/php
	@docker start qrawler 2>/dev/null || docker run -d --network=qrawler --name qrawler -p 80:80 qrawler/nginx || echo "It's likely a web server is already listening on port 80 or 443. Please check and shut down the server"
	@docker start qrawler-daemon 2>/dev/null || docker run -d --network=qrawler --name qrawler-daemon qrawler/php bin/console crawler:daemon

restart:
	@docker restart qrawler || true
	@docker restart qrawler-fpm || true
	@docker restart qrawler-db || true
	@docker restart qrawler-daemon || true

start-dev:
	docker rm -f qrawler-fpm || true
	docker run -d --network=qrawler --name qrawler-fpm -v $$(pwd):/var/www qrawler/php
	docker rm -f qrawler-daemon || true
	docker run -d --network=qrawler --name qrawler-daemon -v $$(pwd):/var/www qrawler/php php bin/console crawler:daemon

stop:
	docker stop qrawler qrawler-db qrawler-fpm qrawler-daemon
