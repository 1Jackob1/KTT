Workey
=

A simple (too simple), task tracking system.

Installation
-

* Clone or download zip archive on your machine
* Create `.env.local` and `.env.test.local`
* Run in your bash
```bash
make preparations
```
* Initialize a server (one of them, or all, you are free to choice)
    * Apache
    * NGINX
    * Symfony local server

Testing
-
To run test you must initialize test database first. \
* Configure connection
* Run `make test-init-db && make run-tests`
