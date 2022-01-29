#!/usr/bin/env bash
set -eu

# Store a backup of the current WordPress folder in case something goes wrong.
timestamp=$(date +%s)
cp -R wordpress backups/${timestamp}

# Clean the wordpress folder and keep the plugins & themes.
rm -Rf ./tmp
mkdir ./tmp
cp -R ./wordpress/wp-content/plugins ./tmp/plugins
cp -R ./wordpress/wp-content/themes ./tmp/themes
rm -Rf ./wordpress
mkdir -p ./wordpress/wp-content
mv ./tmp/plugins ./wordpress/wp-content/plugins
mv ./tmp/themes ./wordpress/wp-content/themes

# Start docker containers.
docker-compose stop
docker-compose rm --all --force
docker-compose build
docker-compose up
