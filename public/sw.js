// Service Worker for H Tech Provision Web Push Notifications
self.addEventListener('push', function(event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    let payload = {
        title: 'এইচ টেক প্রোভিশন ব্রেকিং নিউজ',
        body: 'নতুন খবর প্রকাশিত হয়েছে। বিস্তারিত জানতে ট্যাপ করুন।',
        icon: '/favicon.ico',
        url: '/'
    };

    if (event.data) {
        try {
            payload = event.data.json();
        } catch (e) {
            payload.body = event.data.text();
        }
    }

    const options = {
        body: payload.body,
        icon: payload.icon || '/favicon.ico',
        badge: '/favicon.ico',
        image: payload.image || null,
        data: {
            url: payload.url || '/'
        },
        vibrate: [100, 50, 100],
        requireInteraction: false
    };

    event.waitUntil(
        self.registration.showNotification(payload.title, options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const url = event.notification.data ? event.notification.data.url : '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(windowClients) {
            for (let i = 0; i < windowClients.length; i++) {
                const client = windowClients[i];
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});
