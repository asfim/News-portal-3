# H Tech Provision IT — Bengali News CMS Deployment & Operations Guide

This document provides production deployment instructions for **cPanel / Shared Hosting** and **Linux VPS (Ubuntu / Debian)**.

---

## ১. সার্ভার প্রয়োজনীয়তা (Server Requirements)

| উপাদান | ন্যূনতম স্পেসিফিকেশন |
|---|---|
| **PHP** | PHP 8.2 বা তার ঊর্ধ্বতন (বর্তমানে PHP 8.2+) |
| **PHP Extensions** | `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `gd` (পোস্টার জেনারেশনের জন্য আবশ্যক), `hash`, `intl`, `json`, `mbstring`, `openssl`, `pcre`, `pdo_mysql`, `session`, `tokenizer`, `xml`, `zip` |
| **Database** | MySQL 8.0+ অথবা MariaDB 10.4+ (উভয়ই UTF-8 `utf8mb4` ও `utf8mb4_unicode_ci` সাপোর্টযুক্ত) |
| **Web Server** | Nginx অথবা Apache (with `mod_rewrite` enabled) |
| **Node.js** | Node.js 18+ (যদি প্রোডাকশন অ্যাসেট বিল্ড সার্ভারে চালানো হয়) |

---

## ২. পরিবেশ কনফিগারেশন (.env Setup)

```env
APP_NAME="এইচ টেক প্রোভিশন আইটি"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yournewsdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_production_db
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database

# AI Assistant (ঐচ্ছিক: Google Gemini অথবা OpenAI)
AI_DEFAULT_PROVIDER=gemini
AI_GEMINI_API_KEY=your_gemini_api_key_here
AI_OPENAI_API_KEY=your_openai_api_key_here

# Meta Graph API (Facebook Page Publishing)
FACEBOOK_APP_ID=your_meta_app_id
FACEBOOK_APP_SECRET=your_meta_app_secret
```

---

## ৩. cPanel ডিপ্লয়মেন্ট নির্দেশিকা (cPanel Deployment)

cPanel-এ সাধারণত সরাসরি ব্যাকগ্রাউন্ড ডেমন বা সুপারভাইজর চালানোর পারমিশন থাকে না। তাই cPanel-এর ক্রনজব (Cron Job) এবং ডাটাবেস কিউ ব্যবহার করতে হবে।

### ধাপ ১: ফাইল আপলোড ও এক্সট্র্যাক্ট
1. সম্পূর্ণ প্রজেক্ট আর্কাইভ cPanel-এর `File Manager`-এ আপলোড করুন (সাধারণত `public_html`-এর সমান্তরালে বা বাইরে একটি ফোল্ডারে, যেমন `/home/username/news-portal`)।
2. `public` ফোল্ডারের ফাইলসমূহ `public_html`-এ রাখুন অথবা ডোমেনের Document Root-কে প্রজেক্টের `public` ফোল্ডারে পয়েন্ট করুন (প্রস্তাবিত)।

### ধাপ ২: ডাটাবেস তৈরি ও মাইগ্রেশন
1. cPanel `MySQL Databases` থেকে নতুন ডাটাবেস ও ইউজার তৈরি করে সম্পূর্ণ প্রিভিলেজ দিন।
2. SSH টার্মিনাল চালু করে প্রজেক্ট ফোল্ডারে প্রবেশ করুন:
   ```bash
   cd /home/username/news-portal
   composer install --no-dev --optimize-autoloader
   php artisan key:generate --force
   php artisan migrate --force
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### ধাপ ৩: cPanel Cron Job কনফিগারেশন
cPanel-এর **Cron Jobs** মেনুতে যান এবং নিচের দুটি ক্রন সেট করুন:

1. **লারাভেল শিডিউলার (Laravel Scheduler)** — *প্রতি মিনিটে চলবে*:
   ```bash
   * * * * * /usr/local/bin/php /home/username/news-portal/artisan schedule:run >> /dev/null 2>&1
   ```
2. **কিউ প্রসেসিং (Queue Processing for cPanel)** — *প্রতি ৫ বা ১০ মিনিটে চলবে*:
   ```bash
   */5 * * * * /usr/local/bin/php /home/username/news-portal/artisan queue:work --stop-when-empty --tries=3 --timeout=60 >> /dev/null 2>&1
   ```
   > **নোট:** `--stop-when-empty` ফ্ল্যাগ নিশ্চিত করে যে প্রসেসটি কিউ খালি হওয়া পর্যন্ত কাজ করে স্বয়ংক্রিয়ভাবে বন্ধ হবে, ফলে সার্ভারের র‍্যাম বা প্রসেস লিমিট ওভারফ্লো হবে না।

---

## ৪. VPS ডিপ্লয়মেন্ট নির্দেশিকা (Ubuntu / Nginx / Supervisor)

### ধাপ ১: সার্ভার প্যাকেজ ও এক্সটেনশন ইনস্টলেশন
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-bcmath php8.2-curl php8.2-gd php8.2-zip supervisor git unzip
```

### ধাপ ২: প্রজেক্ট ক্লোন ও পারমিশন
```bash
cd /var/www
sudo git clone <your-repo-url> news-portal
cd news-portal
sudo chown -R www-data:www-data /var/www/news-portal
sudo chmod -R 775 /var/www/news-portal/storage /var/www/news-portal/bootstrap/cache
```

### ধাপ ৩: ডিপ্লয়মেন্ট অপ্টিমাইজেশন কমান্ড
```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

### ধাপ ৪: Nginx সাইট কনফিগারেশন
`/etc/nginx/sites-available/news-portal` তৈরি করুন:
```nginx
server {
    listen 80;
    server_name yournewsdomain.com www.yournewsdomain.com;
    root /var/www/news-portal/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
এনাবল করুন ও Nginx রিলোড দিন:
```bash
sudo ln -s /etc/nginx/sites-available/news-portal /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d yournewsdomain.com -d www.yournewsdomain.com
```

### ধাপ ৫: Supervisor Queue Worker কনফিগারেশন
`/etc/supervisor/conf.d/news-worker.conf` ফাইল তৈরি করুন:
```ini
[program:news-worker]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php /var/www/news-portal/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/news-portal/storage/logs/worker.log
stopwaitsecs=3600
```
সুপারভাইজর সক্রিয় করুন:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start news-worker:*
```

### ধাপ ৬: VPS Cron Setup
`crontab -e` এ যোগ করুন:
```bash
* * * * * cd /var/www/news-portal && php artisan schedule:run >> /dev/null 2>&1
```

---

## ৫. প্রাথমিক অ্যাক্সেস ও অ্যাডমিন লগইন

- **অ্যাডমিন লগইন পোর্টাল**: `/admin/login`
- **সুপার অ্যাডমিন ইমেইল**: `admin@htechprovision.com`
- **পাসওয়ার্ড**: `Admin@123456`
- **বার্তা সম্পাদক (Editor)**: `editor@htechprovision.com` / `Editor@123456`
- **রিপোর্টার (Reporter)**: `reporter@htechprovision.com` / `Reporter@123456`

---

## ৬. ব্যাকআপ ও রোলব্যাক নির্দেশিকা (Backup & Rollback)

1. **ডাটাবেস ব্যাকআপ**:
   ```bash
   mysqldump -u username -p database_name > backup_$(date +%F).sql
   ```
2. **রোলব্যাক**:
   ```bash
   php artisan migrate:rollback
   ```

