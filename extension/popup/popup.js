const API_BASE = 'https://bookmarkfox.b0sh.net/api/v1';

const authView = document.getElementById('auth-view');
const syncView = document.getElementById('sync-view');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const loginBtn = document.getElementById('login-btn');
const registerBtn = document.getElementById('register-btn');
const logoutBtn = document.getElementById('logout-btn');
const syncBtn = document.getElementById('sync-btn');
const authError = document.getElementById('auth-error');
const syncError = document.getElementById('sync-error');
const syncStatus = document.getElementById('sync-status');
const userEmail = document.getElementById('user-email');
const lastSync = document.getElementById('last-sync');
const folderPicker = document.getElementById('folder-picker');
const clearRootBtn = document.getElementById('clear-root-btn');

async function apiCall(endpoint, method = 'POST', body = null, token = null) {
  const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
  if (token) headers['Authorization'] = `Bearer ${token}`;

  const res = await fetch(`${API_BASE}${endpoint}`, {
    method,
    headers,
    body: body ? JSON.stringify(body) : undefined,
  });

  const data = await res.json();
  if (!res.ok) {
    if (res.status === 401) {
      chrome.storage.local.remove(['token', 'email', 'lastSync', 'syncRootId', 'syncRootTitle']);
      showAuthView();
    }
    throw new Error(data.message || 'Request failed');
  }
  return data;
}

async function populateFolderPicker() {
  const tree = await new Promise((resolve) => {
    chrome.bookmarks.getTree((t) => resolve(t));
  });

  const root = tree[0];
  const topLevelFolders = [];

  function collectTopLevelFolders(nodes) {
    for (const node of nodes) {
      if (node.children && node.id !== 'root________') {
        topLevelFolders.push({ id: node.id, title: node.title });
      }
      if (node.children) collectTopLevelFolders(node.children);
    }
  }

  collectTopLevelFolders(root.children || []);

  folderPicker.innerHTML = '<option value="">All bookmarks</option>';

  for (const f of topLevelFolders) {
    const opt = document.createElement('option');
    opt.value = f.id;
    opt.textContent = f.title;
    folderPicker.appendChild(opt);
  }

  const { syncRootId, syncRootTitle } = await new Promise((resolve) => {
    chrome.storage.local.get(['syncRootId', 'syncRootTitle'], resolve);
  });

  if (syncRootId) {
    const exists = topLevelFolders.some((f) => f.id === syncRootId);
    if (exists) {
      folderPicker.value = syncRootId;
      clearRootBtn.classList.remove('hidden');
    } else {
      chrome.storage.local.remove(['syncRootId', 'syncRootTitle']);
    }
  }
}

async function showSyncView(email) {
  authView.classList.add('hidden');
  syncView.classList.remove('hidden');
  userEmail.textContent = email;

  await populateFolderPicker();

  chrome.storage.local.get(['lastSync'], (result) => {
    if (result.lastSync) {
      lastSync.textContent = `Last sync: ${new Date(result.lastSync).toLocaleString()}`;
    }
  });
}

function showAuthView() {
  syncView.classList.add('hidden');
  authView.classList.remove('hidden');
}

async function handleAuth(action) {
  const email = emailInput.value.trim();
  const password = passwordInput.value.trim();

  if (!email || !password) {
    authError.textContent = 'Please enter email and password.';
    authError.classList.remove('hidden');
    return;
  }

  authError.classList.add('hidden');

  try {
    const data = await apiCall(`/${action}`, 'POST', { email, password });
    const token = data.token;

    chrome.storage.local.set({ token, email }, () => {
      showSyncView(email);
    });
  } catch (err) {
    authError.textContent = err.message;
    authError.classList.remove('hidden');
  }
}

loginBtn.addEventListener('click', () => handleAuth('login'));
registerBtn.addEventListener('click', () => handleAuth('register'));

folderPicker.addEventListener('change', () => {
  const value = folderPicker.value;
  if (value) {
    const title = folderPicker.options[folderPicker.selectedIndex].textContent;
    chrome.storage.local.set({ syncRootId: value, syncRootTitle: title });
    clearRootBtn.classList.remove('hidden');
  } else {
    chrome.storage.local.remove(['syncRootId', 'syncRootTitle']);
    clearRootBtn.classList.add('hidden');
  }
});

clearRootBtn.addEventListener('click', () => {
  folderPicker.value = '';
  chrome.storage.local.remove(['syncRootId', 'syncRootTitle']);
  clearRootBtn.classList.add('hidden');
});

logoutBtn.addEventListener('click', () => {
  chrome.storage.local.get(['token'], async (result) => {
    try {
      await apiCall('/logout', 'POST', null, result.token);
    } catch (_) { /* ignore */ }

    chrome.storage.local.remove(['token', 'email', 'lastSync', 'syncRootId', 'syncRootTitle'], () => {
      showAuthView();
    });
  });
});

syncBtn.addEventListener('click', async () => {
  syncError.classList.add('hidden');
  syncStatus.textContent = 'Syncing...';

  chrome.storage.local.get(['token', 'syncRootId'], async (result) => {
    if (!result.token) {
      syncError.textContent = 'Not authenticated.';
      syncError.classList.remove('hidden');
      return;
    }

    try {
      const tree = await chrome.runtime.sendMessage({
        action: 'getBookmarkTree',
        syncRootId: result.syncRootId || null,
      });

      const data = await apiCall('/bookmarks/sync', 'POST', tree, result.token);

      syncStatus.textContent = `Created: ${data.created}, Updated: ${data.updated}, Deleted: ${data.deleted}`;
      chrome.storage.local.set({ lastSync: Date.now() }, () => {
        lastSync.textContent = `Last sync: ${new Date().toLocaleString()}`;
      });
    } catch (err) {
      syncError.textContent = err.message;
      syncError.classList.remove('hidden');
    }
  });
});

chrome.storage.local.get(['token', 'email', 'lastSync'], (result) => {
  if (result.token && result.email) {
    showSyncView(result.email);
    if (result.lastSync) {
      lastSync.textContent = `Last sync: ${new Date(result.lastSync).toLocaleString()}`;
    }
  }
});
