init: init-ci 
	
init-ci: docker-down-clear \
	docker-pull docker-build docker-up \
	api-init

up: docker-up
down: docker-down
restart: down up

images:
	docker images

prune:
	docker system prune -af --volumes

memory:
	sudo sh -c "echo 3 > /proc/sys/vm/drop_caches"

docker-up:
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

docker-pull:
	docker compose pull

docker-build:
	docker compose build --pull

api-init: api-permissions

api-permissions:
	docker run --rm -v ${PWD}/test-app:/app -w /app alpine chmod -R 777 storage bootstrap

api-composer-install:
	docker compose run --rm api-php-cli composer install

tests:
	docker compose run --rm api-php-cli php artisan test

test:
	docker compose run --rm api-php-cli ./vendor/bin/phpunit

tests-coverage:
	docker compose run --rm api-php-cli vendor/bin/phpunit --coverage-html reports/

psalm:
	docker compose run --rm api-php-cli ./vendor/bin/psalm --show-info=true

lint:
	docker compose run --rm api-php-cli ./vendor/bin/pint

analyze:
	docker compose run --rm api-php-cli ./vendor/bin/psalm


build: build-api 

build-api:
	docker --log-level=debug build --pull --file=test-app/docker/production/nginx/Dockerfile --tag=${REGISTRY}/test-app:${IMAGE_TAG} test-app
	docker --log-level=debug build --pull --file=test-app/docker/production/php-fpm/Dockerfile --tag=${REGISTRY}/api-php-fpm:${IMAGE_TAG} test-app
	docker --log-level=debug build --pull --file=test-app/docker/production/php-cli/Dockerfile --tag=${REGISTRY}/api-php-cli:${IMAGE_TAG} test-app

try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build

push: push-api


push-api:
	docker push ${REGISTRY}/test-app:${IMAGE_TAG}
	docker push ${REGISTRY}/api-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY}/api-php-cli:${IMAGE_TAG}

deploy:
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'rm -rf site_${BUILD_NUMBER} && mkdir site_${BUILD_NUMBER}'
	envsubst < docker-compose-production.yml > docker-compose-production-env.yml
	scp -o StrictHostKeyChecking=no -P ${PORT} docker-compose-production-env.yml deploy@${HOST}:site_${BUILD_NUMBER}/docker-compose.yml
	rm -f docker-compose-production-env.yml
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'mkdir site_${BUILD_NUMBER}/secrets'
	scp -o StrictHostKeyChecking=no -P ${PORT} ${API_DB_PASSWORD_FILE} deploy@${HOST}:site_${BUILD_NUMBER}/secrets/api_db_password
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker stack deploy  --compose-file docker-compose.yml server --with-registry-auth --prune'

deploy-clean:
	rm -f docker-compose-production-env.yml

rollback:
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker stack deploy --compose-file docker-compose.yml auction --with-registry-auth --prune'
