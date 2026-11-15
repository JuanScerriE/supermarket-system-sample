#!/bin/sh
set -e

# create + seed the SQLite database on first start (it lives in a volume)
if [ ! -f database/app.sqlite ]; then
  php bin/db create
  php bin/db seed
fi

exec "$@"
