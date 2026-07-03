# bookmarkFox — Backend API

PHP web application that provides the REST API for bookmark sync and serves
public bookmark browsing pages.

## Prerequisites

- PHP 8.1+
- Composer
- MySQL 8.0+

## Setup

```bash
cd backend
composer install
cp .env.example .env
```

Edit `.env` and set your MySQL connection credentials:

```
DB_DATABASE=bookmarkfox
DB_USERNAME=root
DB_PASSWORD=
```

Generate the application key and run migrations:

```bash
php artisan key:generate
php artisan migrate
```

## Development

Start the built-in PHP server:

```bash
php artisan serve
```

The API is available at `http://localhost:8000/api/v1`.

## Test

```bash
php artisan test
```

## Deploy

For production, configure a web server (Nginx, Apache) to serve the `public/`
directory. Ensure the following environment variables are set on the server:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY` (generated via `php artisan key:generate`)
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

Example Nginx configuration:

```nginx
server {
    listen 80;
    server_name bookmarkfox.it;
    root /var/www/backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Directory Structure

```
backend/
├── config/       # Application configuration
├── migrations/   # Database migrations
├── public/       # Web server root (index.php)
├── src/          # PHP source code (Controllers, Models, Services)
├── views/        # Blade templates
└── tests/        # PHPUnit tests
```

## API Endpoints

See `contracts/auth-api.md` and `contracts/bookmarks-api.md` for the full
API contract documentation.
