/**
 * COMP-WRAPPER: loader handling and automatic iframe height.
 *
 * Auto height works in two ways:
 *  - same origin: the height is read from the embedded document directly
 *  - other origin: the embedded page sends postMessage({type: 'compwrapper:height', height: n})
 */
(function () {
  'use strict';

  function setHeight(frame, iframe, height) {
    var min = parseInt(frame.getAttribute('data-cw-min'), 10) || 0;
    iframe.style.height = Math.max(min, Math.ceil(height)) + 'px';
  }

  function sameOriginHeight(iframe) {
    try {
      var doc = iframe.contentDocument || iframe.contentWindow.document;
      if (!doc || !doc.body) {
        return 0;
      }
      return Math.max(doc.documentElement.scrollHeight, doc.body.scrollHeight);
    } catch (e) {
      return 0; // cross origin, rely on postMessage
    }
  }

  function init(frame) {
    var iframe = frame.querySelector('.cw-iframe');
    if (!iframe) {
      return;
    }

    var auto = frame.getAttribute('data-cw-mode') === 'auto';

    var done = false;

    function onLoad() {
      if (done) {
        return;
      }
      done = true;
      frame.classList.remove('cw-loading');

      if (!auto) {
        return;
      }

      var h = sameOriginHeight(iframe);
      if (h) {
        setHeight(frame, iframe, h);

        try {
          var doc = iframe.contentDocument;
          if (window.ResizeObserver && doc && doc.body) {
            new ResizeObserver(function () {
              setHeight(frame, iframe, sameOriginHeight(iframe));
            }).observe(doc.body);
          }
        } catch (e) { /* ignore */ }
      }
    }

    iframe.addEventListener('load', onLoad);

    // The script is deferred, so a same-origin page may already be loaded
    try {
      var d = iframe.contentDocument;
      if (d && d.readyState === 'complete' && d.location.href !== 'about:blank') {
        onLoad();
      }
    } catch (e) { /* cross origin */ }

    // Fallback: hide the loader even if "load" never fires (blocked by X-Frame-Options etc.)
    setTimeout(function () { frame.classList.remove('cw-loading'); }, 8000);

    if (auto) {
      window.addEventListener('message', function (event) {
        if (event.source !== iframe.contentWindow || !event.data) {
          return;
        }
        var data = event.data;
        if (typeof data === 'string') {
          try { data = JSON.parse(data); } catch (e) { return; }
        }
        if (data && data.type === 'compwrapper:height' && isFinite(data.height)) {
          setHeight(frame, iframe, Number(data.height));
        }
      });
    }
  }

  function boot() {
    document.querySelectorAll('.com-compwrapper .cw-frame').forEach(init);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
