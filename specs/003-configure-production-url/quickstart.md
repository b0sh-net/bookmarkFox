# Quickstart: Configure Production API URL

## Prerequisites

- Extension files accessible at `extension/`
- Production backend deployed at `https://bookmarkfox.b0sh.net`

## Validation

### 1. Verify API_BASE constants

```bash
grep -n "API_BASE" extension/background.js extension/popup/popup.js
```

**Expected output:**
```
extension/background.js:1:const API_BASE = 'https://bookmarkfox.b0sh.net/api/v1';
extension/popup/popup.js:1:const API_BASE = 'https://bookmarkfox.b0sh.net/api/v1';
```

### 2. Verify endpoint paths preserved

```bash
grep -n "API_BASE" extension/background.js extension/popup/popup.js
```

Confirm all `API_BASE` usages still have the correct relative paths appended (e.g., `/bookmarks/sync`, `/bookmarks`).

### 3. Manual smoke test

1. Load the extension in Firefox (`about:debugging#/runtime/this-firefox`)
2. Authenticate with the production backend
3. Trigger a manual sync via the popup
4. Verify bookmarks appear on the production backend

## Related

- [Specification](spec.md)
- [Implementation Plan](plan.md)
