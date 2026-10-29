<?php

/**
 * @package     Compwrapper
 * @license     GNU General Public License version 2 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/** @var \Hausi\Component\Compwrapper\Site\View\Wrapper\HtmlView $this */

$f = $this->frame;

$height = $f['mode'] === 'viewport' ? 'calc(100vh - ' . $f['offset'] . 'px)' : $f['height'] . 'px';

$wrapStyle = sprintf(
    '--cw-radius:%dpx;--cw-accent:%s;--cw-height:%s;--cw-min-height:%dpx;--cw-width:%s;',
    $f['radius'],
    $f['accent'],
    $height,
    $f['minHeight'],
    $f['width']
);
?>
<div class="com-compwrapper">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		</div>
	<?php endif; ?>

	<?php if ($this->url === '') : ?>
		<div class="alert alert-warning"><?php echo Text::_('COM_COMPWRAPPER_ERROR_NO_URL'); ?></div>
	<?php else : ?>
		<div class="cw-frame cw-style-<?php echo $f['style']; ?><?php echo $f['loader'] ? ' cw-loading' : ''; ?>"
			style="<?php echo $this->escape($wrapStyle); ?>"
			data-cw-mode="<?php echo $f['mode']; ?>"
			data-cw-min="<?php echo $f['minHeight']; ?>">

			<?php if ($f['toolbar']) : ?>
				<div class="cw-toolbar">
					<span class="cw-toolbar-title"><?php echo $this->escape($f['title']); ?></span>
					<a class="cw-toolbar-link" href="<?php echo $this->escape($this->url); ?>" target="_blank" rel="noopener noreferrer">
						<?php echo Text::_('COM_COMPWRAPPER_OPEN_NEW_WINDOW'); ?>
						<span aria-hidden="true">↗</span>
					</a>
				</div>
			<?php endif; ?>

			<div class="cw-body">
				<?php if ($f['loader']) : ?>
					<div class="cw-loader" aria-hidden="true"><span></span></div>
				<?php endif; ?>
				<iframe class="cw-iframe"
					src="<?php echo $this->escape($this->url); ?>"
					title="<?php echo $this->escape($f['title']); ?>"
					loading="<?php echo $f['loading']; ?>"
					scrolling="<?php echo $f['scrolling']; ?>"
					referrerpolicy="<?php echo $this->escape($f['referrer']); ?>"
					<?php echo $f['sandbox'] !== '' ? 'sandbox="' . $this->escape($f['sandbox']) . '"' : ''; ?>
					<?php echo $f['fullscreen'] ? 'allowfullscreen allow="fullscreen"' : ''; ?>></iframe>
			</div>
		</div>
		<noscript>
			<p><a href="<?php echo $this->escape($this->url); ?>"><?php echo Text::_('COM_COMPWRAPPER_OPEN_NEW_WINDOW'); ?></a></p>
		</noscript>
	<?php endif; ?>
</div>
