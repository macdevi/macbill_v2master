# DEPLOY UBUNTU VPS

Laravel 13 requires PHP 8.3+; the official Laravel release table lists PHP 8.3–8.5 for Laravel 13. The RouterOS package requires ext-sockets.

1. Install packages:

sudo apt update
sudo apt install -y nginx unzip git curl sqlite3 php8.3-cli php8.3-fpm php8.3-sqlite3 php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath php8.3-intl php8.3-sockets

2. Install Composer:

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php

3. Install Node.js 22:

curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs

4. Upload macbilling_v2.zip to /var/www and extract:

sudo mkdir -p /var/www
cd /var/www
sudo unzip macbilling_v2.zip
sudo chown -R $USER:$USER macbilling_v2
cd /var/www/macbilling_v2

5. Install dependencies:

composer install --no-dev --optimize-autoloader
npm install
npm run build

6. Create SQLite:

touch database/database.sqlite

7. Environment:

cp .env.example .env
php artisan key:generate
nano .env

Set APP_URL and keep APP_DEBUG=false.

8. Database:

php artisan migrate --seed
php artisan storage:link

9. Permissions:

sudo chown -R www-data:www-data /var/www/macbilling_v2/storage /var/www/macbilling_v2/bootstrap/cache
sudo chmod -R ug+rwx /var/www/macbilling_v2/storage /var/www/macbilling_v2/bootstrap/cache

10. Nginx:

sudo nano /etc/nginx/sites-available/macbilling_v2

Use:

server {
    listen 80;
    listen [::]:80;
    server_name billing.example.com;

    root /var/www/macbilling_v2/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

Enable:

sudo ln -s /etc/nginx/sites-available/macbilling_v2 /etc/nginx/sites-enabled/macbilling_v2
sudo nginx -t
sudo systemctl reload nginx

11. Scheduler:

sudo crontab -u www-data -e

Add:

* * * * * cd /var/www/macbilling_v2 && php artisan schedule:run >> /dev/null 2>&1

12. Optional queue worker:

sudo nano /etc/systemd/system/macbilling-worker.service

[Unit]
Description=macbilling_v2 queue worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/macbilling_v2/artisan queue:work --sleep=3 --tries=3 --timeout=90
WorkingDirectory=/var/www/macbilling_v2

[Install]
WantedBy=multi-user.target

Then:

sudo systemctl daemon-reload
sudo systemctl enable --now macbilling-worker

13. Optimize:

sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

14. MikroTik:
- Enable API on RouterOS: /ip service enable api
- Restrict API service to the VPS IP using the address field/firewall.
- Create an API user with only the permissions needed by this app.
- Prefer RouterOS API port 8729 with TLS when available; configure the Router record accordingly.

15. First login:
admin@macbilling.local
ChangeMe123!

IMPORTANT: The current UI does not yet include a public authentication screen. Put the application behind VPN/IP allowlisting or add Laravel authentication before exposing the panel to the public internet.
