<?php
/**
 * Z-Blog with PHP.
 *
 * @author
 * @copyright (C) RainbowSoft Studio
 *
 * @version 2.0 2013-06-14
 */
require '../function/c_system_base.php';

ob_clean();
// @TODO: Configuable


if( GetVars('postid','GET') > 0 ){

    $key = $zbp->GetCmtKey( GetVars('postid','GET') );
    $form = GetVars('form','GET');
    echo '$(function () {
    $("#'.$form.'").attr("action" , $("#'.$form.'").attr("action") + "&key='.$key.'");
});';
}


die();
?>
