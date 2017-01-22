#!/bin/sh
docker build -t sitecrawler/composer:latest .

docker run --rm -it \
    -v $(pwd):/sitecrawler \
    sitecrawler/composer:latest \
    composer install
