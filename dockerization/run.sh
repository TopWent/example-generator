#!/usr/bin/env bash

CUR_DIR="$( cd "$( echo "${BASH_SOURCE[0]%/*}" )"; pwd )"

cd "$CUR_DIR/.."

FILE=docker-compose.yml
if [[ -f "$FILE" ]]; then
    echo "$FILE уже существует, удалите его, если хотите заменить на dockerization/docker-compose.yml.example"
else
    cp dockerization/docker-compose.yml.example docker-compose.yml
fi

docker-compose down
docker-compose up -d --build

docker-compose exec nginx mkdir var/log/scoring
docker-compose exec php composer install