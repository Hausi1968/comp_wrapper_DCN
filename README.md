# COMP-WRAPPER (com_compwrapper)

Joomla-4/5-Komponente, die eine Webseite per iframe in die eigene Website einbindet.
Jede eingebundene Seite ist ein eigener Menüeintrag mit eigener URL und eigenen Einstellungen.

## Installation

1. `./build.sh` ausführen, das erzeugt `dist/com_compwrapper-<version>.zip`.
2. In Joomla unter **System › Installieren › Erweiterungen** das ZIP hochladen.
3. Unter **Menüs** einen neuen Eintrag vom Typ **COMP-WRAPPER › Seite einbinden** anlegen.

## Funktionen

- URL extern (http/https) oder als Pfad der eigenen Website (`/shop/`)
- Breite in px oder % (der Inhaltsbreite), Höhe in px, in % (des freien Platzes von der iframe-Oberkante bis zum Fensterrand; 100 % passt immer genau ins Fenster, auch bei Größenänderung) oder automatisch nach Inhalt; Mindesthöhe, Lazy-Loading
- Scrollbalken horizontal und vertikal getrennt: automatisch, immer oder nie
- Design: schlicht, Karte oder Soft (abgerundet, sanfter Schatten, Dark Mode), Eckenradius, Akzentfarbe,
  Ladeanimation, optionale Titelleiste mit „In neuem Fenster öffnen“
- Stil „Homepage-Design (DCN)“: nutzt die Klassen der Vereins-Homepage (`dcn-page`, `dcn-hero`, `dcn-intro`, `dcn-card`) mit rotem Kopfbalken (Titel wahlweise aus Menüeintrag/Linktitel, iframe-Titel oder eigenem Text), Unterzeile und Einleitungstext, damit das Template den Wrapper wie die übrigen Seiten gestaltet
- Sandbox, Vollbild, Referrer-Policy
- Globale Optionen: erlaubte Domains (Whitelist), HTTPS erzwingen, Standard-Design

## Automatische Höhe und Scrollbalken bei fremden Domains

Liegt die eingebundene Seite auf derselben Domain, misst die Komponente die Höhe selbst und setzt die Scrollbalken direkt.
Bei einer anderen Domain muss die eingebundene Seite mitspielen. Dazu dieses Skript vor `</body>` einfügen:

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
  window.addEventListener('message', function (e) {
    var d = e.data, ok = ['auto', 'scroll', 'hidden'];
    if (e.source !== parent || !d || d.type !== 'compwrapper:scroll') { return; }
    if (ok.indexOf(d.x) > -1) { document.documentElement.style.overflowX = d.x; }
    if (ok.indexOf(d.y) > -1) { document.documentElement.style.overflowY = d.y; }
    send();
  });
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
