# Cindes investors project

## Docker used ports
- 5432 Postgresql.
- 8080 nginx (including phpunit).
- 9000-9002 php

## Run with Docker
Steps for run the project in command line in the base path of the project:
1. First time build containers running ```$ docker-compose build```
(Later, is possible use only ```$ docker-compose up -d```)

Congrats! in url **127.0.0.1:8080** you should see **Symfony Welcome Page**.
