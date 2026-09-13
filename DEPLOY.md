# Deploy MacBilling V2 Master ke VPS Ubuntu Kosong

Panduan ini untuk membuat **instalasi baru** MacBilling V2 Master dari repository GitHub ke VPS Ubuntu kosong.

> Panduan ini membuat database baru. Ia tidak memindahkan customer, invoice, pembayaran, pengeluaran, router, ataupun setting dari VPS lama.

## 1. Informasi yang diperlukan

Sebelum mulai, siapkan:

- Domain/subdomain, misalnya `billing.example.com`.
- VPS Ubuntu 24.04 dengan akses sudo/root.
- SSH key/deploy key yang dapat membaca repository private:
  `git@github-macdevi:macdevi/macbill_v2master.git`.
- Nama aplikasi yang akan digunakan pada `APP_NAME`.
- Akses DNS untuk mengarahkan domain ke IP VPS.

Contoh path aplikasi pada panduan ini:

```text
/var/www/macbilling_v2master
```

## 2. Update sistem dan install dependency

```bash
sudo apt update
sudo apt upgrade -y

sudo apt install -y \
  nginx \
  git \
  curl \
  unzip \
  sqlite3 \
  php8.3-cli \
  php8.3-fpm \
  php8.3-sqlite3 \
  php8.3-mbstring \
  php8.3-xml \
  php8.3-curl \
  php8.3-zip \
  php8.3-bcmath \
  php8.3-intl \
  php8.3-sockets
```

Pastikan PHP dan extension tersedia:

```bash
php -v
php -m | grep -Ei 'pdo_sqlite|sqlite3|mbstring|xml|curl|zip|bcmath|intl|sockets'
```

## 3. Install Composer dan Node.js

### Composer

```bash
cd /tmp

php -r "copy('[https://getcomposer.org/installer](https://getcomposer.org/installer)', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php

composer --version
```

### Node.js 22

```bash
curl -fsSL [https://deb.nodesource.com/setup_22.x](https://deb.nodesource.com/setup_22.x) | sudo -E bash -
sudo apt install -y nodejs

node -v
npm -v
```

## 4. Siapkan deploy key GitHub

Buat key khusus deploy pada VPS:

```bash
ssh-keygen -t ed25519 -C "macbilling-v2master-deploy" \
  -f ~/.ssh/macbilling_v2master_deploy \
  -N ""
```

Tampilkan public key:

```bash
cat ~/.ssh/macbilling_v2master_deploy.pub
```

Tambahkan public key tersebut ke GitHub repository:

```text
Repository → Settings → Deploy keys → Add deploy key
```

Pilih akses read-only kecuali VPS memang perlu melakukan push.

Buat konfigurasi SSH:

```bash
cat >> ~/.ssh/config <<'EOF'
Host github-macdevi
    HostName github.com
    User git
    IdentityFile ~/.ssh/macbilling_v2master_deploy
    IdentitiesOnly yes
EOF

chmod 600 ~/.ssh/config
ssh -T git@github-macdevi
```

## 5. Clone source code

```bash
sudo mkdir -p /var/www
sudo chown -R "$USER":"$USER" /var/www

git clone git@github-macdevi:macdevi/macbill_v2master.git \
  /var/www/macbilling_v2master

cd /var/www/macbilling_v2master

git branch --show-current
git log --oneline -1
```

Pastikan branch adalah `main`.

## 6. Install dependency aplikasi

```bash
cd /var/www/macbilling_v2master

composer install --no-dev --optimize-autoloader --no-interaction

npm ci
npm run build
```

Jika `package-lock.json` tidak tersedia, gunakan:

```bash
npm install
npm run build
```

## 7. Buat environment production

```bash
cd /var/www/macbilling_v2master

cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```bash
nano .env
```

Minimal sesuaikan:

```dotenv
APP_NAME="Nama Usaha Anda"
APP_ENV=production
APP_DEBUG=false
APP_URL=[https://billing.example.com](https://billing.example.com)

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/macbilling_v2master/database/database.sqlite

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=database

MIKROTIK_TIMEOUT=5
MIKROTIK_ISOLATION_PROFILE=isolate
```

> Gunakan path absolut untuk `DB_DATABASE` agar SQLite konsisten saat dijalankan oleh cron, queue worker, atau PHP-FPM.

Jangan pernah commit file `.env`.

## 8. Buat database SQLite dan migrate

```bash
cd /var/www/macbilling_v2master

touch database/database.sqlite

php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

Seeder membuat akun bootstrap berikut:

| Field | Nilai |
|---|---|
| Username | `admin` |
| Email | `admin@macbilling.local` |
| Password | `ChangeMe123!` |
| Role | `super_admin` |
| Timezone | `Asia/Jakarta` |

Seeder juga membuat paket default 5 Mbps dan 10 Mbps.

> Setelah login pertama, segera ubah email dan password akun bootstrap.

## 9. Permission Laravel

Nginx/PHP-FPM berjalan sebagai `www-data`. Folder `storage` dan `bootstrap/cache` harus dapat ditulis user tersebut.

```bash
cd /var/www/macbilling_v2master

sudo chown -R www-data:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
```

Buat seluruh cache sebagai `www-data`, bukan root:

```bash
sudo -u www-data php artisan optimize:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

## 10. Konfigurasi Nginx

Buat site:

```bash
sudo nano /etc/nginx/sites-available/macbilling_v2master
```

Isi berikut, lalu ganti domain dan versi socket PHP bila diperlukan:

```nginx
server {
    listen 80;
    listen [::]:80;

    server_name billing.example.com;
    root /var/www/macbilling_v2master/public;
    index index.php;

    client_max_body_size 20M;

    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

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
```

Aktifkan site:

```bash
sudo ln -s /etc/nginx/sites-available/macbilling_v2master \
  /etc/nginx/sites-enabled/macbilling_v2master

sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
sudo systemctl enable --now php8.3-fpm nginx
```

Aplikasi harus diarahkan ke:

```text
/var/www/macbilling_v2master/public
```

Jangan arahkan Nginx ke root project.

## 11. Aktifkan HTTPS

Setelah DNS domain mengarah ke VPS:

```bash
sudo apt install -y certbot python3-certbot-nginx

sudo certbot --nginx -d billing.example.com
```

Uji renewal:

```bash
sudo certbot renew --dry-run
```

Lalu pastikan `.env` memakai:

```dotenv
APP_URL=[https://billing.example.com](https://billing.example.com)
```

Sesudah perubahan `.env`:

```bash
cd /var/www/macbilling_v2master
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan config:cache
```

## 12. Scheduler Laravel

Buka crontab user `www-data`:

```bash
sudo crontab -u www-data -e
```

Tambahkan satu baris:

```cron
* * * * * cd /var/www/macbilling_v2master && /usr/bin/php artisan schedule:run >> /var/www/macbilling_v2master/storage/logs/scheduler.log 2>&1
```

Pastikan cron aktif:

```bash
sudo systemctl enable --now cron
sudo systemctl status cron --no-pager
```

## 13. Queue worker

Karena konfigurasi default menggunakan `QUEUE_CONNECTION=database`, buat worker systemd:

```bash
sudo nano /etc/systemd/system/macbilling-v2master-worker.service
```

Isi:

```ini
[Unit]
Description=MacBilling V2 Master Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
WorkingDirectory=/var/www/macbilling_v2master
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --timeout=90
Restart=always
RestartSec=5
KillSignal=SIGTERM
TimeoutStopSec=3600

[Install]
WantedBy=multi-user.target
```

Aktifkan:

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now macbilling-v2master-worker
sudo systemctl status macbilling-v2master-worker --no-pager
```

## 14. Konfigurasi MikroTik

Pada MikroTik:

1. Aktifkan API atau API-SSL.
2. Batasi service API hanya untuk IP VPS.
3. Buat user API khusus dengan permission minimum.
4. Jika tersedia, pilih API-SSL port `8729`.
5. Jangan gunakan user `admin` MikroTik untuk aplikasi.

Contoh pemeriksaan RouterOS:

```routeros
/ip service print where name~"api"
/ip service set api address=IP_VPS/32
```

Sesuaikan service API-SSL bila digunakan. Setelah itu tambahkan router melalui menu aplikasi dan lakukan test koneksi.

## 15. Verifikasi instalasi

```bash
cd /var/www/macbilling_v2master

php artisan about
php artisan migrate:status
php artisan route:list | grep -E 'login|dashboard'
sudo -u www-data php artisan view:cache
```

Buka:

```text
[https://billing.example.com/login](https://billing.example.com/login)
```

Login dengan akun bootstrap, lalu segera:

1. Ganti password dan email Super Admin.
2. Isi Pengaturan Billing, termasuk Nama Usaha.
3. Buat Area Operasional.
4. Buat akun Admin/Kasir dan assignment area.
5. Tambahkan router MikroTik dan lakukan test koneksi.
6. Ubah setting lain sesuai kebutuhan operasional.

## 16. Update aplikasi dari GitHub

Lakukan backup database dan `.env` sebelum update.

```bash
cd /var/www/macbilling_v2master

git fetch origin
git status -sb
git pull --ff-only origin main

composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build

sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan optimize:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

sudo systemctl restart php8.3-fpm
sudo systemctl restart macbilling-v2master-worker
sudo systemctl reload nginx
```

Jangan menjalankan `php artisan db:seed --force` pada server production yang sudah memiliki data kecuali Anda memahami dampak seedernya.

## 17. Backup dan recovery

GitHub menyimpan source code, bukan data operasional. Untuk backup SQLite:

```bash
cd /var/www/macbilling_v2master

php artisan down --render="errors::503"

mkdir -p /root/macbilling-v2master-backups
cp -a .env /root/macbilling-v2master-backups/.env.$(date +%Y%m%d_%H%M%S)
sqlite3 database/database.sqlite \
  ".backup '/root/macbilling-v2master-backups/database_$(date +%Y%m%d_%H%M%S).sqlite'"
cp -a storage/app \
  "/root/macbilling-v2master-backups/storage-app_$(date +%Y%m%d_%H%M%S)"

php artisan up
```

Simpan backup di lokasi aman dan terenkripsi karena database dapat memuat data pelanggan serta konfigurasi sensitif.

## 18. Troubleshooting

### Error 500 / `touch(): Utime failed: Operation not permitted`

Penyebab umum: cache Blade dibuat oleh `root`, tetapi PHP-FPM berjalan sebagai `www-data`.

Perbaikan:

```bash
cd /var/www/macbilling_v2master

sudo chown -R www-data:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;

sudo -u www-data php artisan view:clear
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan optimize:clear
```

### Halaman 502 Bad Gateway

Periksa socket PHP-FPM dan status service:

```bash
ls -lah /run/php/
sudo systemctl status php8.3-fpm --no-pager
sudo tail -n 100 /var/log/nginx/error.log
```

### Halaman blank atau konfigurasi lama

```bash
cd /var/www/macbilling_v2master
sudo -u www-data php artisan optimize:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

### MikroTik tidak dapat dihubungi

- Periksa host, port, username, dan password router pada aplikasi.
- Pastikan VPS memiliki route ke jaringan manajemen MikroTik.
- Pastikan firewall mengizinkan TCP 8728 atau 8729.
- Periksa `/ip service print where name~"api"` pada MikroTik.
- Uji dari VPS:

```bash
nc -vz -w 5 IP_MIKROTIK 8728
```
