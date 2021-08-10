<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Mymenus module
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package         mymenus
 * @since           1.5
 * @author          Xoops Development Team
 */

use Xmf\Module\Admin;
use XoopsModules\Mymenus;
use XoopsModules\Mymenus\Helper;
use XoopsModules\Mymenus\Utility;

require \dirname(__DIR__) . '/preloads/autoloader.php';

$moduleDirName      = \basename(\dirname(__DIR__));
$moduleDirNameUpper = \mb_strtoupper($moduleDirName);

/** @var \XoopsDatabase $db */
/** @var \XoopsModules\Mymenus\Helper $helper */
/** @var \XoopsModules\Mymenus\Utility $utility */
$db      = \XoopsDatabaseFactory::getDatabaseConnection();
$debug   = false;
$helper  = Helper::getInstance($debug);
$utility = new Utility();

$helper->loadLanguage('common');

$pathIcon16 = Admin::iconUrl('', 16);
$pathIcon32 = Admin::iconUrl('', 32);
if (is_object($helper->getModule())) {
    $pathModIcon16 = $helper->getModule()->getInfo('modicons16');
    $pathModIcon32 = $helper->getModule()->getInfo('modicons32');
}

if (!defined($moduleDirNameUpper . '_CONSTANTS_DEFINED')) {
    define($moduleDirNameUpper . '_DIRNAME', basename(dirname(__DIR__)));
    define($moduleDirNameUpper . '_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/');
    define($moduleDirNameUpper . '_PATH', XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/');
    define($moduleDirNameUpper . '_URL', XOOPS_URL . '/modules/' . $moduleDirName . '/');
    define($moduleDirNameUpper . '_IMAGE_URL', constant($moduleDirNameUpper . '_URL') . '/assets/images/');
    define($moduleDirNameUpper . '_IMAGE_PATH', constant($moduleDirNameUpper . '_ROOT_PATH') . '/assets/images');
    define($moduleDirNameUpper . '_ADMIN_URL', constant($moduleDirNameUpper . '_URL') . '/admin/');
    define($moduleDirNameUpper . '_ADMIN_PATH', constant($moduleDirNameUpper . '_ROOT_PATH') . '/admin/');
    define($moduleDirNameUpper . '_ADMIN', constant($moduleDirNameUpper . '_URL') . '/admin/index.php');
    //    define($moduleDirNameUpper . '_AUTHOR_LOGOIMG', constant($moduleDirNameUpper . '_URL') . '/assets/images/logoModule.png');
    define($moduleDirNameUpper . '_UPLOAD_URL', XOOPS_UPLOAD_URL . '/' . $moduleDirName); // WITHOUT Trailing slash
    define($moduleDirNameUpper . '_UPLOAD_PATH', XOOPS_UPLOAD_PATH . '/' . $moduleDirName); // WITHOUT Trailing slash
    define($moduleDirNameUpper . '_AUTHOR_LOGOIMG', $pathIcon32 . '/xoopsmicrobutton.gif');
    define($moduleDirNameUpper . '_CONSTANTS_DEFINED', 1);
    define($moduleDirNameUpper . '_ICONS_URL', constant($moduleDirNameUpper . '_URL') . '/assets/images/icons/');
}

// This must contain the name of the folder in which reside mymenus
//define('MYMENUS_DIRNAME', basename(dirname(__DIR__)));
//define('MYMENUS_URL', XOOPS_URL . '/modules/' . MYMENUS_DIRNAME);
//define('MYMENUS_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . MYMENUS_DIRNAME);
//define('MYMENUS_IMAGES_URL', MYMENUS_URL . '/assets/images');
//define('MYMENUS_ADMIN_URL', MYMENUS_URL . '/admin');
//define('MYMENUS_ICONS_URL', MYMENUS_URL . '/assets/images/icons');

//require MYMENUS_ROOT_PATH . '/config/config.php'; // IN PROGRESS
//require MYMENUS_ROOT_PATH . '/include/constants.php';

xoops_load('XoopsUserUtility');
xoops_load('XoopsFormLoader');

// module information
$moduleImageUrl      = MYMENUS_URL . '/assets/images/mymenus.png';
$moduleCopyrightHtml = ''; //"<br><br><a href='' title='' target='_blank'><img src='{$moduleImageUrl}' alt=''></a>";

/*
//This is needed or it will not work in blocks.
global $mymenusIsAdmin;

// Load only if module is installed
if (is_object($helper->getModule())) {
    // Find if the user is admin of the module
    $mymenusIsAdmin = Mymenus\Helper::getInstance()->isUserAdmin();
}
*/

$xoopsModule = $helper->getModule();

// Load Xoops handlers
/** @var \XoopsModuleHandler $moduleHandler */
$moduleHandler = xoops_getHandler('module');
/** @var \XoopsMemberHandler $memberHandler */
$memberHandler = xoops_getHandler('member');
/** @var \XoopsNotificationHandler $notificationHandler */
$notificationHandler = xoops_getHandler('notification');
/** @var \XoopsGroupPermHandler $grouppermHandler */
$grouppermHandler = xoops_getHandler('groupperm');
/** @var \XoopsConfigHandler $configHandler */
$configHandler = xoops_getHandler('config');

$icons = [
    'edit'    => "<img src='" . $pathIcon16 . "/edit.png'  alt=" . _EDIT . "' align='middle'>",
    'delete'  => "<img src='" . $pathIcon16 . "/delete.png' alt='" . _DELETE . "' align='middle'>",
    'clone'   => "<img src='" . $pathIcon16 . "/editcopy.png' alt='" . _CLONE . "' align='middle'>",
    'preview' => "<img src='" . $pathIcon16 . "/view.png' alt='" . _PREVIEW . "' align='middle'>",
    'print'   => "<img src='" . $pathIcon16 . "/printer.png' alt='" . _CLONE . "' align='middle'>",
    'pdf'     => "<img src='" . $pathIcon16 . "/pdf.png' alt='" . _CLONE . "' align='middle'>",
    'add'     => "<img src='" . $pathIcon16 . "/add.png' alt='" . _ADD . "' align='middle'>",
    '0'       => "<img src='" . $pathIcon16 . "/0.png' alt='" . 0 . "' align='middle'>",
    '1'       => "<img src='" . $pathIcon16 . "/1.png' alt='" . 1 . "' align='middle'>",
];

$debug = false;

// MyTextSanitizer object
$myts = \MyTextSanitizer::getInstance();

if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof \XoopsTpl)) {
    require_once $GLOBALS['xoops']->path('class/template.php');
    $GLOBALS['xoopsTpl'] = new \XoopsTpl();
}

$GLOBALS['xoopsTpl']->assign('mod_url', XOOPS_URL . '/modules/' . $moduleDirName);
// Local icons path
if (is_object($helper->getModule())) {
    $pathModIcon16 = $helper->getModule()->getInfo('modicons16');
    $pathModIcon32 = $helper->getModule()->getInfo('modicons32');

    $GLOBALS['xoopsTpl']->assign('pathModIcon16', XOOPS_URL . '/modules/' . $moduleDirName . '/' . $pathModIcon16);
    $GLOBALS['xoopsTpl']->assign('pathModIcon32', $pathModIcon32);
}
