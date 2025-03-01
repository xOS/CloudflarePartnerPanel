<?php
/*
 * Using Partner API to add domain for CNAME or NS setup.
 */

if (!isset($adapter)) {exit;}

if (isset($_POST['submit'])) {
	$zone_name = $_POST['domain'];
	if (isset($_POST['type']) && $_POST['type'] == 'ns') {
		/* NS setup */
		try {
			$res = $cloudflare->zoneSet_full($zone_name);
		} catch (Exception $e) {
			exit('<div class="alert alert-danger" role="alert">' . $e->getMessage() . '</div>');
		}
		if ($res['result'] == 'success') {
			$msg = _('成功') . ', <a target="_blank" href="https://www.cloudflare.com/a/overview/' . $zone_name . '">' . _('前往管理中心') . '</a>. ';
			exit('<div class="alert alert-success" role="alert">' . $msg . '</div>');
		} elseif (isset($res['msg'])) {
			$msg = $res['msg'];
		} else {
			print_r($res);
		}
	}

	$zones = new \Cloudflare\API\Endpoints\Zones($adapter);
	try {
		$zoneID = $zones->getZoneID($zone_name);
	} catch (Exception $e) {
		if ($e->getMessage() == 'Could not find zones with specified name.') {
			$add_domain = true;
		}
	}

	if (isset($add_domain) && $add_domain) {
		try {
			$res = $cloudflare->zoneSet($zone_name, 'example.com', 'www');
		} catch (Exception $e) {
			exit('<div class="alert alert-danger" role="alert">' . $e->getMessage() . '</div>');
		}
	} else {
		exit('<div class="alert alert-danger" role="alert">' . _('无法添加已经存在的域名。') . '</div>');
	}

	if ($res['result'] == 'success') {
		$zones = new \Cloudflare\API\Endpoints\Zones($adapter);
		try {
			$zoneID = $zones->getZoneID($zone_name);
		} catch (Exception $e) {
			exit('<div class="alert alert-danger" role="alert">' . $e->getMessage() . '</div>');
		}

		try {
			$dns = new \Cloudflare\API\Endpoints\DNS($adapter);
			$dnsresult = $dns->listRecords($zoneID)->result;
			/*
				 * Delete @ and `www` record to make this zone fresh.
			*/
			foreach ($dnsresult as $record) {
				if ($record->name == $zone_name) {
					$dns->deleteRecord($zoneID, $record->id);
				} elseif ($record->name == 'www.' . $zone_name) {
					$dns->deleteRecord($zoneID, $record->id);
				}
			}
		} catch (Exception $e) {
			exit('<div class="alert alert-danger" role="alert">' . $e->getMessage() . '</div>');
		}

		$msg = _('成功') . ', <a href="?action=zone&amp;domain=' . $zone_name . '&amp;zoneid=' . $zoneID . '">' . _('前往管理中心') . '</a>. ';
		exit('<div class="alert alert-success" role="alert">' . $msg . '</div>');
	} elseif (isset($res['msg'])) {
		$msg = $res['msg'];
	} else {
		print_r($res);
		exit;
	}
}
if (isset($msg) && $msg != '') {
	echo '<div class="alert alert-danger" role="alert">' . $msg . '</div>';
}

?>
<form method="POST" action="" class="add-domain-form">
	<label for="domain" class="sr-only"><?php echo _('域名'); ?></label>
	<input type="text" id="domain" class="form-control" name="domain" placeholder="<?php echo _('请输入你的域名'); ?>">
	<button type="submit" name="submit" class="btn btn-primary mt-3"><?php echo _('提交'); ?></button>
</form>
