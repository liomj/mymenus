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

use Xmf\Module\Admin;
use XoopsModules\Mymenus\Helper;

require \dirname(__DIR__) . '/preloads/autoloader.php';

require \dirname(__DIR__, 3) . '/include/cp_header.php';
xoops_load('XoopsFormLoader');

require \dirname(__DIR__) . '/include/common.php';

$moduleDirName = \basename(\dirname(__DIR__));

/** @var \XoopsModules\Mymenus\Helper $helper */
$helper = Helper::getInstance();
/** @var Xmf\Module\Admin $adminObject */
$adminObject = Admin::getInstance();

$myts = \MyTextSanitizer::getInstance();

if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof \XoopsTpl)) {
    require $GLOBALS['xoops']->path('class/template.php');
    $xoopsTpl = new \XoopsTpl();
}

// Load language files
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('main');
$helper->loadLanguage('common');

//Module specific elements

//Handlers
//$XXXHandler = xoops_getModuleHandler('XXX', $mymenus->dirname);

$mymenusTpl = new \XoopsTpl();
