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
 * Mymenus module
 *
 * @copyright       XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @since           1.5
 * @author          Xoops Development Team
 */

use XoopsModules\Mymenus\{
    Common\Configurator,
    Helper,
    Updater,
    Utility
};

/** @var Helper $helper */
/** @var Utility $utility */
/** @var Configurator $configurator */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

//$moduleDirname = \basename(\dirname(__DIR__));
//require XOOPS_ROOT_PATH . "/modules/$moduleDirname/include/common.php";
require __DIR__ . '/common.php';
$helper = Helper::getInstance($debug);

xoops_loadLanguage('admin', $helper->getDirname());

/**
 * @param object|\XoopsObject $xoopsModule
 * @param int                 $previousVersion
 * @return bool               FALSE if failed
 */
function xoops_module_update_mymenus(\XoopsObject $xoopsModule, $previousVersion)
{
    if ($previousVersion < 151) {
        //if (!checkInfoTemplates($xoopsModule)) return false;
        if (!Updater::checkInfoTable($xoopsModule)) {
            return false;
        }
        //update_tables_to_150($xoopsModule);
    }

    $moduleDirName = \basename(\dirname(__DIR__));

    $helper       = Helper::getInstance();
    $utility      = new Utility();
    $configurator = new Configurator();

    $helper->loadLanguage('common');

    if ($previousVersion < 155) {
        //delete old HTML templates
        if (count($configurator->templateFolders) > 0) {
            foreach ($configurator->templateFolders as $folder) {
                $templateFolder = $GLOBALS['xoops']->path('modules/' . $moduleDirName . $folder);
                if (is_dir($templateFolder)) {
                    $templateList = array_diff(scandir($templateFolder, SCANDIR_SORT_NONE), ['..', '.']);
                    foreach ($templateList as $k => $v) {
                        $fileInfo = new SplFileInfo($templateFolder . $v);
                        if ('html' === $fileInfo->getExtension() && 'index.html' !== $fileInfo->getFilename()) {
                            if (file_exists($templateFolder . $v)) {
                                unlink($templateFolder . $v);
                            }
                        }
                    }
                }
            }
        }

        //  ---  DELETE OLD FILES ---------------
        if (count($configurator->oldFiles) > 0) {
            //    foreach (array_keys($GLOBALS['uploadFolders']) as $i) {
            foreach (array_keys($configurator->oldFiles) as $i) {
                $tempFile = $GLOBALS['xoops']->path('modules/' . $moduleDirName . $configurator->oldFiles[$i]);
                if (is_file($tempFile)) {
                    unlink($tempFile);
                }
            }
        }

        //  ---  DELETE OLD FOLDERS ---------------
        xoops_load('XoopsFile');
        if (count($configurator->oldFolders) > 0) {
            //    foreach (array_keys($GLOBALS['uploadFolders']) as $i) {
            foreach (array_keys($configurator->oldFolders) as $i) {
                $tempFolder = $GLOBALS['xoops']->path('modules/' . $moduleDirName . $configurator->oldFolders[$i]);
                /** @var XoopsObjectHandler $folderHandler */
                $folderHandler = \XoopsFile::getHandler('folder', $tempFolder);
                $folderHandler->delete($tempFolder);
            }
        }

        //  ---  CREATE UPLOAD FOLDERS ---------------
        if (count($configurator->uploadFolders) > 0) {
            //    foreach (array_keys($GLOBALS['uploadFolders']) as $i) {
            foreach (array_keys($configurator->uploadFolders) as $i) {
                $utility::createFolder($configurator->uploadFolders[$i]);
            }
        }

        //  ---  COPY blank.png FILES ---------------
        if (count($configurator->copyBlankFiles) > 0) {
            $file = dirname(__DIR__) . '/assets/images/blank.png';
            foreach (array_keys($configurator->copyBlankFiles) as $i) {
                $dest = $configurator->copyBlankFiles[$i] . '/blank.png';
                $utility::copyFile($file, $dest);
            }
        }

        //delete .html entries from the tpl table
        $sql = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('tplfile') . " WHERE `tpl_module` = '" . $xoopsModule->getVar('dirname', 'n') . "' AND `tpl_file` LIKE '%.html%'";
        $GLOBALS['xoopsDB']->queryF($sql);

        //delete .tpl entries from the tpl table
        $sql = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('tplfile') . " WHERE `tpl_module` = '" . $xoopsModule->getVar('dirname', 'n') . "' AND `tpl_file` LIKE '%.tpl%'";
        $GLOBALS['xoopsDB']->queryF($sql);

        //delete .tpl entries from the tpl_source table
        //        $sql = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('tplsource') . " WHERE `tpl_source` LIKE '%'" . $xoopsModule->getVar('dirname', 'n') . "'%'";
        //        $GLOBALS['xoopsDB']->queryF($sql);

        //        return $gpermHandler->deleteByModule($xoopsModule->getVar('mid'), 'item_read');

        //TODO replace mymenus_block.html in newblocks table with mymenus_block.tpl
    }

    return true;
}

if (!function_exists('InfoColumnExists')) {
    /**
     * @param $tablename
     * @param $spalte
     *
     * @return bool
     */
    function InfoColumnExists($tablename, $spalte)
    {
        if ('' === $tablename || '' === $spalte) {
            return true;
        } // Fehler!!
        $result = $GLOBALS['xoopsDB']->queryF('SHOW COLUMNS FROM ' . $tablename . " LIKE '" . $spalte . "'");
        $ret    = $GLOBALS['xoopsDB']->getRowsNum($result) > 0;

        return $ret;
    }
}

if (!function_exists('InfoTableExists')) {
    /**
     * @param $tablename
     *
     * @return bool
     */
    function InfoTableExists($tablename)
    {
        $result = $GLOBALS['xoopsDB']->queryF("SHOW TABLES LIKE '$tablename'");
        $ret    = $GLOBALS['xoopsDB']->getRowsNum($result) > 0;

        return $ret;
    }
}
