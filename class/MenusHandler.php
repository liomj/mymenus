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
 * Class MenusHandler
 */
class MenusHandler extends \XoopsPersistableObjectHandler
{
    /**
     * @var Mymenus\Helper
     */
    private $helper;

    public function __construct(?\XoopsDatabase $db = null)
    {
        parent::__construct($db, 'mymenus_menus', Menus::class, 'id', 'title', 'css');
        /** @var \XoopsModules\Mymenus\Helper $this ->helper */
        $this->helper = Helper::getInstance();
    }
}
