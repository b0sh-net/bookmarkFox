# Quickstart: Partial Sync Scope

## Prerequisites

- Extension loaded in Firefox via `about:debugging#/runtime/this-firefox`
- Authenticated with the backend (email + password)

## Validation Scenarios

### Scenario 1: Folder Picker Visible

1. Open the extension popup.
2. **Expected**: A dropdown or list shows top-level Firefox bookmark folders
   (e.g., "Bookmarks Menu", "Other Bookmarks", plus any user-created folders).

### Scenario 2: Select Sync Root

1. Choose a folder (e.g., "Bookmarks Menu") from the picker.
2. Click "Sync Now".
3. **Expected**: Only bookmarks under "Bookmarks Menu" appear on the backend
   public page. Bookmarks in other folders are absent.

### Scenario 3: Change Sync Root

1. Select a different folder from the picker.
2. Click "Sync Now".
3. **Expected**: The backend now shows bookmarks from the newly selected folder.
   Previously synced bookmarks from other folders are removed.

### Scenario 4: Clear Sync Root

1. Clear the selection (deselect / choose "All Bookmarks").
2. Click "Sync Now".
3. **Expected**: All bookmarks are synced again (full-tree behavior).

### Scenario 5: Persistence

1. Select a sync root folder.
2. Close and reopen the extension popup.
3. **Expected**: The same folder is still selected.

### Scenario 6: Deleted Folder Fallback

1. Select a folder as the sync root.
2. Delete that folder in Firefox's bookmark manager.
3. Open the extension popup and click "Sync Now".
4. **Expected**: The extension falls back to full-tree sync and shows a
   notification that the selected folder no longer exists.

## Verification

All scenarios above must pass before marking this feature complete.
See `spec.md` for full requirements and acceptance criteria.
