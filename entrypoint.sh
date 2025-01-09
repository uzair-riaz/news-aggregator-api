#!/bin/sh

if [ ! -f "vendor/autoload.php" ]; then
    composer install --no-progress --no-interaction
fi

if [ ! -f ".env" ]; then
    cp .env.example .env
fi

role=${ROLE:-app}

if [ "$role" = "app" ]; then
    php artisan migrate
    php artisan key:generate
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan sync:news &

    php artisan serve --host=0.0.0.0 --env=.env
    exec docker-php-entrypoint "$@"
else
    php artisan key:generate
    while [ true ]
    do
      php artisan schedule:run --verbose --no-interaction
      sleep 60
    done
fi
