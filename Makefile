.PHONY: help up down build restart logs shell migrate seed test clean optimize

help:
	@echo "🚀 Comandos disponíveis:"
	@echo "  make up        - Subir containers"
	@echo "  make down      - Parar containers"
	@echo "  make build     - Build das imagens (sem cache)"
	@echo "  make restart   - Reiniciar containers"
	@echo "  make logs      - Ver logs em tempo real"
	@echo "  make shell     - Entrar no container da app"
	@echo "  make migrate   - Executar migrations"
	@echo "  make seed      - Executar seeders"
	@echo "  make test      - Executar testes"
	@echo "  make optimize  - Otimizar aplicação (caches)"
	@echo "  make clean     - Limpar volumes e containers"

up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose build --no-cache

restart:
	docker-compose restart

logs:
	docker-compose logs -f app

shell:
	docker-compose exec app sh

migrate:
	docker-compose exec app php artisan migrate --force

seed:
	docker-compose exec app php artisan db:seed --force

test:
	docker-compose exec app php artisan test

optimize:
	docker-compose exec app php artisan config:cache
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache
	docker-compose exec app php artisan optimize

clean:
	docker-compose down -v
	docker system prune -f

