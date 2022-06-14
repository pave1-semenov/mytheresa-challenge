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
.PHONY        = help build up start down clear logs sh sf cc import bootstrap

# Import defaults
BATCH_SIZE = 500 # defaults
FILE_PATH = ./resources/products.json
DISCOUNTS_FILE_PATH = ./resources/discounts.json

IMPORT_FLAGS = --batch_size=$(BATCH_SIZE) --file=$(FILE_PATH) --discounts_file=$(DISCOUNTS_FILE_PATH)

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

clear: ## Stop the docker hub, removing volumes
	@$(DOCKER_COMP) down -v --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 -f

sh: ## Connect to the PHP FPM container
	@$(APP_CONT) sh

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

## —— App 💾 ———————————————————————————————————————————————————————————————————

import: c=app:import -vvv $(IMPORT_FLAGS) ## Start data import from default files
import: sf

bootstrap: start import ## Starts the application and imports default data. Use -e FILE_PATH=/path/to/products.json, -e DISCOUNTS_FILE_PATH=/path/to/discounts.json, -e BATCH_SIZE=1000 to override the defaults
test: ## Runs unit tests
	@$(PHPUNIT)