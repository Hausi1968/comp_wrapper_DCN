<?php

/**
 * @package     Compwrapper
 * @license     GNU General Public License version 2 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$snippet = <<<'JS'
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
JS;
?>
<div class="card mb-3">
	<div class="card-body">
		<h2 class="h4"><?php echo Text::_('COM_COMPWRAPPER_DASHBOARD_HEADING'); ?></h2>
		<p><?php echo Text::_('COM_COMPWRAPPER_DASHBOARD_INTRO'); ?></p>
		<ol>
			<li><?php echo Text::_('COM_COMPWRAPPER_DASHBOARD_STEP1'); ?></li>
			<li><?php echo Text::_('COM_COMPWRAPPER_DASHBOARD_STEP2'); ?></li>
			<li><?php echo Text::_('COM_COMPWRAPPER_DASHBOARD_STEP3'); ?></li>
		</ol>
		<a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_menus&view=items'); ?>">
			<?php echo Text::_('COM_COMPWRAPPER_DASHBOARD_TO_MENUS'); ?>
		</a>
	</div>
</div>
<div class="card">
	<div class="card-body">
		<h3 class="h5"><?php echo Text::_('COM_COMPWRAPPER_DASHBOARD_AUTOHEIGHT_HEADING'); ?></h3>
		<p><?php echo Text::_('COM_COMPWRAPPER_DASHBOARD_AUTOHEIGHT_INTRO'); ?></p>
		<pre class="p-3 bg-light border rounded"><code><?php echo htmlspecialchars($snippet, ENT_QUOTES, 'UTF-8'); ?></code></pre>
	</div>
</div>
