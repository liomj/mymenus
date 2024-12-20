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
$skinVersion['template'] = 'templates/template.tpl';

$skinVersion['css'] = [
    'css/superfish.css',
    'css/superfish-navbar.css',
];

//$skinVersion['js'] = ['../../../../browse.php?Frameworks/jquery/jquery.js',
$skinVersion['js'] = [
    '../../assets/js/jquery.js',
    //        '../../assets/js/jquery-1.11.2.min.js',
    '../../assets/js/hoverIntent.js',
    '../../assets/js/superfish.js',
];

$header = "\n" . '<script type="module">
      jQuery(document).ready(function () {
          jQuery("ul.sf-menu").superfish();          
             pathClass:  "current"
         });
</script>';

$skinVersion['header'] = $header;
