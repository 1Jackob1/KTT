PHP := php

create-db:
	$(PHP) bin/console doctrine:database:create

drop-db:
	$(PHP) bin/console doctrine:database:drop --force --no-debug

reload-db:
	$(MAKE) -f $(THIS_FILE) drop-db
	$(MAKE) -f $(THIS_FILE) create-db
