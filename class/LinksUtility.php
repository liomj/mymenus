<?php declare(strict_types=1);

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
 * @since           1.0
 * @author          trabis <lusopoemas@gmail.com>, bleekk <bleekk@outlook.com>
 */

use Xmf\Module\Admin;
use Xmf\Request;

/** @var Helper $helper */
/** @var LinksHandler $linksHandler */

/**
 * Class LinksUtility
 */
class LinksUtility
{
    /**
     * Display the links in a menu
     *
     * @param int $start
     * @param int $mid
     *
     * @return bool|mixed|string
     */
    public static function listLinks($start, $mid)
    {
        $helper = Helper::getInstance();

        global $mymenusTpl;

        $linksCriteria = new \CriteriaCompo(new \Criteria('mid', (int)$mid));
        $linksCount    = $helper->getHandler('Links')->getCount($linksCriteria);
        $mymenusTpl->assign('count', $linksCount);

        $linksCriteria->setSort('weight');
        $linksCriteria->setOrder('ASC');
        //
        //        $menusArray = [];
        if (($linksCount > 0) && ($linksCount >= (int)$start)) {
            $linksCriteria->setStart((int)$start);
            $linksArrays = $helper->getHandler('Links')->getObjects($linksCriteria, false, false); // as array
            //
            $menuBuilder = new Builder($linksArrays);
            $menusArray  = $menuBuilder->render();
            $mymenusTpl->assign('menus', $menusArray); // not 'menus', 'links' shoult be better
        }

        $mymenusTpl->assign('addform', self::editLink(null, null, $mid));

        return $mymenusTpl->fetch($GLOBALS['xoops']->path("modules/{$helper->getDirname()}/templates/static/mymenus_admin_links.tpl"));
    }

    /**
     * @param int $mid
     */
    public static function addLink($mid): void
    {
        $helper = Helper::getInstance();

        if (!$GLOBALS['xoopsSecurity']->check()) {
            \redirect_header($GLOBALS['mymenusAdminPage'], 3, \implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (!$mid) {
            \redirect_header($GLOBALS['mymenusAdminPage'] . '?op=list', 2, \_AM_MYMENUS_MSG_MENU_INVALID_ERROR);
        }

        $linksCiteria = new \CriteriaCompo(new \Criteria('mid', $mid));
        $linksCiteria->setSort('weight');
        $linksCiteria->setOrder('DESC');
        $linksCiteria->setLimit(1);
        $linksObjs = $helper->getHandler('Links')->getObjects($linksCiteria);
        $weight    = 1;
        if (isset($linksObjs[0]) && ($linksObjs[0] instanceof \XoopsModules\Mymenus\Links)) {
            $weight = $linksObjs[0]->getVar('weight') + 1;
        }

        $newLinksObj = $helper->getHandler('Links')->create();
        //    if (!isset($_POST['hooks'])) {
        //        $_POST['hooks'] = [];
        //    }
        if (!Request::getArray('hooks', null, 'POST')) {
            $_POST['hooks'] = [];
        }
        // clean incoming POST vars
        $newLinksObj->setVar('id', Request::getInt('id', 0, 'POST'));
        $newLinksObj->setVar('pid', Request::getInt('pid', 0, 'POST'));
        $newLinksObj->setVar('mid', Request::getInt('mid', 0, 'POST'));
        $newLinksObj->setVar('title', Request::getString('title', '', 'POST'));
        $newLinksObj->setVar('alt_title', Request::getString('alt_title', '', 'POST'));
        $newLinksObj->setVar('visible', Request::getInt('visible', 0, 'POST'));
        $newLinksObj->setVar('link', Request::getString('link', '', 'POST'));
        $newLinksObj->setVar('weight', Request::getInt('weight', 0, 'POST'));
        $newLinksObj->setVar('target', Request::getString('target', '', 'POST'));
        $newLinksObj->setVar('groups', Request::getArray('groups', [], 'POST'));
        $newLinksObj->setVar('hooks', Request::getArray('hooks', [], 'POST'));
        $newLinksObj->setVar('image', Request::getString('image', '', 'POST'));
        $newLinksObj->setVar('css', Request::getString('css', '', 'POST'));

        $newLinksObj->setVar('weight', $weight);
        $linksHandler = $helper->getHandler('Links');
        if (!$linksHandler->insert($newLinksObj)) {
            $msg = \_AM_MYMENUS_MSG_ERROR;
        } else {
            $linksHandler->updateWeights($newLinksObj);
            $msg = \_AM_MYMENUS_MSG_SUCCESS;
        }

        \redirect_header($GLOBALS['mymenusAdminPage'] . '?op=list&amp;mid=' . $newLinksObj->getVar('mid'), 2, $msg);
    }

    /**
     * @param int $id
     * @param int $mid
     */
    public static function saveLink($id, $mid): void
    {
        $helper       = Helper::getInstance();
        $linksHandler = $helper->getHandler('Links');

        if (!$GLOBALS['xoopsSecurity']->check()) {
            \redirect_header($GLOBALS['mymenusAdminPage'], 3, \implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (!$mid) {
            \redirect_header($GLOBALS['mymenusAdminPage'] . '?op=list', 2, \_AM_MYMENUS_MSG_MENU_INVALID_ERROR);
        }

        $mid      = (int)$mid;
        $linksObj = $linksHandler->get((int)$id);

        //if this was moved then parent could be in different menu, if so then set parent to top level
        if (Request::getInt('pid', '', 'POST')) {
            $parentLinksObj = $linksHandler->get($linksObj->getVar('pid'));  //get the parent object
            if (($parentLinksObj instanceof \XoopsModules\Mymenus\Links)
                && ($linksObj->getVar('mid') != $parentLinksObj->getVar('mid'))) {
                $linksObj->setVar('pid', 0);
            }
        }
        // Disable xoops debugger in dialog window
        \xoops_load('xoopslogger');
        $xoopsLogger            = \XoopsLogger::getInstance();
        $xoopsLogger->activated = false;
        \error_reporting(0);

        // @TODO: clean incoming POST vars
        $linksObj->setVars($_POST);

        if (!$linksHandler->insert($linksObj)) {
            $msg = \_AM_MYMENUS_MSG_ERROR;
        } else {
            $msg = \_AM_MYMENUS_MSG_SUCCESS;
        }

        \redirect_header($GLOBALS['mymenusAdminPage'] . "?op=list&mid={$mid}", 2, $msg);
    }

    /**
     * @param null|int $id
     * @param null|int $pid
     *
     * @param null|int $mid
     * @return string
     */
    public static function editLink($id = null, $pid = null, $mid = null)
    {
        $helper = Helper::getInstance();
        //
        // Disable xoops debugger in dialog window
        \xoops_load('xoopslogger');
        $xoopsLogger            = \XoopsLogger::getInstance();
        $xoopsLogger->activated = false;
        \error_reporting(0);

        $pathIcon16 = Admin::iconUrl('', '16');

        //        $registry = MymenusRegistry::getInstance();
        //        $plugin   = MymenusPlugin::getInstance();

        $linksObj = $helper->getHandler('Links')->get((int)$id);

        if ($linksObj->isNew()) {
            $formTitle = _ADD;
            if (null !== $pid) {
                $linksObj->setVar('pid', (int)$pid);
            }
            if (null !== $mid) {
                $linksObj->setVar('mid', (int)$mid);
            }
        } else {
            $formTitle = _EDIT;
        }
        $form = new \XoopsThemeForm($formTitle, 'admin_form', $GLOBALS['mymenusAdminPage'], 'post', true);
        // links: title
        $formtitle = new \XoopsFormText(\_AM_MYMENUS_MENU_TITLE, 'title', 50, 255, $linksObj->getVar('title'));
        $form->addElement($formtitle, true);
        // links: alt_title
        $formalttitle = new \XoopsFormText(\_AM_MYMENUS_MENU_ALTTITLE, 'alt_title', 50, 255, $linksObj->getVar('alt_title'));
        $form->addElement($formalttitle);
        // links: mid
        $menusCriteria = new \CriteriaCompo();
        $menusCriteria->setSort('title');
        $menusCriteria->setOrder('ASC');
        $menusList = $helper->getHandler('Menus')->getList($menusCriteria);
        if (\count($menusList) > 1) {
            // display menu options (if more than 1 menu available
            if (!$linksObj->getVar('mid')) { // initial menu value not set
                //                $menuValues = array_flip($menusList);
                $formmid = new \XoopsFormSelect(\_AM_MYMENUS_MENU_MENU, 'mid', $mid); //array_shift($menuValues));
            } else {
                $formmid = new \XoopsFormSelect(\_AM_MYMENUS_MENU_MENU, 'mid', $linksObj->getVar('mid'));
            }
            $formmid->addOptionArray($menusList);
        } else {
            $menuKeys  = \array_keys($menusList);
            $menuTitle = \array_shift($menusList);
            $formmid   = new \XoopsFormElementTray('Menu');
            $formmid->addElement(new \XoopsFormHidden('mid', $menuKeys[0]));
            $formmid->addElement(new \XoopsFormLabel('', $menuTitle, 'menuTitle'));
        }
        $form->addElement($formmid);
        // links: link
        $formlink = new \XoopsFormText(\_AM_MYMENUS_MENU_LINK, 'link', 50, 255, $linksObj->getVar('link'));
        $form->addElement($formlink);
        // links: image
        $formimage = new \XoopsFormText(\_AM_MYMENUS_MENU_IMAGE, 'image', 50, 255, $linksObj->getVar('image'));
        $form->addElement($formimage);
        //
        //$form->addElement($formparent);
        // links: visible
        $statontxt  = "&nbsp;<img src='{$pathIcon16}1.png' alt='" . _YES . "'>&nbsp;" . _YES . '&nbsp;&nbsp;&nbsp;';
        $statofftxt = "&nbsp;<img src='{$pathIcon16}0.png' alt='" . _NO . "'>&nbsp;" . _NO . '&nbsp;';
        $formvis    = new \XoopsFormRadioYN(\_AM_MYMENUS_MENU_VISIBLE, 'visible', $linksObj->getVar('visible'), $statontxt, $statofftxt);
        $form->addElement($formvis);
        // links: target
        $formtarget = new \XoopsFormSelect(\_AM_MYMENUS_MENU_TARGET, 'target', $linksObj->getVar('target'));
        $formtarget->addOption('_self', \_AM_MYMENUS_MENU_TARG_SELF);
        $formtarget->addOption('_blank', \_AM_MYMENUS_MENU_TARG_BLANK);
        $formtarget->addOption('_parent', \_AM_MYMENUS_MENU_TARG_PARENT);
        $formtarget->addOption('_top', \_AM_MYMENUS_MENU_TARG_TOP);
        $form->addElement($formtarget);
        // links: groups
        $formgroups = new \XoopsFormSelectGroup(\_AM_MYMENUS_MENU_GROUPS, 'groups', true, $linksObj->getVar('groups'), 5, true);
        $formgroups->setDescription(\_AM_MYMENUS_MENU_GROUPS_HELP);
        $form->addElement($formgroups);
        // @TODO: reintroduce hooks
        /*
            //links: hooks
            $formhooks = new \XoopsFormSelect(_AM_MYMENUS_MENU_ACCESS_FILTER, "hooks", $linksObj->getVar('hooks'), 5, true);
            $plugin->triggerEvent('AccessFilter');
            $results = $registry->getEntry('accessFilter');
            if ($results) {
                foreach ($results as $result) {
                    $formhooks->addOption($result['method'], $result['name']);
                }
            }
            $form->addElement($formhooks);
        */
        // links: css
        $formcss = new \XoopsFormText(\_AM_MYMENUS_MENU_CSS, 'css', 50, 255, $linksObj->getVar('css'));
        $form->addElement($formcss);

        $buttonTray = new \XoopsFormElementTray('', '');
        $buttonTray->addElement(new \XoopsFormButton('', 'submit_button', _SUBMIT, 'submit'));
        $button = new \XoopsFormButton('', 'reset', _CANCEL, 'button');
        if (null !== $id) {
            $button->setExtra("onclick=\"document.location.href='" . $GLOBALS['mymenusAdminPage'] . "?op=list&amp;mid={$mid}'\"");
        } else {
            $button->setExtra("onclick=\"document.getElementById('addform').style.display = 'none'; return false;\"");
        }
        $buttonTray->addElement($button);
        $form->addElement($buttonTray);

        if (null !== $id) {
            $form->addElement(new \XoopsFormHidden('op', 'save'));
            $form->addElement(new \XoopsFormHidden('id', $id));
        } else {
            $form->addElement(new \XoopsFormHidden('op', 'add'));
        }

        return $form->render();
    }

    /**
     * Update the {@see MymenusLinks} weight (order)
     *
     * @param int $id of links object
     * @param int $weight
     */
    public static function moveLink($id, $weight): void
    {
        $helper       = Helper::getInstance();
        $linksHandler = $helper->getHandler('Links');

        $linksObj = $linksHandler->get((int)$id);
        $linksObj->setVar('weight', (int)$weight);
        $linksHandler->insert($linksObj);
        $linksHandler->updateWeights($linksObj);
    }

    /**
     * @param int $id
     */
    public static function toggleLinkVisibility($id): void
    {
        $helper       = Helper::getInstance();
        $linksHandler = $helper->getHandler('Links');
        // Disable xoops debugger in dialog window
        \xoops_load('xoopslogger');
        $xoopsLogger            = \XoopsLogger::getInstance();
        $xoopsLogger->activated = false;
        \error_reporting(0);

        $linksObj = $linksHandler->get((int)$id);
        $visible  = (1 === $linksObj->getVar('visible')) ? 0 : 1;
        $linksObj->setVar('visible', $visible);
        $linksHandler->insert($linksObj);
        echo $linksObj->getVar('visible');
    }

    public static function cloneLink($id): void
    {
        $helper       = Helper::getInstance();
        $linksHandler = $helper->getHandler('Links');

        $new_id = false;
        $table  = $GLOBALS['xoopsDB']->prefix('mymenus_links');
        // copy content of the record you wish to clone
        $tempTable = $GLOBALS['xoopsDB']->fetchArray($GLOBALS['xoopsDB']->query("SELECT * FROM $table WHERE id='$id' "), \MYSQLI_ASSOC) or exit('Could not select record');
        // set the auto-incremented id's value to blank.
        unset($tempTable['id']);
        // insert cloned copy of the original  record
        $result = $GLOBALS['xoopsDB']->queryF("INSERT INTO $table (" . \implode(', ', \array_keys($tempTable)) . ") VALUES ('" . \implode("', '", \array_values($tempTable)) . "')") or \trigger_error($GLOBALS['xoopsDB']->error());

        if ($result) {
            // Return the new id
            $new_id = $GLOBALS['xoopsDB']->getInsertId();
            $msg    = \_AM_MYMENUS_MSG_SUCCESS;
        } else {
            $msg = \_AM_MYMENUS_MSG_ERROR;
        }

        \redirect_header($GLOBALS['mymenusAdminPage'] . '?op=list&amp;mid=' . $new_id, 2, $msg);
    }
}
