<?php declare(strict_types=1);

namespace XoopsModules\Mymenus\Plugins\Smarty;

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

use XoopsModules\Mymenus\{
    Registry
};


/**
 * Class PluginItem
 */
class PluginItem extends \XoopsModules\Mymenus\PluginItem
{
    public static function eventLinkDecoration(): void
    {
        $linkArray = self::getLinkArray();
        $linkArray['link'] = self::doDecoration($linkArray['link']);
        self::setLinkArray($linkArray);
    }

    public static function eventImageDecoration(): void
    {
        $linkArray = self::getLinkArray();
        $linkArray['image'] = self::doDecoration($linkArray['image']);
        self::setLinkArray($linkArray);
    }

    public static function eventTitleDecoration(): void
    {
        $linkArray = self::getLinkArray();
        $linkArray['title'] = self::doDecoration($linkArray['title']);
        self::setLinkArray($linkArray);
    }

    public static function eventAltTitleDecoration(): void
    {
        $linkArray = self::getLinkArray();
        $linkArray['alt_title'] = self::doDecoration($linkArray['alt_title']);
        self::setLinkArray($linkArray);
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    protected static function doDecoration($string)
    {
        if (!\preg_match('/{(.*\|.*)}/i', $string, $reg)) {
            return $string;
        }

        $expression = $reg[0];
        [$validator, $value] = \array_map('\mb_strtolower', \explode('|', $reg[1]));

        if ('smarty' === $validator) {
            if (isset($GLOBALS['xoopsTpl']->_tpl_vars[$value])) {
                $string = \str_replace($expression, $GLOBALS['xoopsTpl']->_tpl_vars[$value], $string);
            }
        }

        return $string;
    }

    /**
     * Get the link array from the registry.
     *
     * @return array
     */
    private static function getLinkArray(): array
    {
        $registry = Registry::getInstance();
        return $registry->getEntry('link_array');
    }

    /**
     * Set the link array in the registry.
     *
     * @param array $linkArray
     */
    private static function setLinkArray(array $linkArray): void
    {
        $registry = Registry::getInstance();
        $registry->setEntry('link_array', $linkArray);
    }
}
