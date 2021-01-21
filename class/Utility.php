<?php

namespace XoopsModules\Mymenus;

use XoopsModules\Mymenus;
use XoopsModules\Mymenus\Common;
use XoopsModules\Mymenus\Constants;

/**
 * Class Utility
 */
class Utility extends Common\SysUtility
{
    //--------------- Custom module methods -----------------------------
    /**
     * @param string $moduleSkin
     * @param bool   $useThemeSkin
     * @param string $themeSkin
     * @return array
     */
    public static function getSkinInfo($moduleSkin = 'default', $useThemeSkin = false, $themeSkin = '')
    {
        //    require __DIR__ . '/common.php';
        /** @var \XoopsModules\Mymenus\Helper $helper */
        $helper = \XoopsModules\Mymenus\Helper::getInstance();
        $error  = false;
        if ($useThemeSkin) {
            $path = 'themes/' . $GLOBALS['xoopsConfig']['theme_set'] . '/menu';
            if (!file_exists($GLOBALS['xoops']->path("{$path}/skin_version.php"))) {
                $path = 'themes/' . $GLOBALS['xoopsConfig']['theme_set'] . "/modules/{$helper->getDirname()}/skins/{$themeSkin}";
                if (!file_exists($GLOBALS['xoops']->path("{$path}/skin_version.php"))) {
                    $error = true;
                }
            }
        }

        if ($error || !$useThemeSkin) {
            $path = "modules/{$helper->getDirname()}/skins/{$moduleSkin}";
        }

        $file = $GLOBALS['xoops']->path("{$path}/skin_version.php");
        $info = [];

        if (is_file($file)) {
            require $file;
            $info = $skinVersion;
        }

        $info['path'] = $GLOBALS['xoops']->path($path);
        $info['url']  = $GLOBALS['xoops']->url($path);

        if (!isset($info['template'])) {
            $info['template'] = $GLOBALS['xoops']->path("modules/{$helper->getDirname()}/templates/static/blocks/mymenus_block.tpl");
        } else {
            $info['template'] = $GLOBALS['xoops']->path("{$path}/" . $info['template']);
        }

        if (!isset($info['prefix'])) {
            $info['prefix'] = $moduleSkin;
        }

        if (isset($info['css'])) {
            $info['css'] = (array)$info['css'];
            foreach ($info['css'] as $key => $value) {
                $info['css'][$key] = $GLOBALS['xoops']->url("{$path}/{$value}");
            }
        }

        if (isset($info['js'])) {
            $info['js'] = (array)$info['js'];
            foreach ($info['js'] as $key => $value) {
                $info['js'][$key] = $GLOBALS['xoops']->url("{$path}/{$value}");
            }
        }

        if (!isset($info['config'])) {
            $info['config'] = [];
        }

        return $info;
    }
}
