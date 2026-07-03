# Quickstart: Bookmark Sync

## Prerequisites

- PHP 8.x + Composer
- MySQL 8.x
- Node.js (for extension development)
- Firefox (for extension testing)

## Backend Setup

```bash
cd backend
composer install
cp .env.example .env   # configure DB connection
php artisan key:generate
php artisan migrate
php artisan serve
```

### Run backend tests

```bash
php artisan test
```

## Extension Setup

```bash
cd extension
# Edit manifest.json to point to your backend URL
```

1. Open `about:debugging#/runtime/this-firefox` in Firefox
2. Click "Load Temporary Add-on"
3. Select `extension/manifest.json`

## Validation Scenarios

### Scenario 1: User Registration

```bash
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"secret123"}'
```

**Expected**: HTTP 201 with `{"token": "..."}`

### Scenario 2: User Login

```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"secret123"}'
```

**Expected**: HTTP 200 with `{"token": "..."}`

### Scenario 3: Sync Bookmarks

```bash
TOKEN="1|..."
curl -X POST http://localhost:8000/api/v1/bookmarks/sync \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "tree": [{
      "firefox_id": "root",
      "title": "",
      "type": "folder",
      "children": [{
        "firefox_id": "f1",
        "title": "Dev",
        "type": "folder",
        "children": [{
          "firefox_id": "b1",
          "title": "GitHub",
          "type": "bookmark",
          "url": "https://github.com",
          "position": 0
        }]
      }]
    }]
  }'
```

**Expected**: HTTP 200 with `{"created": 2, "updated": 0, "deleted": 0, ...}`

### Scenario 4: Public Page

Open `http://localhost:8000/test@example.com` in browser.

**Expected**: HTML page showing "Dev" folder with "GitHub" link.

### Scenario 5: Extension Sync (manual)

1. Load extension in Firefox
2. Open extension popup, enter email + password
3. Click "Sync" button
4. Open `http://localhost:8000/{email}` — bookmarks should appear

## Verification

All scenarios above must pass before marking this feature complete.
See `contracts/auth-api.md` and `contracts/bookmarks-api.md` for full API details.
See `data-model.md` for the database schema.
