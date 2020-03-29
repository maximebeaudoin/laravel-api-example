# Laravel API example

[![CircleCI](https://circleci.com/gh/maximebeaudoin/laravel-api-example.svg?style=shield)](https://circleci.com/gh/maximebeaudoin/laravel-api-example)

## What this example contains

1. Api endpoints
1. CRUD operations using services
1. Domain Driven application structure with entity and repositories
1. Unit testing with phpunit
1. Acceptance testing with behat
1. Laravel Sanctum integration for authentication
1. Application policies to check user permissions
1. Code quality control using tools like CodeSniffer
1. CircleCi integration for running test and code quality control
1. Docker compose for easy application setup
1. Composer helpful scripts


## Quick runtime environment to test the api
For convinience, we provide a `docker-compose.yml` file to quickly start a working environment. The file include `php-fpm`, `nginx
and a `mysql` database.

__Note__ : The application will run on `http://localhost:8000`. If you want yo change the port, simply edit the `docker-compose.yml` file at the root of the project.

### Environment variables
The first step is to copy the `.env.example` file to `.env`. The example file is already configure to work with the docker-compose.yml configurations.

### Commands to start the environment
```
## Launch containers
docker-compose up

## Migrate the dabase
docker-compose exec php  php artisan migrate:fresh
docker-compose exec php  php artisan db:seed

## Voilà !
```
