This is the XOOPS Module MyMenus Skin for xbootstrap theme  (Bootstrap 3)

1. If you already installed the MyMenus Module, create a menu in Menus Manager 
2. Add Link to your menu in Links Manager 
3. In Links Manager, use the following

Menu Image - > fa fa-cube
::  You can use any of your favorite Font Awesome Icon. Leave it empty if you don't want to use any icon

4. Activate the My menu Block 
5. In the block settings, use the following:

a. Select your menu
b. Select Skin -> xboostrap
c. Use skin from theme —> No
d. Display Method —> Assign to template
e. Unique ID —> xbootstrap
f. Groups -> Select groups that you want to give permission to see the menu

6. Save the block
7. In you XOOPS site folder search for this file themes/xbootstrap/tpl/nav-menu.tpl and rename it nav_menu_backup.tpl
8. Create a new empty nav-menu.tpl in the same folder and paste this code. Save the file.

<{$xoops_menu_xbootstrap}>
           
9. Open you site to see the menu in action.
10. If you can't ee the menu try clearing your XOOPS Cache 
