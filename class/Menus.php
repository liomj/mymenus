<?php

namespace XoopsModules\Mymenus;

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @package         Mymenus
 * @since           1.0
 * @author          trabis <lusopoemas@gmail.com>
 */

use Xmf\Request;
use XoopsModules\Mymenus;

//require  \dirname(__DIR__) . '/include/common.php';

/**
 * Class Menus
 */
class Menus extends \XoopsObject
{
    /**
     * @var Menus
     * @access private
     */
    private $helper;
    private $db;

    /**
     * constructor
     */
    public function __construct()
    {
        /** @var \XoopsModules\Mymenus\Helper $this ->helper */
        $this->helper = Helper::getInstance();
        $this->db     = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('id', \XOBJ_DTYPE_INT);
        $this->initVar('title', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('css', \XOBJ_DTYPE_TXTBOX);
    }

    /**
     * Get {@link XoopsThemeForm} for adding/editing items
     *
     * @param bool|string $action
     * @return \XoopsThemeForm <a href='psi_element://XoopsThemeForm'>XoopsThemeForm</a>
     */
    public function getForm($action = false)
    {
        /** @var \XoopsGroupPermHandler $grouppermHandler */
        $grouppermHandler = \xoops_getHandler('groupperm');
        //
        \xoops_load('XoopsFormLoader');

        if (false === $action) {
            //            $action = $_SERVER['REQUEST_URI'];
            $action = Request::getString('REQUEST_URI', '', 'SERVER');
        }
        //
        //        $isAdmin = mymenusUserIsAdmin();
        //        $groups  = is_object($GLOBALS['xoopsUser']) ? $GLOBALS['xoopsUser']->getGroups() : array(0 => XOOPS_GROUP_ANONYMOUS);
        //
        $title = $this->isNew() ? \_AM_MYMENUS_MENUS_ADD : \_AM_MYMENUS_MENUS_EDIT;

        $form = new \XoopsThemeForm($title, 'moneusform', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');
        // menus: title
        $menusTitleText = new \XoopsFormText(\_AM_MYMENUS_MENU_TITLE, 'title', 50, 255, $this->getVar('title', 'e'));
        $menusTitleText->setDescription(\_AM_MYMENUS_MENU_TITLE_DESC);
        $form->addElement($menusTitleText, true);
        // menus: css
        $menusCssText = new \XoopsFormText(\_AM_MYMENUS_MENU_CSS, 'css', 50, 255, $this->getVar('css', 'e'));
        $menusCssText->setDescription(\_AM_MYMENUS_MENU_CSS_DESC);
        $form->addElement($menusCssText, false);
        // form: button tray
        $buttonTray = new \XoopsFormElementTray('', '');
        $buttonTray->addElement(new \XoopsFormHidden('op', 'save'));

        $buttonSubmit = new \XoopsFormButton('', '', _SUBMIT, 'submit');
        $buttonSubmit->setExtra('onclick="this.form.elements.op.value=\'save\'"');
        $buttonTray->addElement($buttonSubmit);
        if ($this->isNew()) {
            // NOP
        } else {
            $form->addElement(new \XoopsFormHidden('id', (int)$this->getVar('id')));

            $buttonDelete = new \XoopsFormButton('', '', _DELETE, 'submit');
            $buttonDelete->setExtra('onclick="this.form.elements.op.value=\'delete\'"');
            $buttonTray->addElement($buttonDelete);
        }
        $buttonReset = new \XoopsFormButton('', '', _RESET, 'reset');
        $buttonTray->addElement($buttonReset);

        $buttonCancel = new \XoopsFormButton('', '', _CANCEL, 'button');
        $buttonCancel->setExtra('onclick="history.go(-1)"');
        $buttonTray->addElement($buttonCancel);

        $form->addElement($buttonTray);

        return $form;
    }
}
