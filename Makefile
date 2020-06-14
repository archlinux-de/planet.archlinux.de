.EXPORT_ALL_VARIABLES:
.PHONY: all init start start-db stop clean rebuild install shell-php shell-node test test-db test-db-migrations test-coverage test-db-coverage test-security fix-code-style update deploy

UID!=id -u
GID!=id -g
COMPOSE=UID=${UID} GID=${GID} docker-compose -f docker/docker-compose.yml -p planet_archlinux_de
COMPOSE-RUN=${COMPOSE} run --rm -u ${UID}:${GID}
PHP-DB-RUN=${COMPOSE-RUN} api
PHP-RUN=${COMPOSE-RUN} --no-deps api
NODE-RUN=${COMPOSE-RUN} --no-deps -e DISABLE_OPENCOLLECTIVE=true app
MARIADB-RUN=${COMPOSE-RUN} --no-deps mariadb

all: install

init: start
	${PHP-DB-RUN} bin/console cache:warmup
	${PHP-DB-RUN} bin/console doctrine:database:create
	${PHP-DB-RUN} bin/console doctrine:schema:create
	${PHP-DB-RUN} bin/console doctrine:migrations:version --add --all --no-interaction
	${PHP-DB-RUN} bin/console app:update:feeds

start:
	${COMPOSE} up -d
	${MARIADB-RUN} mysqladmin -uroot --wait=10 ping

start-db:
	${COMPOSE} up -d mariadb
	${MARIADB-RUN} mysqladmin -uroot --wait=10 ping

stop:
	${COMPOSE} stop

clean:
	${COMPOSE} down -v
	git clean -fdqx -e .idea

rebuild: clean
	${COMPOSE} build --pull
	${MAKE} install
	${MAKE} init
	${MAKE} stop

install:
	${PHP-RUN} composer --no-interaction install
	${NODE-RUN} yarn install --non-interactive --frozen-lockfile

shell-php:
	${PHP-DB-RUN} bash

shell-node:
	${NODE-RUN} bash

test:
	${PHP-RUN} composer validate
	${PHP-RUN} vendor/bin/phpcs
	${NODE-RUN} node_modules/.bin/eslint src --ext js --ext vue
	${NODE-RUN} node_modules/.bin/stylelint 'src/assets/css/**/*.scss' 'src/assets/css/**/*.css' 'src/**/*.vue'
	${NODE-RUN} node_modules/.bin/jest
	${PHP-RUN} bin/console lint:yaml config
	${PHP-RUN} bin/console lint:twig templates
	${NODE-RUN} yarn build --dest $(shell mktemp -d)
	${PHP-RUN} vendor/bin/phpstan analyse
	${PHP-RUN} vendor/bin/phpunit

test-db: start-db
	${PHP-DB-RUN} vendor/bin/phpunit -c phpunit-db.xml

test-db-migrations: start-db
	${PHP-DB-RUN} vendor/bin/phpunit -c phpunit-db.xml --testsuite 'Doctrine Migrations Test'

test-coverage:
	${NODE-RUN} node_modules/.bin/jest --coverage --coverageDirectory var/coverage/jest
	${PHP-RUN} phpdbg -qrr -d memory_limit=-1 vendor/bin/phpunit --coverage-html var/coverage/phpunit

test-db-coverage: start-db
	${PHP-RUN} phpdbg -qrr -d memory_limit=-1 vendor/bin/phpunit --coverage-html var/coverage -c phpunit-db.xml

test-security:
	${PHP-RUN} bin/console security:check
	${NODE-RUN} yarn audit --groups dependencies

fix-code-style:
	${PHP-RUN} vendor/bin/phpcbf || true
	${NODE-RUN} node_modules/.bin/eslint src --fix --ext js --ext vue
	${NODE-RUN} node_modules/.bin/stylelint --fix 'src/assets/css/**/*.scss' 'src/assets/css/**/*.css' 'src/**/*.vue'

update:
	${PHP-RUN} composer --no-interaction update
	${PHP-RUN} composer --no-interaction update --lock --no-scripts
	${NODE-RUN} yarn upgrade --non-interactive

deploy:
	yarn install --non-interactive --frozen-lockfile --prod
	yarn build --modern
	find public/build -type f -mtime +30 -delete
	find public/build -type d -empty -delete
	composer --no-interaction install --prefer-dist --no-dev --optimize-autoloader
	composer dump-env prod
	bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
