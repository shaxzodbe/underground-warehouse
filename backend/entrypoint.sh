#!/bin/bash
set -e

if [ ! -f "vendor/autoload.php" ]; then
    echo "Vendor autoload not found. Installing dependencies..."
    composer install --no-interaction --optimize-autoloader
else
    echo "Dependencies already installed."
fi

echo "Waiting for database connection..."
max_tries=30
tries=0
until php -r "try { new PDO('mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME'), getenv('DB_USER'), getenv('DB_PASSWORD')); exit(0); } catch (PDOException \$e) { exit(1); }" > /dev/null 2>&1; do
    if [ $tries -ge $max_tries ]; then
        echo "Database connection failed after $max_tries attempts."
        exit 1
    fi
    tries=$((tries+1))
    echo "Database is unavailable - sleeping ($tries/$max_tries)"
    sleep 2
done
echo "Database connection established."

echo "Running migrations..."
php yii migrate --interactive=0

echo "Starting background jobs..."
php yii job/start

exec "$@"
