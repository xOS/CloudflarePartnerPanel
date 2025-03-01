<?php
/*
 * Zone setup page
 */
if (!isset($adapter)) {exit;}

if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
	$_GET['page'] = 1;
}
$dns = new Cloudflare\API\Endpoints\DNS($adapter);
$zones = new Cloudflare\API\Endpoints\Zones($adapter);

$zoneID = $_GET['zoneid'];

try {
	$dnsresult_data = $dns->listRecords($zoneID, false, false, false, intval($_GET['page']));
} catch (Exception $e) {
	exit('<div class="alert alert-danger" role="alert">' . $e->getMessage() . '</div>');
}

$dnsresult = $dnsresult_data->result;
$zone_name = htmlspecialchars($_GET['domain']);

foreach ($dnsresult as $record) {
	$zone_name = $record->zone_name;
	$dnsids[$record->id] = true;
	$dnsproxyied[$record->id] = $record->proxied;
	$dnstype[$record->id] = $record->type;
	$dnscontent[$record->id] = $record->content;
	$dnsname[$record->id] = $record->name;
	$dnscheck[$record->name] = true;
}
?>
<strong><?php echo '<h1 class="h5"><a href="?action=zone&amp;domain=' . $zone_name . '&amp;zoneid=' . $zoneID . '">' . strtoupper($zone_name) . '</a></h1>'; ?></strong>
<hr><?php
/* Toggle the CDN */
if (isset($_GET['enable']) && !$dnsproxyied[$_GET['enable']]) {
	if ($dns->updateRecordDetails($zoneID, $_GET['enable'], ['type' => $dnstype[$_GET['enable']], 'content' => $dnscontent[$_GET['enable']], 'name' => $dnsname[$_GET['enable']], 'proxied' => true])->success == true) {
		echo '<p class="alert alert-success" role="alert">' . _('成功') . '! </p>';
	} else {
		echo '<p class="alert alert-danger" role="alert">' . _('失败') . '! </p><p><a href="?action=zone&amp;domain=' . $zone_name . '&amp;zoneid=' . $zoneID . '">' . _('前往管理中心') . '</a></p>';
		exit();
	}
} else {
	$_GET['enable'] = 1;
	if (isset($_GET['disable']) && $dnsproxyied[$_GET['disable']]) {
		if ($dns->updateRecordDetails($zoneID, $_GET['disable'], ['type' => $dnstype[$_GET['disable']], 'content' => $dnscontent[$_GET['disable']], 'name' => $dnsname[$_GET['disable']], 'proxied' => false])->success == true) {
			echo '<p class="alert alert-success" role="alert">' . _('成功！') . '</p>';
		} else {
			echo '<p class="alert alert-danger" role="alert">' . _('失败') . '! </p><p><a href="?action=zone&amp;domain=' . $zone_name . '&amp;zoneid=' . $zoneID . '">' . _('前往管理中心') . '</a></p>';
			exit();
		}
	} else {
		$_GET['disable'] = 1;
	}
}
?>
<div class="btn-group dropright">
	<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		<?php echo _('目录'); ?>
	</button>
	<div class="dropdown-menu">
		<a class="dropdown-item" href="#dns"><?php echo _('DNS 管理'); ?></a>
		<a class="dropdown-item" href="#cname"><?php echo _('CNAME 接入'); ?></a>
		<a class="dropdown-item" href="#ip"><?php echo _('IP 接入'); ?></a>
		<a class="dropdown-item" href="#ns"><?php echo _('NS 接入'); ?></a>
		<div class="dropdown-divider"></div>
		<a class="dropdown-item" href="https://dash.cloudflare.com/" target="_blank"><?php echo _('更多设置'); ?></a>
	</div>
</div>
<h3 class="mt-5 mb-3" id="dns"><?php echo _('DNS 管理'); ?>
	<a class="btn btn-primary float-sm-right d-block mt-3 mt-sm-0" href='?action=add_record&amp;zoneid=<?php echo $zoneID; ?>&amp;domain=<?php echo $zone_name; ?>'><?php echo _('添加新记录'); ?></a>
</h3>
<table class="table table-striped">
	<thead>
		<tr>
			<th scope="col" class="d-none d-md-table-cell"><?php echo _('记录类型'); ?></th>
			<th scope="col"><?php echo _('主机名'); ?></th>
			<th scope="col" class="d-none d-md-table-cell"><?php echo _('内容'); ?></th>
			<th scope="col" class="d-none d-md-table-cell"><?php echo _('TTL'); ?></th>
			<th scope="col" class="d-none d-md-table-cell"><?php echo _('操作'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
$no_record_yet = true;
foreach ($dnsresult as $record) {
	if ($record->proxiable) {
		if ($record->proxied) {
			$proxiable = '<a href="?action=zone&domain=' . $zone_name . '&disable=' . $record->id . '&page=' . $_GET['page'] . '&amp;zoneid=' . $zoneID . '"><img src="images/cloud_on.png" height="19"></a>';
		} else {
			$proxiable = '<a href="?action=zone&domain=' . $zone_name . '&enable=' . $record->id . '&page=' . $_GET['page'] . '&amp;zoneid=' . $zoneID . '"><img src="images/cloud_off.png" height="30"></a>';
		}
	} else {
		$proxiable = '<img src="images/cloud_off.png" height="30">';
	}
	if (isset($_GET['enable']) && $record->id === $_GET['enable']) {
		$proxiable = '<a href="?action=zone&domain=' . $zone_name . '&disable=' . $record->id . '&page=' . $_GET['page'] . '&amp;zoneid=' . $zoneID . '"><img src="images/cloud_on.png" height="19"></a>';
	} elseif (isset($_GET['disable']) && $record->id === $_GET['disable']) {
		$proxiable = '<a href="?action=zone&domain=' . $zone_name . '&enable=' . $record->id . '&page=' . $_GET['page'] . '&amp;zoneid=' . $zoneID . '"><img src="images/cloud_off.png" height="30"></a>';
	}
	if ($record->type == 'MX') {
		$priority = '<code>' . $record->priority . '</code> ';
	} else {
		$priority = '';
	}
	if (isset($ttl_translate[$record->ttl])) {
		$ttl = $ttl_translate[$record->ttl];
	} else {
		$ttl = $record->ttl . ' s';
	}
	$no_record_yet = false;
	echo '<tr>
		<td class="d-none d-md-table-cell"><code>' . $record->type . '</code></td>
		<td scope="col">
			<div class="d-block d-md-none float-right">' . $proxiable . '</div>
			<div class="d-block d-md-none">' . $record->type . ' ' . _('记录') . '</div>
			<code>' . htmlspecialchars($record->name) . '</code>
			<div class="d-block d-md-none">' . _('指向') . ' ' . '<code>' . htmlspecialchars($record->content) . '</code></div>
			<div class="btn-group dropleft float-right d-block d-md-none" style="margin-top:-1em;">
				<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				' . _('管理') . '
				</button>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="?action=edit_record&domain=' . $zone_name . '&recordid=' . $record->id . '&zoneid=' . $zoneID . '">' . _('编辑') . '</a>
					<a class="dropdown-item" href="?action=delete_record&domain=' . $zone_name . '&delete=' . $record->id . '&zoneid=' . $zoneID . '" onclick="return confirm(\'' . _('你确认要删除') . ' ' . htmlspecialchars($record->name) . '?\')">' . _('删除') . '</a>
				</div>
			</div>
			<div class="d-block d-md-none">' . _('TTL') . ' ' . $ttl . '</div>
		</td>
		<td class="d-none d-md-table-cell">' . $priority . '<code>' . htmlspecialchars($record->content) . '</code></td>
		<td class="d-none d-md-table-cell">' . $ttl . '</td>
		<td class="d-none d-md-table-cell" style="width: 200px;">' . $proxiable . ' |
			<div class="btn-group" role="group">
				<a class="btn btn-dark btn-sm" href="?action=edit_record&domain=' . $zone_name . '&recordid=' . $record->id . '&zoneid=' . $zoneID . '">' . _('编辑') . '</a>
				<a class="btn btn-danger btn-sm" href="?action=delete_record&domain=' . $zone_name . '&delete=' . $record->id . '&zoneid=' . $zoneID . '" onclick="return confirm(\'' . _('你确认要删除') . ' ' . htmlspecialchars($record->name) . '?\')">' . _('删除') . '</a>
			</div>
		</td>
	</tr>';

}
?>
	</tbody>
</table><?php

if ($no_record_yet) {
	echo '<div class="alert alert-warning" role="alert">' . _('该域名还没有任何记录, 请添加！') . '</div>';
}

if (isset($dnsresult_data->result_info->total_pages)) {
	$previous_page = '';
	$next_page = '';
	if ($dnsresult_data->result_info->page < $dnsresult_data->result_info->total_pages) {
		$page_link = $dnsresult_data->result_info->page + 1;
		$next_page = ' | <a href="?action=zone&domain=' . $zone_name . '&page=' . $page_link . '&amp;zoneid=' . $zoneID . '">' . _('下一页') . '</a>';
	}
	if ($dnsresult_data->result_info->page > 1) {
		$page_link = $dnsresult_data->result_info->page - 1;
		$previous_page = '<a href="?action=zone&domain=' . $zone_name . '&page=' . $page_link . '&amp;zoneid=' . $zoneID . '">' . _('上一页') . '</a> | ';
	}
	echo '<p>' . $previous_page . _('页码') . ' ' . $dnsresult_data->result_info->page . '/' . $dnsresult_data->result_info->total_pages . $next_page . '</p>';
}
?>
<p><?php echo _('你可以使用 CNAME, IP 和 NS 任意一种方式接入。'); ?></p>

<h3 class="mt-5 mb-3" id="cname"><?php echo _('CNAME 接入'); ?></h3>
<table class="table table-striped">
	<thead>
		<tr>
			<th scope="col"><?php echo _('主机名'); ?></th>
			<th scope="col" class="d-none d-md-table-cell">CNAME</th>
		</tr>
	</thead>
	<tbody>
		<?php
$avoid_cname_duplicated = [];
$last_domain = '';
foreach ($dnsresult as $record) {
	if (!isset($avoid_cname_duplicated[$record->name])) {
		$last_subdomain = $record->name;
		echo '<tr>
				<td scope="col"><code>' . $record->name . '</code>
					<div class="d-block d-md-none">' . _('指向') . ' <code>' . $record->name . '.cdn.cloudflare.net</code></div>
				</td>
				<td class="d-none d-md-table-cell"><code>' . $record->name . '.cdn.cloudflare.net</code></td>
				</tr>';
		$avoid_cname_duplicated[$record->name] = true;
	}
}
?>
	</tbody>
</table><?php

if ($no_record_yet) {
	echo '<div class="alert alert-warning" role="alert">' . _('该域名还没有任何记录, 请添加！') . '</div>';
}

if (isset($dnsresult_data->result_info->total_pages)) {
	$previous_page = '';
	$next_page = '';
	if ($dnsresult_data->result_info->page < $dnsresult_data->result_info->total_pages) {
		$page_link = $dnsresult_data->result_info->page + 1;
		$next_page = ' | <a href="?action=zone&domain=' . $zone_name . '&page=' . $page_link . '">' . _('下一页') . '</a>';
	}
	if ($dnsresult_data->result_info->page > 1) {
		$page_link = $dnsresult_data->result_info->page - 1;
		$previous_page = '<a href="?action=zone&domain=' . $zone_name . '&page=' . $page_link . '">' . _('上一页') . '</a> | ';
	}
	echo '<p>' . $previous_page . _('页码') . ' ' . $dnsresult_data->result_info->page . '/' . $dnsresult_data->result_info->total_pages . $next_page . '</p>';
}

$resp_cache = apcu_fetch('tlo_cf_'.$zone_name);
if ($last_subdomain != '' && !$resp_cache) {
	try {
		$resolver = new Net_DNS2_Resolver(array('nameservers' => array('173.245.59.31', '2400:cb00:2049:1::adf5:3b1f')));
		$resp = $resolver->query($zone_name, 'NS');
		$resp_a = $resolver->query($last_subdomain . '.cdn.cloudflare.net', 'A');
		$resp_aaaa = $resolver->query($last_subdomain . '.cdn.cloudflare.net', 'AAAA');
	} catch (Net_DNS2_Exception $e) {
		// echo $e->getMessage();
	}
} else {
	$resp = $resp_cache['ns'];
	$resp_a = $resp_cache['a'];
	$resp_aaaa = $resp_cache['aaaa'];
}

if ($last_subdomain != '' && (isset($resp_a->answer[0]->address) && isset($resp_a->answer[1]->address)) ||
	(isset($resp_aaaa->answer[0]->address) && isset($resp_aaaa->answer[1]->address))) {
	?>
	<h3 class="mt-5 mb-3" id="ip"><?php echo _('IP 接入'); ?></h3>
	<?php if (isset($resp_a->answer[0]->address) && isset($resp_a->answer[1]->address)) { ?>
		<h4>Anycast IPv4</h4>
		<ul>
			<li><code><?php echo $resp_a->answer[0]->address; ?></code></li>
			<li><code><?php echo $resp_a->answer[1]->address; ?></code></li>
		</ul>
	<?php } if (isset($resp_aaaa->answer[0]->address) && isset($resp_aaaa->answer[1]->address)) { ?>
		<h4>Anycast IPv6</h4>
		<ul>
			<li><code><?php echo $resp_aaaa->answer[0]->address; ?></code></li>
			<li><code><?php echo $resp_aaaa->answer[1]->address; ?></code></li>
		</ul>
	<?php }
}

if ($last_subdomain != '' && isset($resp->answer[0]->nsdname) && isset($resp->answer[1]->nsdname)) {
	apcu_store('tlo_cf_'.$zone_name, [
		'a' => $resp_a,
		'aaaa' => $resp_aaaa,
		'ns' => $resp,
	], 172800); // Two days
	?>
<h3 class="mt-5 mb-3" id="ns"><?php echo _('NS 接入'); ?></h3>
<table class="table table-striped">
	<thead>
		<tr>
			<th scope="col"><?php echo _('主机名'); ?></th>
			<th class="d-none d-md-table-cell">NS</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><code><?php echo $zone_name; ?></code>
				<div class="d-block d-md-none"><?php echo _('指向') . ' <code>' . $resp->answer[0]->nsdname . '</code>' ?></div>
			</td>
			<td class="d-none d-md-table-cell"><code><?php echo $resp->answer[0]->nsdname; ?></code></td>
		</tr>
		<tr>
			<td><code><?php echo $zone_name; ?></code>
				<div class="d-block d-md-none"><?php echo _('指向') . ' <code>' . $resp->answer[1]->nsdname . '</code>' ?></div>
			</td>
			<td class="d-none d-md-table-cell"><code><?php echo $resp->answer[1]->nsdname; ?></code></td>
		</tr>
	</tbody>
</table>
<?php }?>

<hr>
<h3 class="mt-5 mb-3"><a href="https://dash.cloudflare.com/" target="_blank"><?php echo _('更多设置'); ?></a></h3>
<p><?php echo _('本站只提供官网之外的配置。更多设置，如 Page Rules、SSL 回源配置、防火墙、缓存"
"配置等，请使用相同账号登录 CloudFlare.com 查看。'); ?><a href="https://dash.cloudflare.com/" target="_blank"><?php echo _('更多设置'); ?></a></p>
