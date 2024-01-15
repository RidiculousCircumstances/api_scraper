APP_UID=$(shell id -u)
APP_GID=$(shell id -g)


autoload:
	docker compose run web composer dump-autoload

run:
	docker compose up -d nginx web redis db consumer && docker exec -u 0 api_scraper-web chown -R $(APP_UID):$(APP_GID) /var/www/output && docker exec -u 0 api_scraper-web chmod -R 644 /var/www/output

build-admin-static:
	docker exec -u 0 api_scraper-web php bin/console assets:install --symlink

install-deps:
	docker exec -u 0 api_scraper-web composer install

migrate:
	docker exec -u 0 api_scraper-web php bin/console d:m:migrate

install:
	 cp ./.env-example .env && cp ./scraper/.env-example ./scraper/.env && make run && make install-deps && make build-admin-static && migrate

