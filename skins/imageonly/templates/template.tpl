<div class='row'>
    <{foreach item=menu from=$block}>
    <{if $menu.level == 0}>
        <a href="<{$menu.link}>" target="<{$menu.target}>" alt="<{$menu.alt_title}>" title="<{$menu.alt_title}>">
       <img src="<{$menu.image}>" /> </a>
        
    <{/if}>
    <{/foreach}>
</div>