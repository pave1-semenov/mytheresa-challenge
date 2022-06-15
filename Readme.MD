# Overview

This is a challenge task for [Mytheresa](https://www.mytheresa.com/)

Application requires Docker and Docker Compose to run. Setup contains 3 containers:
* app - application container with PHP-FPM (version 8.1);
* nginx - web server container;
* database - RDBMS container with PostgreSQL to store data.

Features:
* Storing data in RDBMS to decrease application startup time and memory footprint;
* Data can be imported as many times as needed. Application manages data updates/inserts when new file is imported;
* Discounts can be imported from file as many times as needed;
* Import process utilizes batch data processing to decrease memory footprint in case of large file. However, it still can be improved using multi-value inserts;
* Advanced request parsing and validation.
## Installation

* Clone the repository ``git clone git@github.com:pave1-semenov/mytheresa-challenge.git``
* Run ```make bootstrap```. This command will create and run all the needed containers and import the initial data from repository;
* Application will start on ```http://localhost:80``` by default and expose ```GET /products``` endpoint.

## Documentation

Application provides OpenAPI 3 documentation on endpoint ```GET /api/doc```

## Configuration
Application can be configured via environment variables:
* *POSTGRES_USER* - database username. Used in app and database containers. Default value ```challenger```;
* *POSTGRES_PASSWORD* - database password. Used in app and database containers. Default value ```changeMe```;
* *POSTGRES_DB* - database to create on bootstrap and connect to; Used in app and database containers. Default value ```mytheresa```;
* *POSTGRES_HOST* - database host. Used in app. Default value ```database```;
* *DATABASE_URL* - full database DSN to connect. Used in app. Default value ```postgresql://${POSTGRES_USER}:${POSTGRES_PASSWORD}@${POSTGRES_HOST}:5432/${POSTGRES_DB}?serverVersion=13&charset=utf8```;
* *NGINX_HOST* - host, which NGINX will use as server_name. Used in nginx and app (to use in /api/doc endpoint). Default value ```localhost```;
* *NGINX_PORT* - port, on which NGINX will serve the requests. Default value ```80```;
* *APP_ENV* - application environment. Default value: ```prod```;
* *APP_DEFAULT_CURRENCY* - default currency for products. Default value ```EUR```;
* *PHPFPM_MAX_CHILDREN*, *PHPFPM_START_SERVERS*, *PHPFPM_MIN_SPARE_SERVERS*, *PHPFPM_MAX_SPARE_SERVERS* - PHP-FPM runtime parameters;

## Commands
* ```make help``` - commands summary;
* ```make bootstrap``` - create all containers, applying migrations and importing data;
* ```make build``` - build all docker containers;
* ```make up``` - start all docker containers;
* ```make down``` - stop all docker containers (saving persistent volumes);
* ```make clean``` - stop all docker containers and erase all data;
* ```make sh``` - connect to the app containers shell;
* ```make sf``` - list all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about;
* ```make cc``` - clear symfony cache;
* ```make migrations``` - execute database migrations;
* ```make import``` - starts data import. Available parameters: 
  * ```FILE_PATH``` - path to json file with products. File should be accessible from inside app container or be a web link;
  * ```DISCOUNTS_FILE_PATH``` - path to json file with discounts. File should be accessible from inside app container or be a web link;
  * ```BATCH_SIZE``` - Maximum number of database operations to process in one transaction.
* ```make test``` - run unit tests;
* ```make sleep``` - sleep 10 seconds.

## Tests
* Run ```make test```