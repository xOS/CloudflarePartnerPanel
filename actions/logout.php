<?php
/*
 * Logout action.
 */

if (!isset($adapter)) {exit;}

setcookie('cloudflare_email', null, -1);
setcookie('user_key', null, -1);
setcookie('user_api_key', null, -1);

$msg = '<p class="alert alert-success" role="alert">' . _('成功') . ', <a href="./">' . _('前往管理中心') . '</a></p>';
echo $msg;
