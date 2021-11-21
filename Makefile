init: docker-stop docker-pull docker-build docker-start db-init

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
	docker compose run --rm artisan php artisan migrate:refresh --seed

build:
	docker build -f docker/heroprotocol/Dockerfile -t hotsapi/heroprotocol .
	docker build -f docker/Dockerfile.parser -t hotsapi/parser .
	docker build -f docker/Dockerfile.hotsapi -t hotsapi/hotsapi .
	docker build -f docker/Dockerfile.webserver -t hotsapi/webserver .
