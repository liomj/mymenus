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
 * @author          trabis <lusopoemas@gmail.com>
 */

//require  \dirname(__DIR__) . '/include/common.php';

/**
 * Class Links
 */
class Links extends \XoopsObject
{
    /**
     * @var Links
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
        $this->initVar('pid', \XOBJ_DTYPE_INT);
        $this->initVar('mid', \XOBJ_DTYPE_INT);
        $this->initVar('title', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('alt_title', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('visible', \XOBJ_DTYPE_INT, true);
        $this->initVar('link', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('weight', \XOBJ_DTYPE_INT, 255);
        $this->initVar('target', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('groups', \XOBJ_DTYPE_ARRAY, \serialize([XOOPS_GROUP_ANONYMOUS, XOOPS_GROUP_USERS]));
        $this->initVar('hooks', \XOBJ_DTYPE_ARRAY, \serialize([]));
        $this->initVar('image', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('css', \XOBJ_DTYPE_TXTBOX);
    }

    /**
     * @return bool
     */
    public function checkAccess()
    {
        $hooks              = $this->getHooks();
        $hooks['mymenus'][] = 'checkAccess';
        foreach ($hooks as $hookName => $hook) {
            if (!mymenusHook($hookName, 'checkAccess', ['links' => $this])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function getHooks(): array
    {
        $ret  = [];
        $data = $this->getVar('hooks', 'n');
    if (empty($data)) {
            return $ret;
        }

        $lines = \explode("\n", $data);
        foreach ($lines as $lineStr) {
            $trimmedLine = \trim($lineStr);
        if ('' === $trimmedLine) {
            continue;
        }

            $parts       = \explode('|', $trimmedLine);
        [$hook, $method] = \array_pad($parts, 2, '');
            $hook   = \trim($hook);
            $method = \trim($method);

        if ('' !== $hook) {
            $ret[$hook][] = $method;
        }
    }

        return $ret;
    }
}
