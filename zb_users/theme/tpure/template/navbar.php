{* Template Name:页头导航栏公共区(勿选) *}
<div class="header{if $zbp->Config('tpure')->PostFIXMENUON=='1'} fixed{/if}">
    <div class="wrap">
{if $zbp->Config('tpure')->PostLOGOON == '1'}
        <div class="logo{if $zbp->Config('tpure')->PostLOGOHOVERON=='1'} on{/if}"><a href="{$host}"><img src="{$zbp->Config('tpure')->PostNIGHTLOGO}" alt="{$subname}"><img src="{$zbp->Config('tpure')->PostLOGO}" alt="{$subname}"></a></div>
{else}
        <div class="logo{if $zbp->Config('tpure')->PostLOGOHOVERON=='1'} on{/if}"><h1 class="name"><a href="{$host}">{$name}</a></h1></div>
{/if}
        <div class="head">
            {if $zbp->Config('tpure')->PostSIGNON}
<div class="account">
{if $user.ID > 0}
                <div class="signuser{if $zbp->Config('tpure')->PostSIGNUSERSTYLE == '0'} normal{else} simple{/if}">
                    <a href="{if $zbp->Config('tpure')->PostSIGNUSERURL}{$zbp->Config('tpure')->PostSIGNUSERURL}{else}{$host}zb_system/admin/index.php{/if}" class="uimg"><img src="{tpure_MemberAvatar($user)}" alt="{$user.StaticName}">{if $zbp->Config('tpure')->PostSIGNUSERSTYLE == '0'}<span class="uname">{$user.StaticName}</span>{/if}</a>
                    <div class="signuserpop">
                        <div class="signusermenu">
                            {$zbp->Config('tpure')->PostSIGNUSERMENU}

                            <a href="{BuildSafeCmdURL('act=logout')}">{$lang['tpure']['exit']}</a>
                        </div>
                    </div>
                </div>
    {else}
    <div class="sign"><span><a href="{$zbp->Config('tpure')->PostSIGNBTNURL}">{$zbp->Config('tpure')->PostSIGNBTNTEXT}</a></span></div>
    {/if}
        </div>
            {/if}
<div class="menuico"><span></span><span></span><span></span></div>
            <div class="menu">
                <ul{if $zbp->Config('tpure')->PostSEARCHON=='0'} class="nosch"{/if}>
                    {module:navbar}
                
                </ul>
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