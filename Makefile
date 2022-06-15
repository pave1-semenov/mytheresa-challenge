# Executables (local)
DOCKER_COMP = docker-compose

# Docker containers
APP_CONT = $(DOCKER_COMP) exec app

# Executables
PHP      = $(APP_CONT) php
COMPOSER = $(APP_CONT) composer
SYMFONY  = $(APP_CONT) bin/console
PHPUNIT  = $(APP_CONT) bin/phpunit

# Misc
.DEFAULT_GOAL = help
.PHONY        = help build up start down clear sh sf cc import migrations bootstrap

# Import defaults
BATCH_SIZE = 500 # defaults
FILE_PATH = ./resources/products.json
DISCOUNTS_FILE_PATH = ./resources/discounts.json

## —— 🎵 🐳 The Symfony-docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up -d

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

clean: ## Stop the docker hub, removing volumes
	@$(DOCKER_COMP) down -v --remove-orphans

sh: ## Connect to the PHP FPM container
	@$(APP_CONT) sh

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

## —— App 💾 ———————————————————————————————————————————————————————————————————
migrations: c=doctrine:migrations:migrate --no-interaction ## Run the database migrations
migrations: sf

import: ## Start data import from default files. Available parameters: FILE_PATH, DISCOUNTS_FILE_PATH, BATCH_SIZE
	@$(SYMFONY) app:import -vvv --batch_size=$(BATCH_SIZE) --file=$(FILE_PATH) --discounts_file=$(DISCOUNTS_FILE_PATH)

bootstrap: start migrations import ## Starts the application and imports default data
test: ## Runs unit tests
	@$(PHPUNIT)
