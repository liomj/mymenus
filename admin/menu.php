<?php declare(strict_types=1);
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
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

use Xmf\Module\Admin;
use XoopsModules\Mymenus;
use XoopsModules\Mymenus\Helper;

/** @var \XoopsModules\Mymenus\Helper $helper */
$moduleDirName      = \basename(\dirname(__DIR__));
$moduleDirNameUpper = \mb_strtoupper($moduleDirName);

$helper = Helper::getInstance();
$helper->loadLanguage('common');
$helper->loadLanguage('feedback');

$pathIcon32    = Admin::menuIconPath('');
$pathModIcon32 = XOOPS_URL . '/modules/' . $moduleDirName . '/assets/images/icons/32/';
if (is_object($helper->getModule()) && false !== $helper->getModule()->getInfo('modicons32')) {
    $pathModIcon32 = $helper->url($helper->getModule()->getInfo('modicons32'));
}

$adminmenu[] = [
    'title' => _MI_MYMENUS_ADMMENU0,
    'link'  => 'admin/index.php',
    'icon'  => "{$pathIcon32}/home.png",
];

$adminmenu[] = [
    'title' => _MI_MYMENUS_MENUSMANAGER,
    'link'  => 'admin/menus.php',
    'icon'  => "{$pathIcon32}/manage.png",
];

$adminmenu[] = [
    'title' => _MI_MYMENUS_MENUMANAGER,
    'link'  => 'admin/links.php',
    'icon'  => "{$pathIcon32}/insert_table_row.png",
];

$adminmenu[] = [
    //        'title' => _MI_MYMENUS_BLOCKS, //'Block/Group Admin'
    'title' => constant('CO_' . $moduleDirNameUpper . '_' . 'BLOCKS'),
    'link'  => 'admin/blocksadmin.php',
    'icon'  => $pathIcon32 . '/block.png',
];

$adminmenu[] = [
    'title' => _MI_MYMENUS_ADMENU6,  //Permissions,
    'link'  => 'admin/myblocksadmin.php',
    'desc'  => constant('CO_' . $moduleDirNameUpper . '_' . 'BLOCKS_DESC'),
    'icon'  => $pathIcon32 . '/permissions.png',
];

//Feedback
$adminmenu[] = [
    'title' => constant('CO_' . $moduleDirNameUpper . '_' . 'ADMENU_FEEDBACK'),
    'link'  => 'admin/feedback.php',
    'icon'  => $pathIcon32 . '/mail_foward.png',
];

if (is_object($helper->getModule()) && $helper->getConfig('displayDeveloperTools')) {
    $adminmenu[] = [
        'title' => constant('CO_' . $moduleDirNameUpper . '_' . 'ADMENU_MIGRATE'),
        'link'  => 'admin/migrate.php',
        'icon'  => $pathIcon32 . '/database_go.png',
    ];
}

$adminmenu[] = [
    'title' => _MI_MYMENUS_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => "{$pathIcon32}/about.png",
];

//constant('CO_' . $moduleDirNameUpper . '_' . 'PERMISSIONS')
//constant('CO_' . $moduleDirNameUpper . '_' . 'BLOCKS')

//$mymenus_adminmenu = $adminmenu;
