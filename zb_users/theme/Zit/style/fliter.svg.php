<?php
/*
 * @Name     : Zit Setting
 * @Author   : 吉光片羽
 * @Support  : jgpy.cn
 * @Create   : 2019-12-25 20:10:23
 * @Update   : 2020-02-19 13:18:48
 */

require '../../../../zb_system/function/c_system_base.php';
require '../../../../zb_system/function/c_system_admin.php';

header('Content-Type: text/xml; charset=utf-8');
?><svg height="0" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <filter id="change">
                <feColorMatrix type="hueRotate" values="<?php echo $zbp->Config('Zit')->ColorChange?>" />
        </filter>
        <filter id="deep">
                <feColorMatrix type="saturate" values="1" />
        </filter>        
    </defs>
</svg>