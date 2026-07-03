# Deploy — bookmarkFox Backend

## Server

| Field     | Value                    |
|-----------|--------------------------|
| Host      | `b0sh.net`               |
| Path      | `/home/b0sh/domains/bookmarkfox.b0sh.net` |
| Web root  | `public_html/`           |
| PHP       | 8.4                      |
| DB        | MariaDB 10.11, database `bookmarkfox` |
| Composer  | `/home/b0sh/domains/gps.b0sh.net/composer` |

> SSH user and password will be provided separately.

## Files to update

| File | What to change |
|------|----------------|
| `extension/background.js` | `API_BASE` constant |
| `extension/popup/popup.js` | `API_BASE` constant |
| `extension/README.md` | docs if URL changes |

## Deploy steps

```bash
# 1. Upload backend code (exclude vendor/)
cd bookmarkFox
tar czf - -C backend/ --exclude=vendor . | \
  ssh USER@b0sh.net "tar xzf - -C /home/b0sh/domains/bookmarkfox.b0sh.net/laravel/"

# 2. Install PHP dependencies
ssh USER@b0sh.net "cd /home/b0sh/domains/bookmarkfox.b0sh.net/laravel && \
  /home/b0sh/domains/gps.b0sh.net/composer install --no-dev --no-interaction"

# 3. Set up .env on the server (see below)
# 4. Run migrations
ssh USER@b0sh.net "cd /home/b0sh/domains/bookmarkfox.b0sh.net/laravel && \
  php artisan migrate --force"
```

## .env (server)

Create `/home/b0sh/domains/bookmarkfox.b0sh.net/laravel/.env`:

```ini
APP_NAME=bookmarkFox
APP_ENV=production
APP_KEY=<run: php artisan key:generate>
APP_DEBUG=false
APP_URL=https://bookmarkfox.b0sh.net

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bookmarkfox
DB_USERNAME=<ask me>
DB_PASSWORD=<ask me>

SANCTUM_STATEFUL_DOMAINS=bookmarkfox.b0sh.net
SESSION_DRIVER=file
SESSION_DOMAIN=.bookmarkfox.b0sh.net
```

## First deploy only

```bash
# Create directories
ssh USER@b0sh.net "mkdir -p /home/b0sh/domains/bookmarkfox.b0sh.net/laravel && \
  rm -f /home/b0sh/domains/bookmarkfox.b0sh.net/public_html/index.html"

# Upload code (same as above)

# Create web entrypoint: public_html/index.php
cat > public_html/index.php << 'PHP'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = __DIR__ . '/../laravel';

require $basePath . '/vendor/autoload.php';

$app = new Application($basePath);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Request::capture());
$response->send();
$kernel->terminate($request, $response);
PHP

# Create .htaccess in public_html/
# (same .htaccess as gps.b0sh.net — mod_rewrite to index.php)

# Storage directories
ssh USER@b0sh.net "cd /home/b0sh/domains/bookmarkfox.b0sh.net/laravel && \
  mkdir -p storage/framework/cache/data storage/framework/views \
  storage/framework/sessions storage/logs storage/app && \
  chmod -R 755 storage bootstrap/cache"

# Install Sanctum migrations
ssh USER@b0sh.net "cd /home/b0sh/domains/bookmarkfox.b0sh.net/laravel && \
  php artisan vendor:publish --provider='Laravel\Sanctum\SanctumServiceProvider' \
  --tag='sanctum-migrations' --force && \
  php artisan key:generate && \
  php artisan migrate --force"
```

## Troubleshooting

- Check logs: `storage/logs/laravel.log`
- Clear cache: `php artisan config:clear`
- Apache config: Virtualmin manages it — do not edit manually.
- Migration path: Laravel looks in `database/migrations/` (symlinked from `migrations/`).
