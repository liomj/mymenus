<?php declare(strict_types=1);

namespace XoopsModules\Mymenus\Plugins\Dynamic;

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
 * @author          trabis <lusopoemas@gmail.com>
 */

use XoopsModules\Mymenus;
use XoopsModules\Mymenus\Helper;

/**
 * Class PluginItem
 */
class PluginItem extends Mymenus\PluginItem
{
    public static function eventEnd(): void
    {
        $newmenus = [];
        $registry = Mymenus\Registry::getInstance();
        $menus    = $registry->getEntry('menus');
        foreach ($menus as $menu) {
            if (!\preg_match('/{(MODULE\|.*)}/i', $menu['title'], $reg)) {
                $newmenus[] = $menu;
                continue;
            }
            $result      = \array_map('\mb_strtolower', \explode('|', $reg[1]));
            $moduleMenus = self::getModuleMenus($result[1], $menu['pid']);
            foreach ($moduleMenus as $mMenu) {
                $newmenus[] = $mMenu;
            }
        }
        $registry->setEntry('menus', $newmenus);
    }

    /**
     * @param $module
     * @param $pid
     *
     * @return array
     */
    protected static function getModuleMenus($module, $pid)
    {
        global $xoopsModule;
        static $id = -1;
        /** @var \XoopsModules\Mymenus\Helper $helper */
        $helper = Helper::getInstance();

        $ret = [];
        //Sanitizing $module
        if (\preg_match('/[^a-z0-9\\/\\\\_.:-]/i', $module)) {
            return $ret;
        }

        $path = "modules/{$module}";
        $file = $GLOBALS['xoops']->path("{$path}/xoops_version.php");

        if (!\is_file($file)) {
            return $ret;
        }
        $helper->loadLanguage('modinfo');

        $overwrite = false;
        if (true === $force) {  //can set to false for debug
            if (!($xoopsModule instanceof \XoopsModule) || ($xoopsModule->getVar('dirname') != $module)) {
                // @TODO: check the following 2 statements, they're basically just assigns - is this intended?
                $_xoopsModule       = ($xoopsModule instanceof \XoopsModule) ? $xoopsModule : $xoopsModule;
                $_xoopsModuleConfig = \is_object($xoopsModuleConfig) ? $xoopsModuleConfig : $xoopsModuleConfig;
                /** @var \XoopsModuleHandler $moduleHandler */
                $moduleHandler          = \xoops_getHandler('module');
                $xoopsModule            = $moduleHandler->getByDirname($module);
                $GLOBALS['xoopsModule'] = $xoopsModule;
                if ($xoopsModule instanceof \XoopsModule) {
                    /** @var \XoopsConfigHandler $configHandler */
                    $configHandler                = \xoops_getHandler('config');
                    $xoopsModuleConfig            = $configHandler->getConfigsByCat(0, $xoopsModule->getVar('mid'));
                    $GLOBALS['xoopsModuleConfig'] = $xoopsModuleConfig;
                }
                $overwrite = true;
            }
        }
        $modversion['sub'] = [];
        require $file;

        /** @var \XoopsModules\Mymenus\LinksHandler $linksHandler */
        $linksHandler = $helper->getHandler('Links');
        foreach ($modversion['sub'] as $links) {
            $obj = $linksHandler->create();
            $obj->setVars(
                [
                    'title'     => $links['name'],
                    'alt_title' => $links['name'],
                    'link'      => $GLOBALS['xoops']->url("{$path}/{$links['url']}"),
                    'id'        => $id,
                    'pid'       => (int)$pid,
                ]
            );
            $ret[] = $obj->getValues();
            $id--;
        }

        if ($overwrite) {
            $xoopsModule                  = $_xoopsModule;
            $GLOBALS['xoopsModule']       = $xoopsModule;
            $xoopsModuleConfig            = $_xoopsModuleConfig;
            $GLOBALS['xoopsModuleConfig'] = $xoopsModuleConfig;
        }

        return $ret;
    }
}
