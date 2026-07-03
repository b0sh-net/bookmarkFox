# Bookmarks API Contract

**Base URL**: `https://bookmarkfox.it/api/v1`

**Auth Scheme**: Bearer token (all endpoints require auth)

---

## POST /bookmarks/sync

Send the full bookmark tree from the Firefox extension to the backend.
The backend computes diffs and returns a summary.

### Request

Headers: `Authorization: Bearer 1|abc123def456...`
Content-Type: `application/json`

```json
{
  "tree": [
    {
      "firefox_id": "root________",
      "title": "",
      "type": "folder",
      "children": [
        {
          "firefox_id": "2",
          "title": "Bookmarks Menu",
          "type": "folder",
          "children": [
            {
              "firefox_id": "3",
              "title": "Mozilla",
              "type": "bookmark",
              "url": "https://mozilla.org",
              "position": 0
            }
          ]
        },
        {
          "firefox_id": "4",
          "title": "Other Bookmarks",
          "type": "folder",
          "children": []
        }
      ]
    }
  ]
}
```

Each node in the tree:
- `firefox_id`: string — the Firefox bookmark node ID
- `title`: string — bookmark or folder title
- `type`: `"folder"` | `"bookmark"`
- `url`: string — bookmark URL (absent for folders)
- `position`: integer — zero-based position within parent
- `children`: array of nodes — present only for folders (may be empty)

### Response 200

```json
{
  "created": 12,
  "updated": 3,
  "deleted": 1,
  "folders": 5,
  "bookmarks": 10
}
```

### Response 422

```json
{
  "message": "Invalid tree structure.",
  "errors": {
    "tree": ["The tree field is required."]
  }
}
```

---

## Public Pages (no auth)

The following pages are publicly accessible without authentication.
They return HTML (not JSON).

| URL Pattern | Description |
|-------------|-------------|
| `GET /{email}` | User's root bookmarks page |
| `GET /{email}/{folder}` | Folder contents |
| `GET /{email}/{folder}/{subfolder}` | Nested folder contents |

URL segments are URL-encoded folder names. Each page renders an HTML view
with the folder's bookmarks as links and subfolders as navigable entries.

### Response 200

HTML page with modern responsive design.

### Response 404

HTML page with "User not found" or "Folder not found" message.
