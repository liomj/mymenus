<?php

namespace XoopsModules\Mymenus\Common;

/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 *
 * @category        Module
 * @author          XOOPS Development Team
 * @copyright       XOOPS Project
 * @link            https://xoops.org
 * @license         GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */

use Xmf\Request;

//require __DIR__ . '/admin_header.php';

class Blocksadmin
{
    public static function listBlocks()
    {
        global $xoopsModule, $pathIcon16;
        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
        $moduleDirName      = \basename(\dirname(__DIR__, 2));
        $moduleDirNameUpper = \mb_strtoupper($moduleDirName);
        $db                 = \XoopsDatabaseFactory::getDatabaseConnection();
        \xoops_loadLanguage('admin', 'system');
        \xoops_loadLanguage('admin/blocksadmin', 'system');
        \xoops_loadLanguage('admin/groups', 'system');
        \xoops_loadLanguage('common', $moduleDirName);

        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = \xoops_getHandler('module');
        /** @var \XoopsMemberHandler $memberHandler */
        $memberHandler = \xoops_getHandler('member');
        /** @var \XoopsGroupPermHandler $grouppermHandler */
        $grouppermHandler = \xoops_getHandler('groupperm');
        $groups           = $memberHandler->getGroups();
        $criteria         = new \CriteriaCompo(new \Criteria('hasmain', 1));
        $criteria->add(new \Criteria('isactive', 1));
        $module_list     = $moduleHandler->getList($criteria);
        $module_list[-1] = \_AM_SYSTEM_BLOCKS_TOPPAGE;
        $module_list[0]  = \_AM_SYSTEM_BLOCKS_ALLPAGES;
        \ksort($module_list);
        echo "
        <h4 style='text-align:left;'>" . \constant('CO_' . $moduleDirNameUpper . '_' . 'BADMIN') . '</h4>';
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = \xoops_getHandler('module');
        echo "<form action='" . $_SERVER['SCRIPT_NAME'] . "' name='blockadmin' method='post'>";
        echo $GLOBALS['xoopsSecurity']->getTokenHTML();
        echo "<table width='100%' class='outer' cellpadding='4' cellspacing='1'>
        <tr valign='middle'><th align='center'>"
             . \_AM_SYSTEM_BLOCKS_TITLE
             . "</th><th align='center' nowrap='nowrap'>"
             . \constant('CO_' . $moduleDirNameUpper . '_' . 'SIDE')
             . '<br>'
             . _LEFT
             . '-'
             . _CENTER
             . '-'
             . _RIGHT
             . "</th><th align='center'>"
             . \constant(
                 'CO_' . $moduleDirNameUpper . '_' . 'WEIGHT'
             )
             . "</th><th align='center'>"
             . \constant('CO_' . $moduleDirNameUpper . '_' . 'VISIBLE')
             . "</th><th align='center'>"
             . \_AM_SYSTEM_BLOCKS_VISIBLEIN
             . "</th><th align='center'>"
             . \_AM_SYSTEM_ADGS
             . "</th><th align='center'>"
             . \_AM_SYSTEM_BLOCKS_BCACHETIME
             . "</th><th align='center'>"
             . \constant('CO_' . $moduleDirNameUpper . '_' . 'ACTION')
             . '</th></tr>
        ';
        $block_arr   = \XoopsBlock::getByModule($xoopsModule->mid());
        $block_count = \count($block_arr);
        $class       = 'even';
        $cachetimes  = [
            '0'       => _NOCACHE,
            '30'      => \sprintf(_SECONDS, 30),
            '60'      => _MINUTE,
            '300'     => \sprintf(_MINUTES, 5),
            '1800'    => \sprintf(_MINUTES, 30),
            '3600'    => _HOUR,
            '18000'   => \sprintf(_HOURS, 5),
            '86400'   => _DAY,
            '259200'  => \sprintf(_DAYS, 3),
            '604800'  => _WEEK,
            '2592000' => _MONTH,
        ];
        foreach ($block_arr as $i) {
            $groups_perms = $grouppermHandler->getGroupIds('block_read', $i->getVar('bid'));
            $sql          = 'SELECT module_id FROM ' . $db->prefix('block_module_link') . ' WHERE block_id=' . $i->getVar('bid');
            $result       = $db->query($sql);
            $modules      = [];
            while (false !== ($row = $db->fetchArray($result))) {
                $modules[] = (int)$row['module_id'];
            }

            $cachetime_options = '';
            foreach ($cachetimes as $cachetime => $cachetime_name) {
                if ($i->getVar('bcachetime') == $cachetime) {
                    $cachetime_options .= "<option value='$cachetime' selected='selected'>$cachetime_name</option>\n";
                } else {
                    $cachetime_options .= "<option value='$cachetime'>$cachetime_name</option>\n";
                }
            }

            $sel0 = $sel1 = $ssel0 = $ssel1 = $ssel2 = $ssel3 = $ssel4 = $ssel5 = $ssel6 = $ssel7 = '';
            if (1 === $i->getVar('visible')) {
                $sel1 = ' checked';
            } else {
                $sel0 = ' checked';
            }
            if (\XOOPS_SIDEBLOCK_LEFT === $i->getVar('side')) {
                $ssel0 = ' checked';
            } elseif (\XOOPS_SIDEBLOCK_RIGHT === $i->getVar('side')) {
                $ssel1 = ' checked';
            } elseif (\XOOPS_CENTERBLOCK_LEFT === $i->getVar('side')) {
                $ssel2 = ' checked';
            } elseif (\XOOPS_CENTERBLOCK_RIGHT === $i->getVar('side')) {
                $ssel4 = ' checked';
            } elseif (\XOOPS_CENTERBLOCK_CENTER === $i->getVar('side')) {
                $ssel3 = ' checked';
            } elseif (\XOOPS_CENTERBLOCK_BOTTOMLEFT === $i->getVar('side')) {
                $ssel5 = ' checked';
            } elseif (\XOOPS_CENTERBLOCK_BOTTOMRIGHT === $i->getVar('side')) {
                $ssel6 = ' checked';
            } elseif (\XOOPS_CENTERBLOCK_BOTTOM === $i->getVar('side')) {
                $ssel7 = ' checked';
            }
            if ('' === $i->getVar('title')) {
                $title = '&nbsp;';
            } else {
                $title = $i->getVar('title');
            }
            $name = $i->getVar('name');
            echo "<tr valign='top'><td class='$class' align='center'><input type='text' name='title["
                 . $i->getVar('bid')
                 . "]' value='"
                 . $title
                 . "'></td><td class='$class' align='center' nowrap='nowrap'>
                    <div align='center' >
                    <input type='radio' name='side["
                 . $i->getVar('bid')
                 . "]' value='"
                 . \XOOPS_CENTERBLOCK_LEFT
                 . "'$ssel2>
                        <input type='radio' name='side["
                 . $i->getVar('bid')
                 . "]' value='"
                 . \XOOPS_CENTERBLOCK_CENTER
                 . "'$ssel3>
                    <input type='radio' name='side["
                 . $i->getVar('bid')
                 . "]' value='"
                 . \XOOPS_CENTERBLOCK_RIGHT
                 . "'$ssel4>
                    </div>
                    <div>
                        <span style='float:right;'><input type='radio' name='side["
                 . $i->getVar('bid')
                 . "]' value='"
                 . \XOOPS_SIDEBLOCK_RIGHT
                 . "'$ssel1></span>
                    <div align='left'><input type='radio' name='side["
                 . $i->getVar('bid')
                 . "]' value='"
                 . \XOOPS_SIDEBLOCK_LEFT
                 . "'$ssel0></div>
                    </div>
                    <div align='center'>
                    <input type='radio' name='side["
                 . $i->getVar('bid')
                 . "]' value='"
                 . \XOOPS_CENTERBLOCK_BOTTOMLEFT
                 . "'$ssel5>
                        <input type='radio' name='side["
                 . $i->getVar('bid')
                 . "]' value='"
                 . \XOOPS_CENTERBLOCK_BOTTOM
                 . "'$ssel7>
                    <input type='radio' name='side["
                 . $i->getVar('bid')
                 . "]' value='"
                 . \XOOPS_CENTERBLOCK_BOTTOMRIGHT
                 . "'$ssel6>
                    </div>
                </td><td class='$class' align='center'><input type='text' name='weight["
                 . $i->getVar('bid')
                 . "]' value='"
                 . $i->getVar('weight')
                 . "' size='5' maxlength='5'></td><td class='$class' align='center' nowrap><input type='radio' name='visible["
                 . $i->getVar('bid')
                 . "]' value='1'$sel1>"
                 . _YES
                 . "&nbsp;<input type='radio' name='visible["
                 . $i->getVar('bid')
                 . "]' value='0'$sel0>"
                 . _NO
                 . '</td>';

            echo "<td class='$class' align='center'><select size='5' name='bmodule[" . $i->getVar('bid') . "][]' id='bmodule[" . $i->getVar('bid') . "][]' multiple='multiple'>";
            foreach ($module_list as $k => $v) {
                echo "<option value='$k'" . (\in_array($k, $modules) ? " selected='selected'" : '') . ">$v</option>";
            }
            echo '</select></td>';

            echo "<td class='$class' align='center'><select size='5' name='groups[" . $i->getVar('bid') . "][]' id='groups[" . $i->getVar('bid') . "][]' multiple='multiple'>";
            foreach ($groups as $grp) {
                echo "<option value='" . $grp->getVar('groupid') . "' " . (\in_array($grp->getVar('groupid'), $groups_perms) ? " selected='selected'" : '') . '>' . $grp->getVar('name') . '</option>';
            }
            echo '</select></td>';

            // Cache lifetime
            echo '<td class="' . $class . '" align="center"> <select name="bcachetime[' . $i->getVar('bid') . ']" size="1">' . $cachetime_options . '</select>
                                    </td>';

            // Actions

            echo "<td class='$class' align='center'><a href='blocksadmin2.php?op=edit&amp;bid=" . $i->getVar('bid') . "'><img src=" . $pathIcon16 . '/edit.png' . " alt='" . _EDIT . "' title='" . _EDIT . "'>
                 </a> <a href='blocksadmin2.php?op=clone&amp;bid=" . $i->getVar('bid') . "'><img src=" . $pathIcon16 . '/editcopy.png' . " alt='" . _CLONE . "' title='" . _CLONE . "'>
                 </a>";
            if ('S' !== $i->getVar('block_type') && 'M' !== $i->getVar('block_type')) {
                echo "&nbsp;<a href='" . XOOPS_URL . '/modules/system/admin.php?fct=blocksadmin&amp;op=delete&amp;bid=' . $i->getVar('bid') . "'><img src=" . $pathIcon16 . '/delete.png' . " alt='" . _DELETE . "' title='" . _DELETE . "'>
                     </a>";
            }
            echo "
            <input type='hidden' name='oldtitle[" . $i->getVar('bid') . "]' value='" . $i->getVar('title') . "'>
            <input type='hidden' name='oldside[" . $i->getVar('bid') . "]' value='" . $i->getVar('side') . "'>
            <input type='hidden' name='oldweight[" . $i->getVar('bid') . "]' value='" . $i->getVar('weight') . "'>
            <input type='hidden' name='oldvisible[" . $i->getVar('bid') . "]' value='" . $i->getVar('visible') . "'>
            <input type='hidden' name='oldgroups[" . $i->getVar('groups') . "]' value='" . $i->getVar('groups') . "'>
            <input type='hidden' name='oldbcachetime[" . $i->getVar('bid') . "]' value='" . $i->getVar('bcachetime') . "'>
            <input type='hidden' name='bid[" . $i->getVar('bid') . "]' value='" . $i->getVar('bid') . "'>
            </td></tr>
            ";
            $class = ('even' === $class) ? 'odd' : 'even';
        }
        echo "<tr><td class='foot' align='center' colspan='8'>
        <input type='hidden' name='op' value='order'>
        " . $GLOBALS['xoopsSecurity']->getTokenHTML() . "
        <input type='submit' name='submit' value='" . _SUBMIT . "'>
        </td></tr></table>
        </form>
        <br><br>";
    }

    /**
     * @param int $bid
     */
    public static function cloneBlock($bid)
    {
        require __DIR__ . '/admin_header.php';
        //require __DIR__ . '/admin_header.php';
        \xoops_cp_header();

        $moduleDirName      = \basename(\dirname(__DIR__));
        $moduleDirNameUpper = \mb_strtoupper($moduleDirName);

        \xoops_loadLanguage('admin', 'system');
        \xoops_loadLanguage('admin/blocksadmin', 'system');
        \xoops_loadLanguage('admin/groups', 'system');

        //        mpu_adm_menu();
        $myblock = new \XoopsBlock($bid);
        $db      = \XoopsDatabaseFactory::getDatabaseConnection();
        $sql     = 'SELECT module_id FROM ' . $db->prefix('block_module_link') . ' WHERE block_id=' . (int)$bid;
        $result  = $db->query($sql);
        $modules = [];
        while (false !== ($row = $db->fetchArray($result))) {
            $modules[] = (int)$row['module_id'];
        }
        $is_custom = ('C' === $myblock->getVar('block_type') || 'E' === $myblock->getVar('block_type'));
        $block     = [
            'title'      => $myblock->getVar('title') . ' Clone',
            'form_title' => \constant('CO_' . $moduleDirNameUpper . '_' . 'BLOCKS_CLONEBLOCK'),
            'name'       => $myblock->getVar('name'),
            'side'       => $myblock->getVar('side'),
            'weight'     => $myblock->getVar('weight'),
            'visible'    => $myblock->getVar('visible'),
            'content'    => $myblock->getVar('content', 'N'),
            'modules'    => $modules,
            'is_custom'  => $is_custom,
            'ctype'      => $myblock->getVar('c_type'),
            'bcachetime' => $myblock->getVar('bcachetime'),
            'op'         => 'clone_ok',
            'bid'        => $myblock->getVar('bid'),
            'edit_form'  => $myblock->getOptions(),
            'template'   => $myblock->getVar('template'),
            'options'    => $myblock->getVar('options'),
        ];
        echo '<a href="blocksadmin2.php">' . constant('CO_' . $moduleDirNameUpper . '_' . 'BADMIN') . '</a>&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;' . \_AM_SYSTEM_BLOCKS_CLONEBLOCK . '<br><br>';
        require_once __DIR__ . '/blockform.php';
        $form->display();
        //        xoops_cp_footer();
        require_once __DIR__ . '/admin_footer.php';
        exit();
    }

    /**
     * @param int $bid
     * @param     $bside
     * @param     $bweight
     * @param     $bvisible
     * @param     $bcachetime
     * @param     $bmodule
     * @param     $options
     */
    public static function isBlockCloned($bid, $bside, $bweight, $bvisible, $bcachetime, $bmodule, $options)
    {
        \xoops_loadLanguage('admin', 'system');
        \xoops_loadLanguage('admin/blocksadmin', 'system');
        \xoops_loadLanguage('admin/groups', 'system');

        /** @var \XoopsBlock $block */
        $block = new \XoopsBlock($bid);
        $clone = $block->xoopsClone();
        if (empty($bmodule)) {
            \xoops_cp_header();
            \xoops_error(\sprintf(_AM_NOTSELNG, _AM_VISIBLEIN));
            \xoops_cp_footer();
            exit();
        }
        $clone->setVar('side', $bside);
        $clone->setVar('weight', $bweight);
        $clone->setVar('visible', $bvisible);
        //$clone->setVar('content', $_POST['bcontent']);
        $clone->setVar('title', Request::getString('btitle', '', 'POST'));
        $clone->setVar('bcachetime', $bcachetime);
        if (isset($options) && (\count($options) > 0)) {
            $options = \implode('|', $options);
            $clone->setVar('options', $options);
        }
        $clone->setVar('bid', 0);
        if ('C' === $block->getVar('block_type') || 'E' === $block->getVar('block_type')) {
            $clone->setVar('block_type', 'E');
        } else {
            $clone->setVar('block_type', 'D');
        }
        $newid = $clone->store();
        if (!$newid) {
            \xoops_cp_header();
            $clone->getHtmlErrors();
            \xoops_cp_footer();
            exit();
        }
        if ('' !== $clone->getVar('template')) {
            /** @var \XoopsTplfileHandler $tplfileHandler */
            $tplfileHandler = \xoops_getHandler('tplfile');
            $btemplate      = $tplfileHandler->find($GLOBALS['xoopsConfig']['template_set'], 'block', $bid);
            if (\count($btemplate) > 0) {
                $tplclone = $btemplate[0]->xoopsClone();
                $tplclone->setVar('tpl_id', 0);
                $tplclone->setVar('tpl_refid', $newid);
                $tplfileHandler->insert($tplclone);
            }
        }
        $db = \XoopsDatabaseFactory::getDatabaseConnection();
        foreach ($bmodule as $bmid) {
            $sql = 'INSERT INTO ' . $db->prefix('block_module_link') . ' (block_id, module_id) VALUES (' . $newid . ', ' . $bmid . ')';
            $db->query($sql);
        }
        $groups = &$GLOBALS['xoopsUser']->getGroups();
        $count  = \count($groups);
        for ($i = 0; $i < $count; ++$i) {
            $sql = 'INSERT INTO ' . $db->prefix('group_permission') . ' (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES (' . $groups[$i] . ', ' . $newid . ", 1, 'block_read')";
            $db->query($sql);
        }
        \redirect_header('blocksadmin.php?op=list', 1, _AM_DBUPDATED);
    }

    /**
     * @param int    $bid
     * @param string $title
     * @param int    $weight
     * @param bool   $visible
     * @param string $side
     * @param int    $bcachetime
     */
    public static function setOrder($bid, $title, $weight, $visible, $side, $bcachetime)
    {
        $myblock = new \XoopsBlock($bid);
        $myblock->setVar('title', $title);
        $myblock->setVar('weight', $weight);
        $myblock->setVar('visible', $visible);
        $myblock->setVar('side', $side);
        $myblock->setVar('bcachetime', $bcachetime);
        //        $myblock->store();
        /** @var \XoopsBlockHandler $blockHandler */
        $blockHandler = xoops_getHandler('block');
        return $blockHandler->insert($myblock);
    }

    /**
     * @param int $bid
     */
    public static function editBlock($bid)
    {
        require_once \dirname(__DIR__,2) . '/admin/admin_header.php';
        \xoops_cp_header();
        $moduleDirName      = \basename(\dirname(__DIR__));
        $moduleDirNameUpper = \mb_strtoupper($moduleDirName);
        \xoops_loadLanguage('admin', 'system');
        \xoops_loadLanguage('admin/blocksadmin', 'system');
        \xoops_loadLanguage('admin/groups', 'system');
        //        mpu_adm_menu();
        $myblock = new \XoopsBlock($bid);
        $db      = \XoopsDatabaseFactory::getDatabaseConnection();
        $sql     = 'SELECT module_id FROM ' . $db->prefix('block_module_link') . ' WHERE block_id=' . (int)$bid;
        $result  = $db->query($sql);
        $modules = [];
        while (false !== ($row = $db->fetchArray($result))) {
            $modules[] = (int)$row['module_id'];
        }
        $is_custom = ('C' === $myblock->getVar('block_type') || 'E' === $myblock->getVar('block_type'));
        $block     = [
            'title'      => $myblock->getVar('title'),
            'form_title' => \_AM_SYSTEM_BLOCKS_EDITBLOCK,
            //        'name'       => $myblock->getVar('name'),
            'side'       => $myblock->getVar('side'),
            'weight'     => $myblock->getVar('weight'),
            'visible'    => $myblock->getVar('visible'),
            'content'    => $myblock->getVar('content', 'N'),
            'modules'    => $modules,
            'is_custom'  => $is_custom,
            'ctype'      => $myblock->getVar('c_type'),
            'bcachetime' => $myblock->getVar('bcachetime'),
            'op'         => 'edit_ok',
            'bid'        => $myblock->getVar('bid'),
            'edit_form'  => $myblock->getOptions(),
            'template'   => $myblock->getVar('template'),
            'options'    => $myblock->getVar('options'),
        ];
        echo '<a href="blocksadmin2.php">' . constant('CO_' . $moduleDirNameUpper . '_' . 'BADMIN') . '</a>&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;' . \_AM_SYSTEM_BLOCKS_EDITBLOCK . '<br><br>';

        /** @var \XoopsThemeForm $form */
//                $form = new Blockform();
//        $form->render($block);
//        $form = new \XoopsThemeForm();

        $form->display();

        //        xoops_cp_footer();
        require_once \dirname(__DIR__,2) . '/admin/admin_footer.php';
        exit();
    }

    /**
     * @param int               $bid
     * @param string            $btitle
     * @param string            $bside
     * @param int               $bweight
     * @param bool              $bvisible
     * @param int               $bcachetime
     * @param array             $bmodule
     * @param null|array|string $options
     * @param null|array        $groups
     */
    public static function updateBlock($bid, $btitle, $bside, $bweight, $bvisible, $bcachetime, $bmodule, $options, $groups)
    {
        $myblock = new XoopsBlock($bid);
        $myblock->setVar('title', $btitle);
        $myblock->setVar('weight', $bweight);
        $myblock->setVar('visible', $bvisible);
        $myblock->setVar('side', $bside);
        $myblock->setVar('bcachetime', $bcachetime);
        //update block options
        if (isset($options)) {
            $options_count = \count($options);
            if ($options_count > 0) {
                //Convert array values to comma-separated
                for ($i = 0; $i < $options_count; ++$i) {
                    if (\is_array($options[$i])) {
                        $options[$i] = \implode(',', $options[$i]);
                    }
                }
                $options = \implode('|', $options);
                $myblock->setVar('options', $options);
            }
        }
        $myblock->store();

        if (!empty($bmodule) && \count($bmodule) > 0) {
            $sql = \sprintf('DELETE FROM `%s` WHERE block_id = %u', $GLOBALS['xoopsDB']->prefix('block_module_link'), $bid);
            $GLOBALS['xoopsDB']->query($sql);
            if (\in_array(0, $bmodule)) {
                $sql = \sprintf('INSERT INTO `%s` (block_id, module_id) VALUES (%u, %d)', $GLOBALS['xoopsDB']->prefix('block_module_link'), $bid, 0);
                $GLOBALS['xoopsDB']->query($sql);
            } else {
                foreach ($bmodule as $bmid) {
                    $sql = \sprintf('INSERT INTO `%s` (block_id, module_id) VALUES (%u, %d)', $GLOBALS['xoopsDB']->prefix('block_module_link'), $bid, (int)$bmid);
                    $GLOBALS['xoopsDB']->query($sql);
                }
            }
        }
        $sql = \sprintf('DELETE FROM `%s` WHERE gperm_itemid = %u', $GLOBALS['xoopsDB']->prefix('group_permission'), $bid);
        $GLOBALS['xoopsDB']->query($sql);
        if (!empty($groups)) {
            foreach ($groups as $grp) {
                $sql = \sprintf("INSERT INTO `%s` (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES (%u, %u, 1, 'block_read')", $GLOBALS['xoopsDB']->prefix('group_permission'), $grp, $bid);
                $GLOBALS['xoopsDB']->query($sql);
            }
        }
        \redirect_header($_SERVER['SCRIPT_NAME'], 1, \constant('CO_' . $moduleDirNameUpper . '_' . 'UPDATE_SUCCESS'));
    }
}


