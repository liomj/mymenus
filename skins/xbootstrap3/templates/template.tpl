<div id="menu_area" class="menu-area">
    <div class="container">
        <div class="row">
            <nav class="navbar navbar-default custom-nav">
                <div class="navbar-header">
                    <!-- Logo -->
                    <a href="<{$xoops_url}>" class="navbar-brand xlogo" title="<{$xoops_sitename}>">
                        <img src="<{$xoops_url}>/images/logo-transparent.png" alt="<{$xoops_sitename}>">
                    </a>
                    
                    <!-- Navbar toggle button for mobile view -->
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbarSupportedContent">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                
                <!-- Navbar menu -->
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="nav navbar-nav">
                        <{foreach item=menu from=$block}>
                            <!-- Main Menu Item -->
                            <{if $menu.level == 0}>
                                <li class="<{if $menu.hassub}>dropdown<{/if}>">
                                    <a href="<{$menu.link}>" 
                                       class="<{if $menu.hassub}>dropdown-toggle<{/if}>" 
                                       <{if $menu.hassub}>data-toggle="dropdown" aria-expanded="false"<{/if}>
                                       target="<{$menu.target}>" 
                                       title="<{$menu.alt_title}>">
                                        <{if $menu.image}><span class="<{$menu.image}>"></span><{/if}>
                                        <{$menu.title}>
                                    </a>
                                    <!-- Submenu -->
                                    <{if $menu.hassub}>
                                        <ul class="dropdown-menu">
                                            <{foreach item=submenu from=$block}>
                                                <{if $submenu.level == 1}>
                                                    <li class="<{if $submenu.hassub}>dropdown-submenu<{/if}>">
                                                        <a href="<{$submenu.link}>" 
                                                           class="<{if $submenu.hassub}>dropdown-toggle<{/if}>"
                                                           <{if $submenu.hassub}>data-toggle="dropdown" aria-expanded="false"<{/if}>
                                                           target="<{$submenu.target}>" 
                                                           title="<{$submenu.alt_title}>">
                                                            <{if $submenu.image}><span class="<{$submenu.image}>"></span><{/if}>
                                                            <{$submenu.title}>
                                                        </a>
                                                        <!-- Submenu level 2 -->
                                                        <{if $submenu.hassub}>
                                                            <ul class="dropdown-menu">
                                                                <{foreach item=subsubmenu from=$block}>
                                                                    <{if $subsubmenu.level == 2}>
                                                                        <li>
                                                                            <a href="<{$subsubmenu.link}>" 
                                                                               target="<{$subsubmenu.target}>" 
                                                                               title="<{$subsubmenu.alt_title}>">
                                                                                <{if $subsubmenu.image}><span class="<{$subsubmenu.image}>"></span><{/if}>
                                                                                <{$subsubmenu.title}>
                                                                            </a>
                                                                        </li>
                                                                    <{/if}>
                                                                <{/foreach}>
                                                            </ul>
                                                        <{/if}>
                                                    </li>
                                                <{/if}>
                                            <{/foreach}>
                                        </ul>
                                    <{/if}>
                                </li>
                            <{/if}>
                        <{/foreach}>
                    </ul>

                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <form class="navbar-form" role="search" action="<{xoAppUrl 'search.php'}>" method="get">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="query" placeholder="<{$smarty.const.THEME_SEARCH_TEXT}>">
                                </div>
                                <button type="submit" class="btn btn-default">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                </button>
                                <input type="hidden" name="action" value="results">
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</div>

<script>
(function($) {
    // Multi-level dropdowns
    $('.dropdown-submenu a').on('click', function(e) {
        var $subMenu = $(this).next('.dropdown-menu');
        
        // If submenu exists, toggle it
        if ($subMenu.is(':visible')) {
            $subMenu.parent().removeClass('open');
            $subMenu.slideUp(200);
        } else {
            $subMenu.parent().addClass('open');
            $subMenu.stop(true, true).slideDown(200);
        }
        
        e.stopPropagation();
    });

    // Closing all dropdowns when clicked outside
    $(document).on('click', function(e) {
        var $target = $(e.target);
        if (!$target.closest('.dropdown').length) {
            $('.dropdown-menu').slideUp(200);
            $('.dropdown').removeClass('open');
        }
    });
})(jQuery);
</script>