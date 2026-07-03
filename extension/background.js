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

async function performSync() {
  const { token } = await chrome.storage.local.get(['token']);
  if (!token) return;

  try {
    const tree = await new Promise((resolve) => {
      chrome.bookmarks.getTree((t) => resolve(buildTreePayload(t)));
    });

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

chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
  if (message.action === 'getBookmarkTree') {
    chrome.bookmarks.getTree((tree) => {
      sendResponse({ tree: buildTreePayload(tree) });
    });
    return true;
  }

  if (message.action === 'syncNow') {
    performSync().then(() => sendResponse({ done: true }));
    return true;
  }
});
