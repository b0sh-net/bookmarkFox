const API_BASE = 'http://localhost:8000/api/v1';

function buildTreePayload(nodes) {
  return nodes.map((node) => {
    const entry = {
      firefox_id: node.id,
      title: node.title,
      type: node.url ? 'bookmark' : 'folder',
      position: node.index ?? 0,
    };

    if (node.url) entry.url = node.url;
    if (node.children) entry.children = buildTreePayload(node.children);

    return entry;
  });
}

function findSubtree(nodes, targetId) {
  for (const node of nodes) {
    if (node.id === targetId) return [node];
    if (node.children) {
      const found = findSubtree(node.children, targetId);
      if (found) return found;
    }
  }
  return null;
}

async function getPrunedTree(syncRootId) {
  const tree = await new Promise((resolve) => {
    chrome.bookmarks.getTree((t) => resolve(t));
  });

  if (!syncRootId) return { tree: buildTreePayload(tree) };

  const subtree = findSubtree(tree, syncRootId);
  if (!subtree) {
    return { tree: buildTreePayload(tree), fallback: true };
  }

  return { tree: buildTreePayload(subtree) };
}

async function performSync() {
  const { token, syncRootId } = await chrome.storage.local.get(['token', 'syncRootId']);
  if (!token) return;

  try {
    const { tree, fallback } = await getPrunedTree(syncRootId);

    if (fallback) {
      chrome.storage.local.remove(['syncRootId', 'syncRootTitle']);
    }

    const res = await fetch(`${API_BASE}/bookmarks/sync`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: JSON.stringify({ tree }),
    });

    if (!res.ok) throw new Error('Sync failed');

    chrome.storage.local.set({ lastSync: Date.now() });
  } catch (_) { /* silent — retry on next alarm */ }
}

chrome.runtime.onInstalled.addListener(() => {
  chrome.alarms.create('bookmarkSync', { periodInMinutes: 5 });
});

chrome.alarms.onAlarm.addListener((alarm) => {
  if (alarm.name === 'bookmarkSync') performSync();
});

chrome.runtime.onMessage.addListener(async (message, sender, sendResponse) => {
  if (message.action === 'getBookmarkTree') {
    const { tree, fallback } = await getPrunedTree(message.syncRootId);

    if (fallback) {
      chrome.storage.local.remove(['syncRootId', 'syncRootTitle']);
    }

    sendResponse({ tree });
    return true;
  }

  if (message.action === 'syncNow') {
    performSync().then(() => sendResponse({ done: true }));
    return true;
  }
});
