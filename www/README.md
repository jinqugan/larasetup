# docker-laravel

## Introduction

Build a simple laravel development environment with docker-compose.

## Usage

```bash
$ cd docker-laravel/www
$ ## modify www/.env.example for variable changes before build    container (optional)
$ make init
$ ## set local domain name (VIRTUAL_HOST in .env) at your own host file (/etc/hosts)
$ ## cd to infra/docker/nginx/nginx-proxy
$ make up
```

http://www.dockerlaravel.com

## Tips

## Container structures

```bash
├── app
├── web
└── db
```

### app container (php)

- Base image
  - [php](https://hub.docker.com/_/php):8.0-fpm-buster
  - [composer](https://hub.docker.com/_/composer):2.0

### web container (nginx)

- Base image
  - [nginx](https://hub.docker.com/_/nginx):1.20-alpine
  - [node](https://hub.docker.com/_/node):16-alpine

### db container (mysql)

- Base image
  - [mysql/mysql-server](https://hub.docker.com/r/mysql/mysql-server):8.0
