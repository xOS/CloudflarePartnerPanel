<?php
/*
 * login2.php should be used instead of login.php, when there is no available Host API key.
 */

if (!isset($version)) {exit;}

if (isset($msg) && $msg != '') {echo '<div class="alert alert-warning" role="alert">' . $msg . '</div>';}
?>
<h1 class="login-h1 text-center"><?php echo _('CloudFlare 域名 CNAME/IP/NS 接入'); ?></h1>
<?php if($no_api_key){
	if(isset($tlo_promotion_header)){
		echo $tlo_promotion_header;
	} else {
		echo '<div class="alert alert-warning" role="alert">' . _('没有找到 Host API key。请通过 Global API Key 登录使用') . '</div>';
	}
} ?>
<form class="form-signin text-center" method="POST" action="">
	<h1 class="h3 mb-3 font-weight-normal"><?php echo _('请先登录'); ?></h1>
	<label for="inputEmail" class="sr-only"><?php echo _('你的 CloudFlare 邮箱'); ?></label>
	<input type="email" name="cloudflare_email" id="inputEmail" class="form-control" placeholder="<?php echo _('你的 CloudFlare 邮箱'); ?>" required autofocus>
	<label for="inputPassword" class="sr-only"><?php echo _('请输入 Global API Key，而不是你的账户密码。'); ?></label>
	<input type="password" name="cloudflare_api" id="inputPassword" class="form-control" minlength="37" maxlength="37" pattern="[0-9a-fA-F]{37}"
		   title="<?php echo _('请输入 Global API Key，而不是你的账户密码。');?>" placeholder="<?php echo _('请输入 Global API Key，而不是你的账户密码。'); ?>" required>
	<div class="checkbox mb-3">
		<label>
			<input type="checkbox" value="remember-me" name="remember"> <?php echo _('记住密码'); ?>
		</label>
	</div>
	<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo _('登录'); ?></button>
	<p class="mt-3 text-muted"><a href="https://support.cloudflare.com/hc/en-us/articles/200167836-Managing-API-Tokens-and-Keys#12345682"><?php echo _('如何获取我的 Global API Key ？'); ?></a></p>
	<p class="text-muted"><?php echo _('我们不会存储你的任何 CloudFlare 数据'); ?></p>
</form>
<?php if($no_api_key && isset($tlo_promotion_footer)){
	echo $tlo_promotion_footer;
} ?>
