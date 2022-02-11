<?php
/*
 * Delete a specific record for a domain.
 */

if (!isset($adapter)) {exit;}

$dns = new \Cloudflare\API\Endpoints\DNS($adapter);
try {
	if ($dns->deleteRecord($_GET['zoneid'], $_GET['delete'])) {
		echo '<p class="alert alert-success" role="alert">' . _('成功') . '! </p><p><a href="?action=zone&domain=' . $_GET['domain'] . '&amp;zoneid=' . $_GET['zoneid'] . '">' . _('前往管理中心') . '</a></p>';
	} else {
		echo '<p class="alert alert-danger" role="alert">' . _('失败') . '! </p><p><a href="?action=zone&domain=' . $_GET['domain'] . '&amp;zoneid=' . $_GET['zoneid'] . '">' . _('前往管理中心') . '</a></p>';
	}
} catch (Exception $e) {
	exit('<div class="alert alert-danger" role="alert">' . $e->getMessage() . '</div>');
}
