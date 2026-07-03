const API_BASE = 'http://localhost:8000/api/v1';

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

async function apiCall(endpoint, method = 'POST', body = null, token = null) {
  const headers = { 'Content-Type': 'application/json' };
  if (token) headers['Authorization'] = `Bearer ${token}`;

  const res = await fetch(`${API_BASE}${endpoint}`, {
    method,
    headers,
    body: body ? JSON.stringify(body) : undefined,
  });

  const data = await res.json();
  if (!res.ok) throw new Error(data.message || 'Request failed');
  return data;
}

async function showSyncView(email) {
  authView.classList.add('hidden');
  syncView.classList.remove('hidden');
  userEmail.textContent = email;

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

logoutBtn.addEventListener('click', () => {
  chrome.storage.local.get(['token'], async (result) => {
    try {
      await apiCall('/logout', 'POST', null, result.token);
    } catch (_) { /* ignore */ }

    chrome.storage.local.remove(['token', 'email', 'lastSync'], () => {
      showAuthView();
    });
  });
});

syncBtn.addEventListener('click', async () => {
  syncError.classList.add('hidden');
  syncStatus.textContent = 'Syncing...';

  chrome.storage.local.get(['token'], async (result) => {
    if (!result.token) {
      syncError.textContent = 'Not authenticated.';
      syncError.classList.remove('hidden');
      return;
    }

    try {
      const tabs = await chrome.tabs.query({ active: true, currentWindow: true });

      const tree = await chrome.runtime.sendMessage({ action: 'getBookmarkTree' });

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
