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

Usage
-
####Users and Tasks
* To create user|task you can request `POST api/(users|tasks)`, request body like fields in `(User|Task)FormType.php`
* To update user|task you can request `PATCH api/(users|tasks)` , request body like fields in `(User|Task)FormType.php`

####Sessions
* To start session you can request `POST api/sessions/start`
* To close session you can request `POST api/sessions/stop`

####Common
* To get entity you can request `GET api/(users|tasks|sessions)`, you can specify `_per_page` and `_page` to navigate and `filter` to filter values. In `filter` field you must specify entity fields
* To delete entity you can request `DELETE api/(users|tasks|sessions)`, produce only entity `id`

