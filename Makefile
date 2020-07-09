PHP := php

create-db:
	$(PHP) bin/console doctrine:database:create

drop-db:
	$(PHP) bin/console doctrine:database:drop --force --no-debug

reload-db:
	$(MAKE) -f $(THIS_FILE) drop-db
	$(MAKE) -f $(THIS_FILE) create-db

migrations-diff:
	$(PHP) bin/console doctrine:migrations:diff

migrations-latest:
	$(PHP) bin/console doctrine:migrations:migrate latest

clear-cache:
	$(PHP) bin/console cache:clear --env=dev

test-init-db:
	$(PHP) bin/console doctrine:database:create --env=test --no-debug
	$(PHP) bin/console doctrine:migrations:migrate --env=test latest --no-interaction

test-reload-db:
	$(PHP) bin/console doctrine:database:drop --force --env=test --no-debug
	$(PHP) bin/console doctrine:database:create --env=test --no-debug
	$(PHP) bin/console doctrine:migrations:migrate --env=test latest --no-interaction

run-tests: fixtures-test
	$(PHP) bin/phpunit --testdox

sync-with-elastica:
	$(PHP) bin/console fos:elastica:populate

fixtures-dev:
	$(PHP) bin/console hautelook:fixtures:load --purge-with-truncate

fixtures-test:
	$(PHP) bin/console hautelook:fixtures:load --purge-with-truncate -e test
