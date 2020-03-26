{*Template Name:页头导航栏公共区*}
<div class="header{if $zbp->Config('tpure')->PostFIXMENUON=='1'} fixed{/if}">
    <div class="wrap">
{if $zbp->Config('tpure')->PostLOGOON == '1'}
        <div class="logo{if $zbp->Config('tpure')->PostLOGOHOVERON=='1'} on{/if}"><a href="{$host}"><img src="{if $zbp->Config('tpure')->PostLOGO}{$zbp->Config('tpure')->PostLOGO}{else}{$host}zb_users/zb_users/{$theme}/style/images/logo.png{/if}" alt="{$name}"></a></div>
{else}
        <h1 class="name"><a href="{$host}">{$name}</a></h1>
{/if}
        <div class="head">
            <div class="menuico"><span></span><span></span><span></span></div>
            <div class="menu">
                <ul{if $zbp->Config('tpure')->PostSEARCHON=='0'} class="nosch"{/if}>
                    {module:navbar}

{if $zbp->Config('tpure')->PostSEARCHON=='1'}
                    <div class="schico statefixed">
                        <a href="javascript:;"></a>
                        <div class="schfixed">
                            <form method="post" name="search" action="{$host}zb_system/cmd.php?act=search">
                                <input type="text" name="q" placeholder="{if $zbp->Config('tpure')->PostSCHTXT}{$zbp->Config('tpure')->PostSCHTXT}{else}{$lang['tpure']['schtxt']}{/if}" class="schinput">
                                <button type="submit" class="btn"></button>
                            </form>
                        </div>
                    </div>
{/if}
                </ul>
{if $zbp->Config('tpure')->PostSEARCHON=='1'}
                <form method="post" name="search" action="{$host}zb_system/cmd.php?act=search" class="sch-m">
                    <input type="text" name="q" placeholder="{if $zbp->Config('tpure')->PostSCHTXT}{$zbp->Config('tpure')->PostSCHTXT}{else}{$lang['tpure']['schtxt']}{/if}" class="schinput">
                    <button type="submit" class="btn"></button>
                </form>
{/if}
            </div>
        </div>
    </div>
</div>