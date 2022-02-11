<?php
/*
 * Login page.
 */

if (!isset($version)) {exit;}

if (isset($msg) && $msg != '') {echo '<div class="alert alert-warning" role="alert">' . $msg . '</div>';}
?>
<h1 class="login-h1 text-center"><?php echo _('CloudFlare 域名 CNAME/IP/NS 接入'); ?></h1>
<form class="form-signin text-center" method="POST" action="">
	<h1 class="h3 mb-3 font-weight-normal"><?php echo _('请先登录'); ?></h1>
	<label for="inputEmail" class="sr-only"><?php echo _('你的 CloudFlare 邮箱'); ?></label>
	<input type="email" name="cloudflare_email" id="inputEmail" class="form-control" placeholder="<?php echo _('你的 CloudFlare 邮箱'); ?>" required autofocus>
	<label for="inputPassword" class="sr-only"><?php echo _('你的 CloudFlare 密码'); ?></label>
	<input type="password" name="cloudflare_pass" id="inputPassword" class="form-control" placeholder="<?php echo _('你的 CloudFlare 密码'); ?>" required>
	<div class="checkbox mb-3">
		<label>
			<input type="checkbox" value="remember-me" name="remember"> <?php echo _('记住密码'); ?>
		</label>
	</div>
	<button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo _('登录'); ?></button>
	<p class="mt-3 text-muted"><?php echo _('在这里登录你已有的账号或者注册新账号。'); ?></p>
	<p class="text-muted"><?php echo _('我们不会存储你的任何 CloudFlare 数据'); ?></p>
</form>
