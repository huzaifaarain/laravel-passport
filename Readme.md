# Laravel Passport OAuth 2.0 Server, and Client Implementation

Hi! Hope you are enjoying the very of best of your health.
The repository is an example of how one can implement the `laravel/passport` for OAuth 2.0 Server, and how it can be consumed by the client applications.

The example is setup and configured using docker. The `docker-compose.yml` has the following services.
- OAuth Server Container, built on top of `php-8-fpm-alpine`
- OAuth Client Container, built on top of `php-8-fpm-alpine`
- DB container, built on top of `mysql:5.7`
- OAuth Server Nginx Container, built on top of `nginx:1.17-alpine`
- OAuth Client Nginx Container, built on top of `nginx:1.17-alpine`
- phpmyadmin container managing databases

```sh
docker-compose build
docker-compose up -d
```
Once the containers are up and running, first get the IP of containers.
```sh
docker inspect oauth_pma | grep "IPAddress"
docker inspect oauth_server_nginx | grep "IPAddress"
docker inspect oauth_client_nginx | grep "IPAddress"
```
Access the `phpmyadmin` using the `oauth_pma` IP from the browser, and create two databases, one for the server and one for the client.

Update/Create `oauth-server/.env` file
```env
DB_CONNECTION=mysql
DB_HOST=oauth_db
DB_PORT=3306
DB_DATABASE=your-oauth-server-db-name
DB_USERNAME=root
DB_PASSWORD=oauth
```
```sh
docker exec oauth_server php artisan migrate
```
Access the application using `oauth_server_nginx` container IP, register a user, login, and under the `developers` section, create a new client, the redirect url should be relative to `oauh_client_nginx` container IP. 

Once you have created the client, Update/Create `oauth-client/.env` file
```env
DB_CONNECTION=mysql
DB_HOST=oauth_db
DB_PORT=3306
DB_DATABASE=your-oauth-client-db-name
DB_USERNAME=root
DB_PASSWORD=oauth
```
Update the `oauth-client/config/oauth.php` 
```php
<?php
return [
'client_id'  =>  'your-client-id',
'client_secret'  =>  'your-client-secret',
'server_url'  =>  'http://oauth_server_nginx', // oauth_server_nginx IP
'callback'  =>  'http://oauth_client_nginx/oauth/callback' // oauth_client_nginx IP
];
``` 
```sh
docker exec oauth_client php artisan migrate
```
You can now access the server using `oauth_server_nginx` IP address, and the client using `oauth_client_nginx` IP address, additionaly, `phpmyadmin` will be accessible via `oauth_pma` IP address. Each time, you restart a container, the IP addresses of the container may or may not be the same one, For persitent configuration, you should consider fully qualified domain names using reverse nginx proxy or whatever suites you good.
