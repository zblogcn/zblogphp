<?php exit(); ?>
<div class="side{if GetVars('side','COOKIE')} on{/if}">
    <div class="logo"><a href="{$host}zb_system/admin2/"><img src="{$host}zb_system/admin2/{$backendtheme}/style/images/logo-icon.svg" alt="后台管理"><img src="{$host}zb_system/admin2/{$backendtheme}/style/images/logo-light.svg" alt="" class="logolight"><img src="{$host}zb_system/admin2/{$backendtheme}/style/images/logo-dark.svg" alt="" class="logodark"></a><span class="sideclose"></span></div>
    <div class="menu">
        <ul>
            {ResponseAdmin_LeftMenu()}
        </ul>
    </div>
</div>
<div class="fademask{if GetVars('side','COOKIE')} on{/if}"></div>