# COMP-WRAPPER (com_compwrapper)

Joomla-4/5-Komponente, die eine Webseite per iframe in die eigene Website einbindet.
Jede eingebundene Seite ist ein eigener Menüeintrag mit eigener URL und eigenen Einstellungen.

## Installation

1. `./build.sh` ausführen, das erzeugt `dist/com_compwrapper-<version>.zip`.
2. In Joomla unter **System › Installieren › Erweiterungen** das ZIP hochladen.
3. Unter **Menüs** einen neuen Eintrag vom Typ **COMP-WRAPPER › Seite einbinden** anlegen.

## Funktionen

- URL extern (http/https) oder als Pfad der eigenen Website (`/shop/`)
- Höhe: fest, automatisch oder Bildschirmhöhe minus Versatz; Mindesthöhe, Breite, Scrollbalken, Lazy-Loading
- Design: schlicht, Karte oder Soft (abgerundet, sanfter Schatten, Dark Mode), Eckenradius, Akzentfarbe,
  Ladeanimation, optionale Titelleiste mit „In neuem Fenster öffnen“
- Sandbox, Vollbild, Referrer-Policy
- Globale Optionen: erlaubte Domains (Whitelist), HTTPS erzwingen, Standard-Design

## Automatische Höhe bei fremden Domains

Liegt die eingebundene Seite auf derselben Domain, misst die Komponente die Höhe selbst.
Bei einer anderen Domain muss die eingebundene Seite ihre Höhe melden. Dazu dieses Skript vor `</body>` einfügen:

```html
<script>
(function () {
  function send() {
    var h = Math.max(document.documentElement.scrollHeight, document.body.scrollHeight);
    parent.postMessage({ type: 'compwrapper:height', height: h }, '*');
  }
  window.addEventListener('load', send);
  window.addEventListener('resize', send);
  if (window.ResizeObserver) { new ResizeObserver(send).observe(document.body); }
})();
</script>
```

## Aufbau

```
com_compwrapper/
  compwrapper.xml          Manifest
  administrator/           Backend: Optionen, Infoseite, Sprachdateien
  site/                    Frontend: View, Template, Menüeintragstyp (tmpl/wrapper/default.xml)
  media/                   CSS (Design-Tokens) und JS (Loader, Auto-Höhe)
```

Lizenz: GNU GPL v2 oder später.
