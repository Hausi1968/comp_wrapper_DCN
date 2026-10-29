<?php

/**
 * @package     Compwrapper
 * @license     GNU General Public License version 2 or later
 */

namespace Hausi\Component\Compwrapper\Administrator\View\Dashboard;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
    public function display($tpl = null): void
    {
        ToolbarHelper::title(Text::_('COM_COMPWRAPPER'), 'window-maximize');

        if (Factory::getApplication()->getIdentity()->authorise('core.admin', 'com_compwrapper')) {
            ToolbarHelper::preferences('com_compwrapper');
        }

        parent::display($tpl);
    }
}
