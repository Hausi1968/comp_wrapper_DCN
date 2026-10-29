<?php

/**
 * @package     Compwrapper
 * @license     GNU General Public License version 2 or later
 */

namespace Hausi\Component\Compwrapper\Site\View\Wrapper;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Registry\Registry;

class HtmlView extends BaseHtmlView
{
    /** @var Registry */
    protected $params;

    /** @var string  Validated URL of the embedded page, empty if invalid */
    protected $url = '';

    /** @var array  Prepared iframe/layout settings for the template */
    protected $frame = [];

    public function display($tpl = null): void
    {
        $app          = Factory::getApplication();
        $this->params = $app->getParams();

        $this->url   = $this->validateUrl(trim((string) $this->params->get('url', '')));
        $this->frame = $this->buildFrameSettings();

        $this->prepareDocument();

        parent::display($tpl);
    }

    /**
     * Allows http(s) URLs and site-relative paths, optionally limited to a host whitelist.
     */
    protected function validateUrl(string $url): string
    {
        if ($url === '') {
            return '';
        }

        // Site-relative path such as /shop/index.html
        if ($url[0] === '/' && (\strlen($url) === 1 || $url[1] !== '/')) {
            return $url;
        }

        $parts = parse_url($url);

        if (!$parts || empty($parts['host']) || !\in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)) {
            return '';
        }

        $allowed = array_filter(array_map('trim', preg_split('/[\s,]+/', strtolower((string) $this->params->get('allowed_hosts', '')))));

        if ($allowed) {
            $host = strtolower($parts['host']);
            $ok   = false;

            foreach ($allowed as $entry) {
                if ($host === $entry || substr($host, -\strlen('.' . $entry)) === '.' . $entry) {
                    $ok = true;
                    break;
                }
            }

            if (!$ok) {
                return '';
            }
        }

        if ($this->params->get('force_https', 0)) {
            $url = preg_replace('#^http://#i', 'https://', $url);
        }

        return $url;
    }

    protected function buildFrameSettings(): array
    {
        $p = $this->params;

        $style = $p->get('frame_style', '');
        $style = \in_array($style, ['plain', 'card', 'soft'], true) ? $style : $p->get('style', 'soft');

        $radius = $p->get('frame_radius', '');
        $radius = $radius === '' ? (int) $p->get('radius', 18) : (int) $radius;

        $accent = $p->get('frame_accent', '') ?: $p->get('accent', '#0071e3');
        $accent = preg_match('/^#[0-9a-f]{3,8}$/i', $accent) ? $accent : '#0071e3';

        $sandbox = '';

        switch ($p->get('sandbox', 'none')) {
            case 'strict':
                $sandbox = 'allow-scripts allow-forms allow-popups';
                break;
            case 'same-origin':
                $sandbox = 'allow-scripts allow-forms allow-popups allow-same-origin';
                break;
            case 'custom':
                $sandbox = preg_replace('/[^a-z\- ]/', '', strtolower((string) $p->get('sandbox_custom', '')));
                break;
        }

        $loaderGlobal = (int) $p->get('show_loader', 1);
        $loader       = $p->get('frame_loader', '');

        return [
            'mode'        => \in_array($p->get('height_mode', 'fixed'), ['fixed', 'auto', 'viewport'], true) ? $p->get('height_mode', 'fixed') : 'fixed',
            'height'      => max(50, (int) $p->get('height', 800)),
            'minHeight'   => max(0, (int) $p->get('min_height', 300)),
            'offset'      => max(0, (int) $p->get('viewport_offset', 0)),
            'width'       => preg_match('/^\d+(\.\d+)?(px|%|vw|rem|em)?$/', (string) $p->get('width', '100%')) ? (string) $p->get('width', '100%') : '100%',
            'scrolling'   => \in_array($p->get('scrolling', 'auto'), ['auto', 'yes', 'no'], true) ? $p->get('scrolling', 'auto') : 'auto',
            'title'       => $p->get('frame_title', '') ?: ($p->get('page_title', '') ?: Text::_('COM_COMPWRAPPER_DEFAULT_FRAME_TITLE')),
            'loading'     => $p->get('loading', 'lazy') === 'eager' ? 'eager' : 'lazy',
            'fullscreen'  => (bool) $p->get('allowfullscreen', 1),
            'sandbox'     => $sandbox,
            'referrer'    => $p->get('referrerpolicy', 'strict-origin-when-cross-origin'),
            'style'       => $style,
            'radius'      => max(0, min(48, $radius)),
            'accent'      => $accent,
            'toolbar'     => (bool) $p->get('show_toolbar', 0),
            'loader'      => $loader === '' ? (bool) $loaderGlobal : (bool) $loader,
        ];
    }

    protected function prepareDocument(): void
    {
        $app   = Factory::getApplication();
        $menu  = $app->getMenu()->getActive();
        $title = $this->params->get('page_title', '');

        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        }

        if ($title === '') {
            $title = $menu->title ?? $app->get('sitename');
        }

        $this->setDocumentTitle($title);

        if ($this->params->get('menu-meta_description')) {
            Factory::getApplication()->getDocument()->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('robots')) {
            Factory::getApplication()->getDocument()->setMetaData('robots', $this->params->get('robots'));
        }

        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->getRegistry()->addExtensionRegistryFile('com_compwrapper');
        $wa->useStyle('com_compwrapper.wrapper')
            ->useScript('com_compwrapper.wrapper');
    }
}
