# Data Model: Bookmark Sync

## Entities

### User

| Field | Type | Constraints |
|-------|------|-------------|
| id | BIGINT UNSIGNED | PK, auto-increment |
| email | VARCHAR(255) | UNIQUE, NOT NULL |
| password | VARCHAR(255) | NOT NULL (bcrypt hash) |
| created_at | TIMESTAMP | NOT NULL |
| updated_at | TIMESTAMP | NOT NULL |

**Relationships**:
- Has many `BookmarkFolder` (cascade on delete)
- Has many `Bookmark` (cascade on delete)
- Has many `PersonalAccessToken` (via Sanctum)

### BookmarkFolder

| Field | Type | Constraints |
|-------|------|-------------|
| id | BIGINT UNSIGNED | PK, auto-increment |
| user_id | BIGINT UNSIGNED | FK → users.id, NOT NULL |
| parent_id | BIGINT UNSIGNED | FK → bookmark_folders.id, NULLABLE (null = root) |
| firefox_id | VARCHAR(64) | NOT NULL (Firefox node ID) |
| name | VARCHAR(255) | NOT NULL |
| position | INT UNSIGNED | NOT NULL (ordering within parent) |
| created_at | TIMESTAMP | NOT NULL |
| updated_at | TIMESTAMP | NOT NULL |

**Unique constraint**: (user_id, firefox_id)

**Relationships**:
- Belongs to `User`
- Belongs to `BookmarkFolder` (parent, self-referential)
- Has many `BookmarkFolder` (children, self-referential)
- Has many `Bookmark`

### Bookmark

| Field | Type | Constraints |
|-------|------|-------------|
| id | BIGINT UNSIGNED | PK, auto-increment |
| user_id | BIGINT UNSIGNED | FK → users.id, NOT NULL |
| folder_id | BIGINT UNSIGNED | FK → bookmark_folders.id, NOT NULL |
| firefox_id | VARCHAR(64) | NOT NULL (Firefox node ID) |
| title | VARCHAR(255) | NOT NULL |
| url | TEXT | NOT NULL |
| position | INT UNSIGNED | NOT NULL (ordering within folder) |
| created_at | TIMESTAMP | NOT NULL |
| updated_at | TIMESTAMP | NOT NULL |

**Unique constraint**: (user_id, firefox_id)

**Relationships**:
- Belongs to `User`
- Belongs to `BookmarkFolder`

## Sync Algorithm

1. Extension calls `browser.bookmarks.getTree()` → obtains full tree
2. Extension sends tree JSON to `POST /api/bookmarks/sync` with Bearer token
3. Backend processes:
   - Flatten tree into folders + bookmarks with their `firefox_id`
   - For each incoming folder/bookmark:
     - If `firefox_id` exists → update title/url/position/parent
     - If `firefox_id` is new → insert
   - For folders/bookmarks in DB not in incoming payload → soft-delete/mark absent
4. Return 200 OK with summary (created/updated/deleted counts)

## Folder Hierarchy for URLs

Public URLs are derived from the folder tree:

```
https://bookmarkfox.it/{email}/{folder_name}/{subfolder_name}
```

The URL path is built by walking from the root folder down to the target folder,
URL-encoding each folder name. Bookmarks without a folder (root-level) appear
at the user's root URL.
