# API Rest Work Entries 

## Contenedores con Docker

Arrancar el proyecto con

`docker-compose up -d`

## Importación de base de datos

Importar migraciones:

`docker-compose exec php php bin/console make:migration`


`docker-compose exec php php bin/console doctrine:migrations:migrate
`

Replicar el esquema de base de datos:

`docker-compose exec php php bin/console doctrine:schema:update --force`

## Creación de entidades

**Entidad User**

id (integer)

created_at (datetime)

updated_at (datetime)

deleted_at (datetime)

user_name (varchar 255)

email (varchar 255)

Relación 1 - N (1 User - N Work Entry)

**Entidad WorkEntry**

id (integer)

created_at (datetime)

updated_at (datetime)

deleted_at (datetime)

start_date (datetime)

end_date (datetime)

users_id( Postgresql tiene la palabra user como reservada y he usado la palabra en plural)
Relación N - 1 (N Work Entry - 1 User)

## Llamadas API REST

LLamadas disponibles:

User:
POST create user
PUT update user
DELETE user by id
GET user by id
GET all users

WorkEntry:
POST create workEntry
PUT update workEntry
DELETE workEntry by id
GET workEntry by id
GET workEntry by userId

Se pueden consultar todas las rutas desde api.yaml


Se pueden importar en postman o otro sistema de llamadas API Rest con el fichero SESAME.postman_collection.json

## Sistema de detección de problemas para la aplicación

Para este objetivo se ha implementado:

Prometheus (Para controlar api, contenedores y logs)

Promtail (Demonio para enviar logs a un sistema como Grafana)

Loki (Sistema para importar logs a sistemas de monitorización)

Grafana (Sistema de monitorización desde el cual centrar los datos
de los logs y de Prometheus)

(El sistema es totalmente adaptable y modificable con Grafana, se 
ha incluído en Docker para configurar con los datos que se puedan
ajustar a las necesidades del proyecto)
