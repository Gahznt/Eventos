ifeq ($(OS),Windows_NT)
	EXECUTABLE=winpty
else
	EXECUTABLE=
endif

build: ## Build php-fpm
	docker-compose build

install: ## Run composer, install vendor
	make build && make clear && $(EXECUTABLE) docker-compose exec php-fpm bash -c "composer install && yarn install && yarn dev && php bin/console doctrine:migrations:migrate"

start: ## Up containers
	docker-compose up -d

stop: ## Stop containers
	docker-compose stop

shell: ## Access bash in, php-fpm container
	make clear && $(EXECUTABLE) docker-compose exec php-fpm bash

hmr: ## Publish HMR for frontend
	make clear && rm -rf events/node_modules && yarn --cwd events && yarn --cwd events dev-server --port 8888

webserver-debug: ## Debug webserver container
	make clear && docker-compose logs -f webserver;

php-debug: ## Debug php-fpm container
	make clear && docker-compose logs -f php-fpm

clear: ## Start and clear
	clear && make start

release-build: ## Release docker container images
	docker-compose build --no-cache && make clear && $(EXECUTABLE) docker-compose exec php-fpm bash -c "composer install && yarn install"

test: ## Run updates test assets
	docker-compose down && make clear && $(EXECUTABLE) docker-compose exec php-fpm bash -c "composer install && yarn install && yarn dev && php bin/console doctrine:migrations:migrate -q && php bin/console doctrine:fixtures:load -q"

help: ## This help.
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.DEFAULT_GOAL := help
