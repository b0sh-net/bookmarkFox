# bookmarkFox

Sync and share your Firefox bookmarks.

Firefox extension that synchronizes bookmarks to a backend server and hosts public browseable bookmark pages for every user.

## Architecture

```
┌─────────────────────┐     ┌──────────────────────────────────┐
│  Firefox Extension  │────▶│  PHP Backend (Laravel + Sanctum) │
│  (Manifest V3)      │     │  - REST API (JSON)               │
│                     │     │  - MySQL storage                 │
│  background.js      │     │  - Public browseable pages       │
│  popup/             │     └──────────────────────────────────┘
└─────────────────────┘
```

- **Extension**: Plain JS/HTML/CSS, no build step. Communicates with the backend via `fetch()`.
- **Backend**: Laravel 11 with Sanctum for token auth. Custom directory structure (no artisan `make:` commands).

## Project Structure

```
bookmarkFox/
├── extension/              # Firefox extension
│   ├── manifest.json       # Manifest V3
│   ├── background.js       # Sync logic, bookmark tree building
│   └── popup/              # Extension UI (popup.html, popup.js, popup.css)
├── backend/                # PHP backend
│   ├── src/                # Application code (PSR-4: App\)
│   │   ├── Controllers/    # Auth, Sync, Public pages
│   │   ├── Models/         # User, Bookmark, BookmarkFolder
│   │   ├── Services/       # AuthService, SyncService
│   │   └── Exceptions/     # Custom error handling
│   ├── routes/api.php      # All routes
│   ├── views/              # Blade templates
│   ├── migrations/         # Database migrations
│   └── config/             # Laravel config
├── deploy.md               # Deployment instructions
└── specs/                  # Feature specifications
```

## Quick Start

### Extension

1. Open Firefox `about:debugging#/runtime/this-firefox`
2. Click **Load Temporary Add-on**
3. Select `extension/manifest.json`
4. The extension icon appears in the toolbar

### Backend

```bash
cd backend
cp .env.example .env        # Configure DB credentials
composer install
php artisan migrate --force
php artisan serve            # Development server on port 8000
```

The API base URL is configured in both `extension/background.js` and `extension/popup/popup.js`.

## API Endpoints

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| POST | `/api/v1/register` | No | Create account (rate limited) |
| POST | `/api/v1/login` | No | Login (rate limited) |
| POST | `/api/v1/logout` | Yes | Revoke token |
| GET | `/api/v1/me` | Yes | Current user info |
| POST | `/api/v1/bookmarks/sync` | Yes | Sync bookmark tree |
| GET | `/` | No | Public homepage with user list |
| GET | `/{email}/{path?}` | No | Browse public bookmarks (unlimited nesting) |

## License

MIT
