.PHONY: *

help:
	@printf "\033[33mComo usar:\033[0m\n  make [comando] [arg=\"valor\"...]\n\n\033[33mComandos:\033[0m\n"
	@grep -E '^[-a-zA-Z0-9_\.\/]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[32m%-30s\033[0m %s\n", $$1, $$2}'

bash: ## Limpar todas as bases de dados
	docker-compose exec cobrancas bash

up: ## Subindo todos os containers
	docker-compose up -d --force-recreate

supervisor: ## Re-criando container do supervisor
	docker-compose up -d --force-recreate cobrancas_supervisor

migrate: ## Migrando todas as tabelas
	docker-compose exec cobrancas php artisan migrate

fresh: ## Limpando todas as tabelas
	docker-compose exec cobrancas php artisan migrate:fresh --seed
