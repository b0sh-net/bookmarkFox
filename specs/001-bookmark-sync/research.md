# Research: Bookmark Sync

## Technical Decisions

### Firefox Bookmarks API

**Decision**: Use `browser.bookmarks.getTree()` to obtain the full bookmark hierarchy.

**Rationale**: `getTree()` returns the complete tree in a single call, starting from the root
node. Each `BookmarkTreeNode` contains `id`, `parentId`, `title`, `url` (absent for folders),
and `children` (for folders). This avoids multiple round-trips for nested folder traversal.
The tree can be serialized to JSON and sent to the backend in one payload.

**Alternatives considered**:
- `getSubTree(id)`: Requires knowing root IDs; more round-trips.
- `search({})`: Returns flat list without folder hierarchy; loses structure.

### PHP Framework

**Decision**: Laravel (latest stable).

**Rationale**: Laravel provides built-in Sanctum for API token authentication (lightweight,
purpose-built for first-party API clients), Eloquent ORM for MySQL, Blade templating for
public pages, and built-in testing support. Sanctum is preferred over OAuth2 for this
use case (single client, no third-party auth needed).

**Alternatives considered**:
- **Symfony**: More complex setup, overkill for this scope.
- **Slim**: Minimal framework, would require manual auth and ORM setup.

### Authentication Scheme

**Decision**: Token-based authentication via Laravel Sanctum with Bearer tokens.

**Rationale**: Sanctum issues plain-text tokens on login that the extension stores in
`storage.local` and sends as `Authorization: Bearer <token>` on every request. Tokens
can have expiry and can be revoked server-side.

**Endpoints**:
- `POST /api/register` — create account (email + password), return token
- `POST /api/login` — authenticate, return token
- `POST /api/logout` — revoke current token

### Sync Strategy

**Decision**: Full-tree sync on each trigger (button press or periodic background task).

**Rationale**: Firefox's `getTree()` returns the full tree efficiently. Sending the
complete tree to the backend allows the server to compute diffs (additions, updates,
deletions) by comparing against the stored state. This is simpler than tracking
individual changes and avoids race conditions.

**Alternatives considered**:
- **Incremental sync via `onCreated`/`onRemoved`/`onChanged` events**: More complex,
  requires persistent event listeners and change queue management.

### Public Page Rendering

**Decision**: Server-rendered HTML with Blade templates, responsive CSS (no JS framework).

**Rationale**: Pages are read-only public views. Server-side rendering is faster,
SEO-friendly, and simpler. A modern CSS framework (e.g., Tailwind CSS) provides
responsive design without client-side JavaScript.

## Storage

**Decision**: MySQL via Laravel Eloquent.

**Rationale**: Specified by user. Eloquent provides migrations, relationships, and
query scoping for hierarchical folder data.

### Bookmark Deduplication

Bookmarks are identified by a combination of `user_id`, `firefox_id` (the Firefox
node ID), and `folder_path`. On each sync:
- New `firefox_id` values → insert
- Existing `firefox_id` with changed title/URL → update
- `firefox_id` present in DB but absent from latest sync → delete (soft)

## Security Considerations

- Passwords hashed via Laravel's Bcrypt hasher.
- API tokens stored hashed by Sanctum.
- All API routes behind `auth:sanctum` middleware (except register/login).
- Rate limiting on auth endpoints to prevent brute force.
- Input validation: email format, password minimum length, bookmark URL sanitization.
