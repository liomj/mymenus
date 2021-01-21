<?php

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
 * @package         Mymenus
 * @since           1.0
 * @author          trabis <lusopoemas@gmail.com>
 */

/**
 * Class Registry
 */
class Registry
{
    protected $entries;
    protected $locks;

    protected function __construct()
    {
        $this->entries = [];
        $this->locks   = [];
    }

    /**
     * @return Registry
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * @param $key
     * @param $item
     *
     * @return bool
     */
    public function setEntry($key, $item)
    {
        $ret = true;
        if (true === $this->isLocked($key)) {
            \trigger_error(\_AM_MYMENUS_ENTRY_UNABLE . " `{$key}`." . \_AM_MYMENUS_ENTRY_LOCKED, \E_USER_WARNING);

            $ret = false;
        }

        $this->entries[$key] = $item;

        return $ret;
    }

    /**
     * @param $key
     */
    public function unsetEntry($key)
    {
        unset($this->entries[$key]);
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getEntry($key)
    {
        if (false === isset($this->entries[$key])) {
            return null;
        }

        return $this->entries[$key];
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function isEntry($key)
    {
        return (null !== $this->getEntry($key));
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function lockEntry($key)
    {
        $this->locks[$key] = true;

        return true;
    }

    /**
     * @param $key
     */
    public function unlockEntry($key)
    {
        unset($this->locks[$key]);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function isLocked($key)
    {
        return (true === isset($this->locks[$key]));
    }

    public function unsetAll()
    {
        $this->entries = [];
        $this->locks   = [];
    }
}
