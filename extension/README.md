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
   zip -r ../bookmarkFox-v1.0.zip manifest.json background.js popup/ icons/
   ```

2. Submit to the [Firefox Add-ons](https://addons.mozilla.org) developer
   hub (select **Self-distributed** — not listed on AMO).

3. After approval, download the signed `.xpi` from AMO and create a
   [GitHub Release](https://github.com/b0sh-net/bookmarkFox/releases)
   with it.

4. Update [`updates.json`](../updates.json) with the download URL of the
   signed XPI so existing installations auto-update.

### Self-distribution

The extension uses `browser_specific_settings.gecko.update_url` pointing to
the raw `updates.json` on GitHub. Firefox checks this URL periodically and
installs new versions automatically.

### Data collection permissions

The extension transmits bookmark data (titles, URLs, folder structure) and
authentication info (email) to the remote backend. These are declared in
`manifest.json` under `data_collection_permissions` as required data types:

- `bookmarksInfo` — bookmark names, URLs, and folder names
- `authenticationInfo` — email and password for the account-based service

Users see this disclosure at install time and cannot opt out, as the
extension cannot function without this data.

## Configuration

Set the backend URL by changing the `API_BASE` constant in both
`background.js` and `popup/popup.js`
(current: `https://bookmarkfox.b0sh.net/api/v1`).

## Permissions

The extension requires these permissions (declared in `manifest.json`):

- `bookmarks` — read the user's bookmark tree
- `storage` — persist auth token and sync state locally
- `alarms` — trigger periodic background sync

### Data collection

The following data types are collected and transmitted (required at install
time, declared under `browser_specific_settings.gecko.data_collection_permissions`):

- **bookmarksInfo** — bookmark titles, URLs, and folder names
- **authenticationInfo** — email and password for account access
