# Data Model: Partial Sync Scope

## Extension Storage (chrome.storage.local)

| Key | Type | Description |
|-----|------|-------------|
| syncRootId | string | Firefox node ID of the selected folder, or null for full sync |
| syncRootTitle | string | Human-readable folder name for display in popup |

## State Transitions

```
null (full sync) → set syncRootId → filtered sync under folder
filtered sync     → clear syncRootId → full sync
filtered sync     → change syncRootId → filtered sync under new folder
any state         → syncRoot folder deleted → null (full sync) + notification
```

## Tree Pruning Algorithm

1. Get full tree via `browser.bookmarks.getTree()`
2. If `syncRootId` is null → use full tree as payload
3. If `syncRootId` is set:
   a. Recursively search the tree for a node with `id === syncRootId`
   b. If found → use that node's subtree as the payload root
   c. If not found → clear `syncRootId`, fall back to full tree, notify user
4. Build payload from (full or pruned) tree via existing `buildTreePayload()`
