#!/bin/sh

until nc -z -v -w30 $DB_HOST $DB_PORT
do
  echo "Waiting for database connection on $DB_HOST:$DB_PORT..."
  sleep 1
done

echo "Database is up and running. Executing Laravel commands..."

php artisan key:generate
php artisan migrate
php artisan migrate:fresh --seed
#php artisan db:seed --class=AdminSeeder
#php artisan db:seed --class=BaseSeeder
