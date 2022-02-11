<?php
$ttl_translate = [
	1 => _('自动'),
	120 => _('2 分钟'),
	300 => _('5 分钟'),
	600 => _('10 分钟'),
	900 => _('15 分钟'),
	1800 => _('30 分钟'),
	3600 => _('1 小时'),
	7200 => _('2 小时'),
	18000 => _('5 小时'),
	43200 => _('12 小时'),
	86400 => _('1 天'),
];
$status_translate = [
	'active' => '<span class="badge badge-success">' . _('已激活') . '</span>',
	'pending' => '<span class="badge badge-warning">' . _('处理中') . '</span>',
	'initializing' => '<span class="badge badge-light">' . _('初始化中') . '</span>',
	'moved' => '<span class="badge badge-dark">' . _('已移动') . '</span>',
	'deleted' => '<span class="badge badge-danger">' . ('已删除') . '</span>',
	'deactivated' => '<span class="badge badge-light">' . _('未激活') . '</span>',
];
$action_name = [
	'logout' => _('注销'),
	'security' => _('安全'),
	'add_record' => _('添加记录'),
	'edit_record' => _('编辑记录'),
	'delete_record' => _('删除记录'),
	'analytics' => _('统计分析'),
	'add' => _('添加域名'),
	'zone' => _('管理主域'),
	'dnssec' => _('DNSSEC'),
	'login' => _('登录'),
];
