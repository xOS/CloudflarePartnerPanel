<?php
/*
 * Security page. (SSL and DNSSEC information)
 */

if (!isset($adapter)) {exit;}

$zone_name = $_GET['domain'];
if (!isset($_GET['page'])) {
	$_GET['page'] = 1;
}
$dns = new Cloudflare\API\Endpoints\DNS($adapter);
$zones = new Cloudflare\API\Endpoints\Zones($adapter);

$zoneID = $_GET['zoneid'];

?>
<strong><?php echo '<h1 class="h5"><a href="?action=security&amp;domain=' . $zone_name . '&amp;zoneid=' . $zoneID . '">' . strtoupper($zone_name) . '</a></h1>'; ?></strong>
<hr>
<div class="am-scrollable-horizontal">
	<h3 id="ssl" class="mt-5 mb-3"><?php echo _('SSL 验证'); ?></h3><?php
try {
	$sslverify = $adapter->get('zones/' . $zoneID . '/ssl/verification?retry=true');
	$sslverify = json_decode($sslverify->getBody(), true)['result'];
} catch (Exception $e) {
	$sslverify[0]['validation_method'] = 'http';
}

foreach ($sslverify as $sslv) {
/*
 * We need `_tlo-wildcard` subdomain to support anycast IP information.
 */
	if (substr($sslv['hostname'], 0, 14) != '_tlo-wildcard.') {
		if ($sslv['validation_method'] == 'http' && isset($sslv['verification_info']['http_url']) && $sslv['verification_info']['http_url'] != '') {?>
			<h4><?php printf(_('%s 下的 HTTP 文件验证'), $sslv['hostname']);?></h4>
			<p>URL: <code><?php echo $sslv['verification_info']['http_url']; ?></code></p>
			<p>Body: <code><?php echo $sslv['verification_info']['http_body']; ?></code></p><?php
if ($sslv['certificate_status'] != 'active') {
			echo '<p>' . _('SSL 状态') . ': ' . $sslv['certificate_status'] . '</p>';
			if ($sslv['verification_status']) {
				echo '<p>' . _('验证') . ': <span style="color:green;">' . _('成功') . '</span></p>';
			} else {
				echo '<p>' . _('验证') . ': <span style="color:red;">' . _('失败') . '</span></p>';
			}
		}
		} elseif ($sslv['validation_method'] == 'cname' || isset($sslv['verification_info']['record_name'])) {?>
			<h4><?php echo _('CNAME 验证'); ?></h4>
			<table class="am-table am-table-striped am-table-hover am-table-striped am-text-nowrap">
			<thead>
			<tr>
				<th><?php echo _('SSL 验证记录名'); ?></th>
				<th>CNAME</th>
			</tr>
			</thead>
			<tbody>

			<?php echo '<tr>
				<td><code>' . $sslv['verification_info']['record_name'] . '</code></td>
				<td><code>' . $sslv['verification_info']['record_target'] . '</code></td>
				</tr>';
			?>
			</tbody>
			</table><?php
if ($sslv['certificate_status'] != 'active') {
				echo '<p>' . _('SSL 状态') . ': ' . $sslv['certificate_status'] . '</p>';
				if ($sslv['verification_status']) {
					echo '<p>' . _('验证') . ': <span style="color:green;">' . _('成功') . '</span></p>';
				} else {
					echo '<p>' . _('验证') . ': <span style="color:red;">' . _('失败') . '</span></p>';
				}
			}
		} elseif ($sslv['validation_method'] == 'http') {
			if (isset($sslv['hostname'])) {echo '<h4>' . $sslv['hostname'] . '</h4>';}
			echo _('<p style=\"color:green;\">SSL 正常。</p><p>你只需要将默认/海外记录指向 "
"Cloudflare, SSL 证书便会自动签发和续期。</p>');
		} else {
			echo '<h4>Unknown Verification</h4><pre>';
			print_r($sslv['verification_info']);
			echo '</pre>';
			if ($sslv['certificate_status'] != 'active') {
				echo '<p>' . _('SSL 状态') . ': ' . $sslv['certificate_status'] . '</p>';
				if ($sslv['verification_status']) {
					echo '<p>' . _('验证') . ': <span style="color:green;">' . _('成功') . '</span></p>';
				} else {
					echo '<p>' . _('验证') . ': <span style="color:red;">' . _('失败') . '</span></p>';
				}
			}
		}
	}
}
?>
	<h3 class="mt-5 mb-3"><?php echo _('DNSSEC <small>(仅限 NS 接入)</small>'); ?></h3><?php

echo '<p>' . _('此功能仅限配置了 Cloudflare DNS 的用户使用, 如果你使用的是第三方 DNS 服务，请"
"不要开启此功能，也不要配置 DS 记录, 否则可能会导致域名无法访问。') . '</p>';

try {
	$dnssec = $adapter->get('zones/' . $zoneID . '/dnssec');
	$dnssec = json_decode($dnssec->getBody());
} catch (Exception $e) {
	exit('<div class="alert alert-danger" role="alert">' . $e->getMessage() . '</div>');
}

if ($dnssec->result->status == 'active') {
	echo '<p style="color:green;">' . _('已激活') . '</p><p>DS：<code>' . $dnssec->result->ds . '</code></p><p>Public Key：<code>' . $dnssec->result->public_key . '</code></p>';
	echo '<p><a href="?action=dnssec&zoneid=' . $zoneID . '&domain=' . $zone_name . '&do=disabled">' . _('关闭') . '</a></p>';
} elseif ($dnssec->result->status == 'pending') {
	echo '<p style="color:orange;">' . _('等待中') . '</p><p>DS：<code>' . $dnssec->result->ds . '</code></p><p>Public Key：<code>' . $dnssec->result->public_key . '</code></p>';
	echo '<p><a href="?action=dnssec&zoneid=' . $zoneID . '&domain=' . $zone_name . '&do=disabled">' . _('关闭') . '</a></p>';
} else {
	echo '<p style="color:red;">' . _('未激活') . '</p>';
	echo '<p><a href="?action=dnssec&zoneid=' . $zoneID . '&domain=' . $zone_name . '&do=active" onclick="return confirm(\'' . _('此功能仅限配置了 Cloudflare DNS 的用户使用, 如果你使用的是第三方 DNS 服务，请"
	"不要开启此功能，也不要配置 DS 记录, 否则可能会导致域名无法访问。') . '\')">' . _('开启') . '</a></p>';
} ?>
</div>
