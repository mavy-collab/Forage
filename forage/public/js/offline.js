/**
 * Gestion offline — PWA Forage
 * - Enregistre le Service Worker
 * - Intercepte les soumissions de formulaire quand hors ligne
 * - Met en file d'attente dans localStorage
 * - Synchronise automatiquement à la reconnexion
 */

const QUEUE_KEY    = 'forage_offline_queue';
const SYNC_ROUTES  = ['/ventes', '/depenses', '/ardoise'];

// ─── Service Worker ──────────────────────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => {
                // Le SW nous demande de synchroniser
                navigator.serviceWorker.addEventListener('message', event => {
                    if (event.data?.type === 'SW_SYNC_REQUESTED') syncQueue();
                });
            })
            .catch(err => console.warn('SW non enregistré :', err));
    });
}

// ─── File d'attente (localStorage) ──────────────────────────────
function getQueue()        { try { return JSON.parse(localStorage.getItem(QUEUE_KEY) || '[]'); } catch { return []; } }
function saveQueue(queue)  { localStorage.setItem(QUEUE_KEY, JSON.stringify(queue)); }

function enqueue(item) {
    const queue = getQueue();
    queue.push({ ...item, _id: Date.now(), _at: new Date().toISOString() });
    saveQueue(queue);
    refreshBadge();
}

function dequeue(id) {
    saveQueue(getQueue().filter(i => i._id !== id));
    refreshBadge();
}

// ─── CSRF frais ──────────────────────────────────────────────────
async function freshCsrfToken() {
    try {
        const r = await fetch('/api/refresh-csrf', { credentials: 'include' });
        if (!r.ok) return null;
        const data = await r.json();
        return data.token || null;
    } catch {
        return null;
    }
}

// ─── Synchronisation ────────────────────────────────────────────
async function syncQueue() {
    const queue = getQueue();
    if (!queue.length) return;

    showToast('sync', 'Synchronisation en cours…');

    let ok = 0, fail = 0;

    for (const item of queue) {
        const token = await freshCsrfToken();
        if (!token) { fail++; continue; }   // session expirée, on arrête

        try {
            const fd = new FormData();
            Object.entries(item.data).forEach(([k, v]) => fd.append(k, v));
            fd.set('_token', token);

            const resp = await fetch(item.action, {
                method: 'POST',
                body: fd,
                credentials: 'include',
                redirect: 'follow',
            });

            // 200, 201, 302 (redirect après succès) = OK
            if (resp.ok || resp.redirected || resp.status === 302) {
                dequeue(item._id);
                ok++;
            } else if (resp.status === 422) {
                // Données invalides → on retire quand même (évite boucle infinie)
                dequeue(item._id);
                fail++;
            }
            // 401/403/5xx → on garde pour retry
        } catch {
            fail++;
        }
    }

    if (ok > 0) {
        showToast('success', `${ok} enregistrement(s) synchronisé(s) avec succès.`);
        setTimeout(() => window.location.reload(), 1800);
    } else if (fail > 0 && getQueue().length === 0) {
        hideToast();
    } else if (fail > 0) {
        showToast('error', 'Certains enregistrements n\'ont pas pu être synchronisés.');
    } else {
        hideToast();
    }
}

// ─── Interception des formulaires ────────────────────────────────
function shouldIntercept(action) {
    try {
        const path = new URL(action, location.origin).pathname;
        return SYNC_ROUTES.some(r => path === r || path === r + '/');
    } catch { return false; }
}

function labelFor(action) {
    const path = new URL(action, location.origin).pathname;
    if (path.startsWith('/ventes'))   return 'Vente';
    if (path.startsWith('/depenses')) return 'Dépense';
    if (path.startsWith('/ardoise'))  return 'Ardoise';
    return 'Enregistrement';
}

function serializeForm(form) {
    const fd = new FormData(form), data = {};
    for (const [k, v] of fd.entries()) {
        if (k !== '_token') data[k] = v;
    }
    return data;
}

document.addEventListener('DOMContentLoaded', () => {

    // Interception submit
    document.addEventListener('submit', async e => {
        const form = e.target;
        if (!form.action || form.dataset.noOffline !== undefined) return;
        if (!shouldIntercept(form.action)) return;
        if (navigator.onLine) return; // en ligne → soumission normale

        e.preventDefault();

        const label = labelFor(form.action);
        enqueue({ action: form.action, data: serializeForm(form), label });

        showToast('offline', `${label} enregistrée localement — sera synchronisée dès la reconnexion.`);

        // Background Sync si disponible
        if ('serviceWorker' in navigator && 'SyncManager' in window) {
            try {
                const reg = await navigator.serviceWorker.ready;
                await reg.sync.register('forage-sync');
            } catch {}
        }

        form.reset();
    });

    // Détection connexion
    window.addEventListener('online',  () => { updateStatus(true);  syncQueue(); });
    window.addEventListener('offline', () => { updateStatus(false); });

    updateStatus(navigator.onLine);
    refreshBadge();

    // Sync automatique au chargement si en ligne et file non vide
    if (navigator.onLine && getQueue().length > 0) {
        syncQueue();
    }
});

// ─── UI ──────────────────────────────────────────────────────────
function updateStatus(online) {
    const el = document.getElementById('conn-status');
    if (!el) return;
    el.className = online ? 'conn-badge conn-online' : 'conn-badge conn-offline';
    el.innerHTML  = online
        ? '<i class="fas fa-wifi"></i> En ligne'
        : '<i class="fas fa-wifi" style="opacity:.4"></i> Hors ligne';
}

function refreshBadge() {
    const n = getQueue().length;
    const el = document.getElementById('sync-count');
    if (!el) return;
    el.style.display = n > 0 ? 'inline-flex' : 'none';
    el.textContent   = n;
}

let toastTimer = null;
function showToast(type, msg) {
    const el = document.getElementById('offline-toast');
    if (!el) return;
    const icons = { offline: '💾', sync: '🔄', success: '✅', error: '⚠️' };
    el.innerHTML  = `<span>${icons[type] || ''}</span> ${msg}`;
    el.className  = `offline-toast toast-${type} show`;
    clearTimeout(toastTimer);
    if (type === 'offline' || type === 'success') {
        toastTimer = setTimeout(hideToast, 5000);
    }
}

function hideToast() {
    const el = document.getElementById('offline-toast');
    if (el) el.classList.remove('show');
}
