/* Service Worker - SI-KOPMA (PWA)
   Cache sederhana agar aplikasi bisa dibuka & di-install di HP. */
const CACHE_NAME = "kopma-npm-v3";
const CORE_ASSETS = [
    "/",
    "/manifest.json",
    "/icon-192.png",
    "/icon-512.png",
    "/apple-touch-icon.png"
];

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(CORE_ASSETS)).then(() => self.skipWaiting())
    );
});

self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(
            keys.map((k) => { if (k !== CACHE_NAME) return caches.delete(k); })
        )).then(() => self.clients.claim())
    );
});

self.addEventListener("fetch", (event) => {
    const req = event.request;
    if (req.method !== "GET") return;
    // Jangan cache endpoint live-reload & request non-http
    if (req.url.indexOf("/live-reload-hash") !== -1) return;
    event.respondWith(
        fetch(req)
            .then((res) => {
                const copy = res.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(req, copy));
                return res;
            })
            .catch(() => caches.match(req).then((cached) => cached || caches.match("/")))
    );
});