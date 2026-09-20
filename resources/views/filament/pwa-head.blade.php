{{-- Kelvs Admin PWA Integration --}}
<link rel="manifest" href="{{ asset('admin.webmanifest') }}?v=2">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/pwa/kelvs-apple-touch-icon.png') }}?v=2">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/pwa/kelvs-icon-192.png') }}?v=2">

{{-- iOS Web App Meta Tags --}}
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Kelvs Admin">
<meta name="theme-color" content="#000000">
<meta name="mobile-web-app-capable" content="yes">

{{-- Mobile Touch Optimizations & Safe Areas --}}
<style>
    /* Prevent delay and double-tap zoom on buttons and links */
    button, a, input, select, textarea, [role="button"] {
        touch-action: manipulation;
    }

    /* Support iOS Notch & Home Bar in Standalone Mode */
    @media all and (display-mode: standalone) {
        body {
            padding-top: env(safe-area-inset-top, 0px);
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
    }
</style>

{{-- Register Admin Service Worker --}}
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw-admin.js', { scope: '/' })
                .then(function(registration) {
                    console.debug('Kelvs Admin PWA ServiceWorker registered with scope:', registration.scope);
                })
                .catch(function(error) {
                    console.debug('Kelvs Admin ServiceWorker registration note:', error);
                });
        });
    }
</script>
