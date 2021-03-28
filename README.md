# document-generation-service

## PRODUCTION

### Пакеты

- php7.3 php7.3-fpm php7.3-cli php7.3-gd php7.3-pdo php7.3-pdo_mysql php7.3-mysqli php7.3-zip
- nginx 1.17
- composer (last version)
- mysql 8
Для MySQL8 необходимо после установки в файле my.cnf прописать параметр `default-authentication-plugin=mysql_native_password` и перезапустить сервис mysql.

### Установка

1. Склонировать репозиторий.
2. На директории var и public назначить рекурсивно права на создание, запись и чтение папок и файлов (777).
3. `composer install`.
4. Настроить nginx на каталог /public, сверить с конфигом, который лежит в папке dockerization/nginx.
5. Скопировать содержимое файла .env в файл .env.local, выставить корректные значения переменных окружения. Проверить, что среда настроена правильно можно командой php bin/console about.
6. Создать БД, накатить миграции командой `php bin/console doctrine:migrations:migrate`

## DEV

Для разработки среду проще всего развернуть в докер-контейнерах, выполнив следующие команды:

```cp dockerization/docker-compose.yml.example docker-compose.yml```

```docker-compose up -d --build```

```docker-compose exec php composer install```

## PRE PROD

1.На сервере в директории проекта создать файл `docker-compose.yml`, с помощью команды:
```
sudo cp docker-compose.example.pre-prod.yml docker-compose.yml
```
 
 2.В файле  `docker-compose.yml` настроить проброс порта из контейера с NGINX.
 Для этого посмотреть порты, уже занятые другими контейнерами, командой:
```
sudo docker ps | grep nginx
```
3.Копируем файл ``.env`` в ``.env.local`` и указываем актуальные значения перменных окружения.

4.Создать конфигурационный файл для NGINX, выступающего как прокси в контейнер
```
sudo nano /etc/nginx/sites-available/document-generation-service
```
Его содержимое может быть таким:
````
upstream document-generation-service {
   server 127.0.0.1:8081;
}

server {
   listen 80;
   server_name document-generation-service.jarvis;
   error_log /var/log/nginx/document-generation-service.error.log;
   access_log /var/log/nginx/document-generation-service.access.log;

   location / {
       proxy_pass http://document-generation-service;
       proxy_redirect off;
       proxy_set_header Host $host;
   }
}

````
5.Включение конфига, как активный:
```
sudo ln -s /etc/nginx/sites-available/document-generation-service /etc/nginx/sites-enabled/
```

6.Проверка отстутсвия ошибок и загрузка обновленных конфигов 
```
sudo nginx -t && sudo service nginx reload
```

7.Запускаем контейнеры ``docker-compose up --build -d``

8.Создаем БД и применяем миграции:
```
docker-compose exec php bin/console doctrine:database:create
docker-compose exec php bin/console doctrine:m:m
```
9.Список доступных uri:
````
docker-compose exec php bin/console debug:router
```` 