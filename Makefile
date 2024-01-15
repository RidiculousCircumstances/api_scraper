APP_UID=$(shell id -u)
APP_GID=$(shell id -g)

#install: ## Install all app dependencies
#	docker compose run --rm --user $(APP_UID):$(APP_GID) --no-deps composer composer install --ansi --prefer-dist

echo: ## Install all app dependencies
	docker compose run -e APP_UID='111' -e APP_GID='222' composer echo $(APP_UID)

autoload:
	docker compose run web composer dump-autoload

up:
	docker compose up -d nginx web redis db consumer && docker exec -u 0 api_scraper-web chown -R 1000:1000 /var/www/output && docker exec -u 0 api_scraper-web chmod -R 777 /var/www/output