#!/bin/sh

until nc -z -v -w30 $DB_HOST $DB_PORT
do
  echo "Aguardando conexão com o banco de dados em $DB_HOST:$DB_PORT..."
  sleep 1
done

echo "O banco de dados está ativo. Executando comandos do Laravel..."

composer install --no-interaction --prefer-dist --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force
