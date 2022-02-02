<?php declare(strict_types=1);
// ------------------------------------------------------------------------- //
//                            myblocksadmin.php                              //
//                - XOOPS block admin for each modules -                     //
//                          GIJOE <https://www.peak.ne.jp>                   //
// ------------------------------------------------------------------------- //

use Xmf\Module\Admin;
use Xmf\Request;

require_once __DIR__ . '/admin_header.php';
//require_once XOOPS_ROOT_PATH."/modules/" . $xoopsModule->getVar("dirname") . "/class/admin.php";

require_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
//require_once  \dirname(__DIR__) . '/include/gtickets.php';// GIJ

$xoops_system_path = XOOPS_ROOT_PATH . '/modules/system';

// language files
$language = $xoopsConfig['language'];
if (!file_exists("{$xoops_system_path}/language/{$language}/admin/blocksadmin.php")) {
    $language = 'english';
}

// to prevent from notice that constants already defined
$error_reporting_level = error_reporting(0);
require_once "{$xoops_system_path}/constants.php";
require_once "{$xoops_system_path}/language/{$language}/admin.php";
require_once "{$xoops_system_path}/language/{$language}/admin/blocksadmin.php";
error_reporting($error_reporting_level);

$group_defs = file("{$xoops_system_path}/language/{$language}/admin/groups.php");
foreach ($group_defs as $def) {
    if (false !== mb_strpos($def, '_AM_MYLINKS_ACCESSRIGHTS') || false !== mb_strpos($def, '_AM_MYLINKS_ACTIVERIGHTS')) {
        eval($def);
    }
}

// check $xoopsModule
if (!is_object($xoopsModule)) {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}

// set target_module if specified by $_GET['dirname']
/** @var \XoopsModuleHandler $moduleHandler */
$moduleHandler = xoops_getHandler('module');

if (!empty($_GET['dirname'])) {
    $target_module = $moduleHandler->getByDirname($_GET['dirname']);
}/* else if ( ! empty( $_GET['mid'] ) ) {
  $target_module =& $moduleHandler->get( (int)( $_GET['mid'] ) );
}*/

if (!empty($target_module) && is_object($target_module)) {
    // specified by dirname
    $target_mid     = $target_module->getVar('mid');
    $target_mname   = $target_module->getVar('name') . '&nbsp;' . sprintf('(%2.2f)', $target_module->getVar('version') / 100.0);
    $query4redirect = '?dirname=' . urlencode(strip_tags($_GET['dirname']));
} elseif ((Request::hasVar('mid', 'GET') && 0 == $_GET['mid']) || 'blocksadmin' === $xoopsModule->getVar('dirname')) {
    $target_mid     = 0;
    $target_mname   = '';
    $query4redirect = '?mid=0';
} else {
    $target_mid     = $xoopsModule->getVar('mid');
    $target_mname   = $xoopsModule->getVar('name');
    $query4redirect = '';
}

// check access right (needs system_admin of BLOCK)
/** @var \XoopsGroupPermHandler $grouppermHandler */
$grouppermHandler = xoops_getHandler('groupperm');
if (!$grouppermHandler->checkRight('system_admin', XOOPS_SYSTEM_BLOCK, $xoopsUser->getGroups())) {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}

// get blocks owned by the module (Imported from xoopsblock.php then modified)
//$block_arr =& XoopsBlock::getByModule( $target_mid );
$db        = \XoopsDatabaseFactory::getDatabaseConnection();
$sql       = 'SELECT * FROM ' . $db->prefix('newblocks') . " WHERE mid='{$target_mid}' ORDER BY visible DESC,side,weight";
$result    = $db->query($sql);
$block_arr = [];
while (false !== ($myrow = $db->fetchArray($result))) {
    $block_arr[] = new \XoopsBlock($myrow);
}

function list_groups(): void
{
    global $target_mid, $target_mname, $block_arr;

    $moduleDirName      = \basename(\dirname(__DIR__));
    $moduleDirNameUpper = \mb_strtoupper($moduleDirName);

    $item_list = [];
    foreach (array_keys($block_arr) as $i) {
        $item_list[$block_arr[$i]->getVar('bid')] = $block_arr[$i]->getVar('title');
    }

    $form = new \XoopsModules\Mymenus\GroupPermForm(constant('CO_' . $moduleDirNameUpper . '_' . 'AGDS'), 1, 'block_read', '');
    if ($target_mid > 1) {
        $form->addAppendix('module_admin', $target_mid, $target_mname . ' ' . constant('CO_' . $moduleDirNameUpper . '_' . 'ACTIVERIGHTS'));
        $form->addAppendix('module_read', $target_mid, $target_mname . ' ' . constant('CO_' . $moduleDirNameUpper . '_' . 'ACCESSRIGHTS'));
    }
    foreach ($item_list as $item_id => $item_name) {
        $form->addItem($item_id, $item_name);
    }
    echo $form->render();
}

if (!empty($_POST['submit'])) {
    if (!$GLOBALS['xoopsSecurity']->check(true, $_REQUEST['myblocksadmin'])) {
        redirect_header(XOOPS_URL . '/', 3, $GLOBALS['xoopsSecurity']->getErrors());
    }

    require_once __DIR__ . '/mygroupperm.php';
    redirect_header(XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/admin/myblocksadmin.php{$query4redirect}", 1, _MD_MYLINKS_DBUPDATED);
}

xoops_cp_header();
$adminObject = Admin::getInstance();
$adminObject->displayNavigation(basename(__FILE__));

if (file_exists('./mymenu.php')) {
    require_once __DIR__ . '/mymenu.php';
}

list_groups();
require_once __DIR__ . '/admin_footer.php';
