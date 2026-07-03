# bookmarkFox — Firefox Extension

Browser extension that syncs Firefox bookmarks to the bookmarkFox backend.

## Prerequisites

- Firefox (any recent version)

## Build

No build step required. The extension is plain JavaScript/HTML/CSS loaded
directly by Firefox.

## Development

1. Open `about:debugging#/runtime/this-firefox` in Firefox.
2. Click **Load Temporary Add-on**.
3. Select `manifest.json` from this directory.

## Deploy

Package the extension for distribution:

1. Zip the contents of this directory:

   ```bash
   zip -r ../bookmarkfox-extension.zip . -x "tests/*"
   ```

2. Submit to the [Firefox Add-ons](https://addons.mozilla.org) developer
   hub following Mozilla's review guidelines.

## Configuration

Set the backend URL by changing the `API_BASE` constant in both
`background.js` and `popup/popup.js`
(current: `https://bookmarkfox.b0sh.net/api/v1`).

## Permissions

The extension requires these permissions (declared in `manifest.json`):

- `bookmarks` — read the user's bookmark tree
- `storage` — persist auth token and sync state locally
- `alarms` — trigger periodic background sync
