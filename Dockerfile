FROM php:8.2-fpm-alpine

RUN apk update
RUN docker-php-ext-install pdo pdo_mysql
RUN curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

WORKDIR /app
COPY . .
COPY .env.example .env
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
