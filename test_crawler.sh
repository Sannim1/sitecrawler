#!/bin/sh
docker run --rm -it \
    -v $(pwd):/sitecrawler \
    sitecrawler/composer:latest \
    vendor/bin/phpunit -c phpunit.xml
