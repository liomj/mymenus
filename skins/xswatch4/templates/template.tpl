<div id="menu_area" class="menu-area">
    <div class="container">
        <div class="row">
            <nav class="navbar navbar-expand-lg navbar-light custom-nav">
                <div class="navbar-header">
                    <!-- Logo -->
                    <a href="<{$xoops_url}>" class="navbar-brand xlogo" title="<{$xoops_sitename}>">
                        <img src="<{$xoops_url}>/images/logo-transparent.png" alt="<{$xoops_sitename}>">
                    </a>

                    <!-- Navbar toggle button for mobile view -->
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                <!-- Navbar menu -->
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <{foreach item=menu from=$block}>
                            <!-- Main Menu Item -->
                            <{if $menu.level == 0}>
                                <li class="nav-item <{if $menu.hassub}>dropdown<{/if}>">
                                    <a href="<{$menu.link}>" 
                                       class="nav-link <{if $menu.hassub}>dropdown-toggle<{/if}>" 
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
                                                    <li class="dropdown-item <{if $submenu.hassub}>dropdown<{/if}>">
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
                                                                        <li class="dropdown-item">
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

                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <form class="form-inline" role="search" action="<{xoAppUrl 'search.php'}>" method="get">
                                <input type="text" class="form-control mr-sm-2" name="query" placeholder="<{$smarty.const.THEME_SEARCH_TEXT}>">
                                <button type="submit" class="btn btn-primary">
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
    $('.dropdown-item.dropdown-toggle').on('click', function(e) {
        var $subMenu = $(this).next('.dropdown-menu');

        // If submenu exists, toggle it
        if ($subMenu.is(':visible')) {
            $subMenu.parent().removeClass('show');
            $subMenu.removeClass('show');
        } else {
            $subMenu.parent().addClass('show');
            $subMenu.addClass('show');
        }

        e.stopPropagation();
    });

    // Closing all dropdowns when clicked outside
    $(document).on('click', function(e) {
        var $target = $(e.target);
        if (!$target.closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
            $('.dropdown').removeClass('show');
        }
    });
})(jQuery);
</script>
