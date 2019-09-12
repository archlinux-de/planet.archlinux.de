.EXPORT_ALL_VARIABLES:
.PHONY: all init start start-db stop clean rebuild install shell-php shell-node test test-db test-db-migrations test-coverage test-db-coverage test-ci ci-build ci-update ci-update-commit deploy

UID!=id -u
GID!=id -g
COMPOSE=UID=${UID} GID=${GID} docker-compose -f docker/docker-compose.yml -p planet_archlinux_de
COMPOSE-RUN=${COMPOSE} run --rm -u ${UID}:${GID}
PHP-DB-RUN=${COMPOSE-RUN} php
PHP-RUN=${COMPOSE-RUN} --no-deps php
NODE-RUN=${COMPOSE-RUN} --no-deps encore
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
	${NODE-RUN} yarn install

shell-php:
	${PHP-DB-RUN} bash

shell-node:
	${NODE-RUN} bash

test:
	${PHP-RUN} composer validate
	${PHP-RUN} vendor/bin/phpcs
	${NODE-RUN} node_modules/.bin/standard 'assets/js/**/*.js' '*.js'
	${NODE-RUN} node_modules/.bin/stylelint 'assets/css/**/*.scss' 'assets/css/**/*.css'
	${PHP-RUN} bin/console lint:yaml config
	${PHP-RUN} bin/console lint:twig templates
	${PHP-RUN} vendor/bin/phpstan analyse
	${PHP-RUN} vendor/bin/phpunit

test-db: start-db
	${PHP-DB-RUN} vendor/bin/phpunit -c phpunit-db.xml

test-db-migrations: start-db
	${PHP-DB-RUN} vendor/bin/phpunit -c phpunit-db.xml --testsuite 'Doctrine Migrations Test'

test-coverage:
	${PHP-RUN} phpdbg -qrr -d memory_limit=-1 vendor/bin/phpunit --coverage-html var/coverage

test-db-coverage: start-db
	${PHP-RUN} phpdbg -qrr -d memory_limit=-1 vendor/bin/phpunit --coverage-html var/coverage -c phpunit-db.xml

test-ci:
	${NODE-RUN} node_modules/.bin/encore production
	${MAKE} test
	${MAKE} test-db
	${PHP-RUN} bin/console security:check

ci-build: install
	${MAKE} test-ci

ci-update-commit:
	git add -A
	git config --local user.name "Maintenance Bob"
	git config --local user.email "bob@archlinux.de"
	git commit -m"Update dependencies"
	git remote add origin-push https://$${GITHUB_ACTOR}:$${GITHUB_TOKEN}@github.com/$${GITHUB_REPOSITORY}.git
	[ -n "$${GITHUB_TOKEN}" ] && git push --set-upstream origin-push $$(git rev-parse --abbrev-ref HEAD)

ci-update:
	git checkout $$(git rev-parse --abbrev-ref HEAD)
	${PHP-RUN} composer --no-interaction update
	${PHP-RUN} rm -rf var/cache/*
	${NODE-RUN} yarn upgrade --latest
	${MAKE} test-ci
	git diff-index --quiet HEAD || ${MAKE} ci-update-commit

deploy:
	yarn install
	yarn run encore production
	composer --no-interaction install --prefer-dist --no-dev --optimize-autoloader
	bin/console cache:clear
	bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
