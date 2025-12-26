#!/bin/sh
set -e

if [ ! -d "node_modules" ] || [ -z "$(ls -A node_modules)" ]; then
    echo "node_modules not found or empty. Installing dependencies..."
    npm install
else
    echo "node_modules already exists."
fi

exec "$@"
