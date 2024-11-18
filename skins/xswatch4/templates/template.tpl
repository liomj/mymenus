<div id="menu_area" class="menu-area">
    <div class="container">
        <div class="row">
            <nav class="navbar navbar-expand-lg navbar-dark custom-nav">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
<ul class="navbar-nav mr-auto">
    <{foreach item=menu from=$block}>
        <!-- Main Menu Item -->
        <{if $menu.level == 0}>
            <li class="nav-item <{if $menu.hassub}>dropdown<{/if}>">
                <a class="nav-link <{if $menu.hassub}>dropdown-toggle<{/if}>" 
                   href="<{$menu.link}>" 
                   <{if $menu.hassub}>
                       id="navbarDropdown-<{$menu.id}>" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                   <{/if}>
                   target="<{$menu.target}>" alt="<{$menu.alt_title}>" title="<{$menu.alt_title}>">
                    <{if $menu.image}><span class="<{$menu.image}>"></span><{/if}>
                    <{$menu.title}> 
                </a>
                <!-- Submenu -->
                <{if $menu.hassub}>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown-<{$menu.id}>">
                        <{foreach item=menu from=$block}>
                            <{if $menu.level == 1}>
                                <li class="dropdown-item">
                                    <a href="<{$submenu.link}>" target="<{$menu.target}>" alt="<{$menu.alt_title}>" title="<{$menu.alt_title}>">
                                        <{if $menu.image}><span class="<{$menu.image}>"></span><{/if}>
                                        <{$menu.title}>
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


                    <ul class="navbar-nav ml-auto d-none d-block d-sm-block d-md-none">
                        <li class="nav-item">
                            <form class="form-inline my-2 my-lg-0" role="search" action="<{xoAppUrl 'search.php'}>" method="get">
                                <div class="input-group mb-3">
                                    <input class="form-control" type="text" name="query" placeholder="<{$smarty.const.THEME_SEARCH_TEXT}>">
                                    <div class="input-group-append">
                                        <button class="btn btn-secondary" type="submit">
                                            <i class="fa fa-search" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
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
    $('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
        if (!$(this).next().hasClass('show')) {
            $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
        }
        var $subMenu = $(this).next(".dropdown-menu");
        $subMenu.toggleClass('show');

        $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
            $('.dropdown-submenu .show').removeClass("show");
        });

        return false;
    });
})(jQuery);
</script>
