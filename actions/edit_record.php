 <?php
/*
 * Edit a record.
 */

if (!isset($adapter)) {exit;}
?>
<strong><?php echo '<h1 class="h5"><a href="?action=zone&amp;domain=' . $_GET['domain'] . '&amp;zoneid=' . $_GET['zoneid'] . '">&lt;- ' . _('返回') . '</a></h1>'; ?></strong><hr>
<?php
$dns = new \Cloudflare\API\Endpoints\DNS($adapter);
$dns_details = $dns->getRecordDetails($_GET['zoneid'], $_GET['recordid']);
if (isset($_POST['submit'])) {
	if (isset($_POST['proxied']) && $_POST['proxied'] == 'true') {
		$_POST['proxied'] = true;
	} else {
		$_POST['proxied'] = false;
	}
	$_POST['ttl'] = intval($_POST['ttl']);
	$_POST['type'] = $dns_details->type;
	if (isset($_POST['priority']) && $_POST['type'] == 'MX') {
		$_POST['priority'] = intval($_POST['priority']);
	} else {
		$_POST['priority'] = 10;
	}
	if (!isset($_POST['content'])) {
		$_POST['content'] = "";
	}

	$options = [
		'type' => $dns_details->type,
		'name' => $_POST['name'],
		'content' => $_POST['content'],
		'ttl' => intval($_POST['ttl']),
		'priority' => $_POST['priority'],
		'proxied' => $_POST['proxied']
	];

	include "record_data.php";

	try {
		if ($dns->updateRecordDetails($_GET['zoneid'], $_GET['recordid'], $options)) {
			exit('<p class="alert alert-success" role="alert">' . _('成功') . '</p>');
		} else {
			echo '<p class="alert alert-danger" role="alert">' . _('失败') . '</p>';
		}
	} catch (Exception $e) {
		echo '<p class="alert alert-danger" role="alert">' . _('失败') . '</p>';
		echo '<div class="alert alert-warning" role="alert">' . $e->getMessage() . '</div>';
	}
}
if (isset($msg)) {echo $msg;}
?>
<form method="POST" action="">
	<fieldset>
		<legend><?php echo _('编辑 DNS 记录'); ?></legend>
		<div class="form-group">
			<label for="name"><?php echo _('记录名 (例 “@”, “www”, etc.)'); ?></label>
			<input type="text" name="name" id="name" value="<?php echo htmlspecialchars($dns_details->name); ?>" class="form-control">
		</div>
		<div class="form-group">
			<label for="type"><?php echo _('记录类型'); ?></label>
			<select name="type" id="type" disabled="disabled" class="form-control">
				<option value="<?php echo $dns_details->type; ?>"><?php echo $dns_details->type; ?></option>
			</select>
		</div>

		<?php if ($dns_details->type == 'CAA') {?>
			<div class="form-group">
				<label for="data_tag"><?php echo _('标签 (Tag)'); ?></label>
				<select name="data_tag" id="data_tag" class="form-control" data-selected="<?php echo $dns_details->data->tag ?>">
					<option value="issue"><?php echo _('仅允许特定主机名') ?></option>
					<option value="issuewild"><?php echo _('仅允许通配符') ?></option>
					<option value="iodef"><?php echo _('发送违规报告到 URL (http:, https:, 或 mailto:)') ?></option>
				</select>
			</div>
			<div class="form-group">
				<label for="data_value"><?php echo _('值 (Value)'); ?></label>
				<input type="text" name="data_value" id="data_value" value="<?php echo htmlspecialchars($dns_details->data->value); ?>" class="form-control">
			</div>
			<input type="hidden" name="data_flags" value="0">
		<?php } elseif ($dns_details->type == 'SRV') {?>
			<div class="form-group">
				<label for="srv_service"><?php echo _('服务 (Service)'); ?></label>
				<input type="text" name="srv_service" id="srv_service" value="<?php echo $dns_details->data->service ?>" class="form-control">
			</div>
			<div class="form-group">
				<label for="srv_proto"><?php echo _('协议 (Proto)'); ?></label>
				<select name="srv_proto" id="srv_proto" class="form-control" data-selected="<?php echo $dns_details->data->proto ?>">
					<option value="_tcp">TCP</option>
					<option value="_udp">UDP</option>
					<option value="_tls">TLS</option>
				</select>
			</div>
			<div class="form-group">
				<label for="srv_name"><?php echo _('主机名'); ?></label>
				<input type="text" name="srv_name" id="srv_name" value="<?php echo $dns_details->data->name ?>" class="form-control">
			</div>
			<div class="form-group">
				<label for="srv_priority"><?php echo _('权重 (Priority)'); ?></label>
				<input type="text" name="srv_priority" id="srv_priority" value="<?php echo $dns_details->data->priority ?>" class="form-control">
			</div>
			<div class="form-group">
				<label for="srv_weight"><?php echo _('权重 (Weight)'); ?></label>
				<input type="text" name="srv_weight" id="srv_weight" value="<?php echo $dns_details->data->weight ?>" class="form-control">
			</div>
			<div class="form-group">
				<label for="srv_port"><?php echo _('端口 (Port)'); ?></label>
				<input type="text" name="srv_port" id="srv_port" value="<?php echo $dns_details->data->port ?>" class="form-control">
			</div>
			<div class="form-group">
				<label for="srv_target"><?php echo _('目标 (Target)'); ?></label>
				<input type="text" name="srv_target" id="srv_target" value="<?php echo $dns_details->data->target ?>" class="form-control">
			</div>
		<?php } else {?>
		<div class="form-group">
			<label for="doc-ta-1"><?php echo _('记录内容'); ?></label>
			<textarea name="content" rows="5" id="doc-ta-1" class="form-control"><?php echo htmlspecialchars($dns_details->content); ?></textarea>
		</div>
			<?php if ($dns_details->type == 'MX' || $dns_details->type == 'SRV') {?>
				<div class="form-group">
					<label for="priority"><?php echo _('权重 (Priority)'); ?></label>
					<input type="number" name="priority" id="priority" step="1" min="1" value="<?php echo $dns_details->priority; ?>" class="form-control">
				</div>
			<?php }?>
		<?php }?>

		<div class="form-group">
			<label for="ttl">TTL</label>
			<select name="ttl" id="ttl" class="form-control">
				<?php
foreach ($ttl_translate as $_ttl => $_ttl_name) {
	echo '<option value="' . $_ttl . '">' . $_ttl_name . '</option>';
}
?>
			</select>
		</div>
		<?php if ($dns_details->proxiable) {?>
		<div class="form-group">
			<label for="proxied">CDN</label>
			<select name="proxied" id="proxied" class="form-control">
				<option value="true" <?php if ($dns_details->proxied) {echo 'selected="selected"';}?>><?php echo _('开启'); ?></option>
				<option value="false" <?php if (!$dns_details->proxied) {echo 'selected="selected"';}?>><?php echo _('关闭'); ?></option>
			</select>
		</div>
		<?php }?>

		<button type="submit" name="submit" class="btn btn-primary"><?php echo _('提交'); ?></button>
	</fieldset>
</form>
