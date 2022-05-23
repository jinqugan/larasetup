up:
	docker compose up -d
down:
	docker compose down --remove-orphans
build:
	docker compose up -d --build
# setup for first time project created
setup:
	cp .env.example .env
	docker compose up -d --build
	docker compose exec app composer install
	docker compose exec app cp .env.example .env
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan storage:link
	docker compose exec app chmod -R 777 storage bootstrap/cache
	@make fresh
# rerun project and set back to fresh project
reset:
	@make cache-clear
	docker compose exec app composer install
	@make fresh
remake:
	@make destroy
	@make init
restart:
	@make down
	@make up
destroy:
	docker compose down --rmi all --volumes --remove-orphans
destroy-volumes:
	docker compose down --volumes --remove-orphans
build-v:
	@make destroy-volumes
	@make build
web:
	docker compose exec web bash
app:
	docker compose exec app bash
fresh:
	docker compose exec app php artisan migrate:fresh --seed
	docker compose exec app php artisan passport:install --force
cache-clear:
	docker compose exec app composer clear-cache
	docker compose exec app php artisan optimize:clear
	docker compose exec app php artisan event:clear