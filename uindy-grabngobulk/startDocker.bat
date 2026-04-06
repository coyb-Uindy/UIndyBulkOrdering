mkdir mysql
mkdir logs
docker run -i --rm -t -p 80:80 -p 3306:3306 -v "%~dp0/app:/app" -v "%~dp0/mysql:/var/lib/mysql" -v "%~dp0/logs:/var/log/apache2" mattrayner/lamp:0.8.0-1804-php8
