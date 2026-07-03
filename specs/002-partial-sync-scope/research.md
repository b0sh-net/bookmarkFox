# Research: Partial Sync Scope

## Technical Decisions

### Tree Pruning Strategy

**Decision**: After calling `browser.bookmarks.getTree()` and building the payload,
filter the tree to only include the selected subtree before sending to the backend.

**Rationale**: `getTree()` always returns the full tree. Pruning after the fact is
simpler than calling `getSubTree(id)` because we already have all node data in
memory. A recursive search locates the selected folder node by `firefox_id`, then
its entire subtree is used as the payload.

**Alternatives considered**:
- `getSubTree(id)`: Makes a separate API call but returns only the relevant nodes.
  Rejected because we already have the full tree from the existing `getTree()` call.
- Server-side filtering: Would require sending the folder ID to the backend and
  modifying the API contract. Rejected per spec constraint (no backend changes).

### Sync Root Persistence

**Decision**: Use `chrome.storage.local` to store the selected folder's `firefox_id`
and `title`.

**Rationale**: `storage.local` persists across browser restarts and is async. The
title is stored alongside the ID so the popup can display the current selection
without an extra bookmark API call.

### Folder Detection on Sync

**Decision**: On each sync, check if the stored `firefox_id` still exists in the
bookmark tree. If not, fall back to full-tree sync and show a notification.

**Rationale**: The user might delete the folder outside the extension. The
extension should gracefully degrade rather than fail silently.
