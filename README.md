# DEMO API DEPLOYMENT

This is basic laravel framework with few extra routes and jobs. It needs to be built and deployed according to this document.

- [Infrastructure Requirements](#infrastructure-requirements)
- [Server Requirements](#server-requirements)
    - [Common Server Requirements](#common-server-requirements)
    - [API Server requirements](#api-server-requirements)
    - [Worker Server requirements](#worker-server-requirements)
- [CI/CD stages](#ci-cd-stages)
    - [Build stage](#build-stage)
    - [Deploy stage](#deploy-stage)
- [Debug](#debug)
- [Extras](#extras)
    - [Nginx config example](#nginx-config-example)
    - [Supervisor config example](#supervisor-config-example)

## Infrastructure Requirements

- Load Balancer
    - without sticky sessions
    - traffic port 80
- MYSQL 5.7 server
- REDIS 6.0 server
- 2 API servers under load balancer
    - traffic port 80
    - ssh port 22
- 1 WORKER server
    - ssh port 22
- Blue-Green deployment is a must for API and WORKER instances
- SSH access to connect to each API and WORKER servers

## Server Requirements

### Common Server Requirements

- PHP version and extensions:
    - PHP >= 7.4
    - BCMath PHP Extension
    - Ctype PHP Extension
    - Fileinfo PHP Extension
    - JSON PHP Extension
    - Mbstring PHP Extension
    - OpenSSL PHP Extension
    - PDO PHP Extension
    - Tokenizer PHP Extension
    - XML PHP Extension

### API Server requirements

- Common server requirements
- OPcache
- High performance PHP-FPM optimization.
- The latest stable version of the Nginx. [Nginx config example](#nginx-config-example).

### Worker Server requirements

- Common server requirements
- The latest stable version of the [Supervisor](http://supervisord.org/). [Supervisor config example](#supervisor-config-example).

## CI CD stages

This section will describe on what commands do we need to run on each stage. 

### Build stage

- pull the latest changes from the git
- .env file
  - create it from [.env.example](.env.example)
  - update DB_* and REDIS_* settings accordingly
- run `composer install --optimize-autoloader --no-dev` from project root directory
- Add build number or version to .env BUILD_VERSION
- use all content inside `./` as artifacts for deployment

### Deploy stage

Make sure that ``storage`` and ``boostrap/cache`` folders are writable by a server.

Run these commands from project root directory on each API and WORKER instance prior allowing traffic or starting supervisor:
- ``php artisan migrate --force``
- ``php artisan config:cache``
- ``php artisan view:cache``
- ``php artisan route:cache``

## Debug

| Path | Description |
| ----------- | ----------- |
| / | Displays message: "Working with Laravel v\*.\*.\* (PHP v7.4.*)" |
| /worker | Queues job for WORKER instance to process it and returns "queued" message |
| /models | Lists all the jobs worker worked on with job ID and Created at date time |
| /api | GET method for api to get {"build":""} |
| /api | POST method for api to send {"Title":"Title"} and get response {"title":"Title"} |

If server is not returning desired outcome you can check ``storage/logs/laravel.log`` file for more information with full stacktrace.

## Extras
Single php process memory usage is around 5 MB

### Nginx config example

```
server {
    listen 80;
    server_name example.com;
    root /path/to/project/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;

        add_header 'Access-Control-Allow-Origin' '*' always;
        add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS' always;
        add_header 'Access-Control-Allow-Headers' 'Accept,Content-Type' always;

        if ($request_method = 'OPTIONS') {
            add_header 'Access-Control-Allow-Origin' '*' always;
            add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS' always;
            add_header 'Access-Control-Allow-Headers' 'Accept,Content-Type' always;
            add_header 'Access-Control-Max-Age' 1728000 always;
            add_header 'Content-Type' 'text/plain charset=UTF-8 always';
            add_header 'Content-Length' 0 always;
            return 204;
        }
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Supervisor config example

```
[program:example-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/project/artisan queue:work
autostart=true
autorestart=true
user=root
numprocs=2
stopwaitsecs=3600
```
