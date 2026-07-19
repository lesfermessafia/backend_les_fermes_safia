import { playAlertSound } from './alert-sound';

const userId = document.body.dataset.userId;
const count = document.getElementById('notification-count');
const unreadUrl = document.body.dataset.notificationsUnreadUrl;
const notificationReadUrl = document.body.dataset.notificationReadUrl;

let previousCount = null;
let skipCountSound = false;

function updatePreview(notifications = []) {
    const preview = document.getElementById('notification-preview');
    if (!preview || !notifications.length) return;

    const footer = preview.querySelector('a[href*="notifications"]:last-child');
    preview.querySelectorAll('[data-live-notification]').forEach((element) => element.remove());
    notifications.forEach((notification) => {
        const item = document.createElement('a');
        item.dataset.liveNotification = notification.id;
        item.href = notificationReadUrl?.replace('__ID__', notification.id) ?? '#';
        item.className = 'block p-2 rounded-lg hover:bg-gray-50';
        item.innerHTML = `<p class="text-sm font-semibold"></p><p class="text-xs text-gray-500 truncate"></p>`;
        item.querySelector('p').textContent = notification.title;
        item.querySelectorAll('p')[1].textContent = notification.message;
        footer?.before(item);
    });
}

function setCount(value) {
    if (!count) return;
    const numericValue = Number(value) || 0;
    count.textContent = numericValue > 99 ? '99+' : String(numericValue);
    count.classList.toggle('hidden', numericValue === 0);
    count.classList.toggle('flex', numericValue > 0);
}

async function syncCount() {
    if (!unreadUrl) return;
    try {
        const response = await fetch(unreadUrl, { headers: { Accept: 'application/json' } });
        if (!response.ok) return;
        const data = await response.json();
        setCount(data.count);
        updatePreview(data.notifications);

        if (previousCount !== null && data.count > previousCount && !skipCountSound) {
            playAlertSound('notification');
        }
        skipCountSound = false;
        previousCount = data.count;
    } catch (exception) {
        console.debug('Synchronisation des notifications indisponible.', exception);
    }
}

const handledNotifications = new Set();

function showNotification(notification) {
    const payload = notification.data ?? notification;
    const notificationId = notification.id ?? payload.id;
    if (notificationId && handledNotifications.has(notificationId)) return;
    if (notificationId) handledNotifications.add(notificationId);
    skipCountSound = true;
    playAlertSound('notification');
    syncCount();

    const toast = document.getElementById('notification-toast');
    const title = document.getElementById('notification-toast-title');
    const message = document.getElementById('notification-toast-message');

    if (title) title.textContent = payload.title ?? 'Nouvelle notification';
    if (message) message.textContent = payload.message ?? '';
    if (toast) {
        toast.classList.remove('hidden');
        window.setTimeout(() => toast.classList.add('hidden'), 6000);
    }

    if (typeof Notification !== 'undefined' && Notification.permission === 'granted') {
        new Notification(payload.title ?? 'Nouvelle notification', {
            body: payload.message ?? '',
        });
    }
}

if (userId && window.Echo) {
    const channel = window.Echo.private(`App.Models.User.${userId}`);
    channel.notification(showNotification);
    channel.listen('.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', showNotification);
}

syncCount();
window.setInterval(syncCount, 5000);
