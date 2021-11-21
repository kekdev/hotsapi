init: docker-stop docker-pull docker-build install-deps docker-start

start: docker-start
stop: docker-stop

docker-pull:
	docker compose pull

docker-build:
	docker compose build --pull

docker-start:
	docker compose up -d

docker-stop:
	docker compose down --remove-orphans

db-init:
	docker compose run --rm php-cli php artisan migrate:refresh --seed

install-deps:
	docker compose run --rm php-cli composer install

build:
	docker build -f docker/heroprotocol/Dockerfile -t hotsapi/heroprotocol .
	docker build -f docker/Dockerfile.parser -t hotsapi/parser .
	docker build -f docker/Dockerfile.hotsapi -t hotsapi/hotsapi .
	docker build -f docker/Dockerfile.webserver -t hotsapi/webserver .
