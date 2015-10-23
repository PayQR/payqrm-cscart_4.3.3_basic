<?php
if (!defined('BOOTSTRAP')) { die('Access denied'); }
use Tygh\Registry;

require_once DIR_ROOT . '/app/addons/payqr/lib/payqr_config.php';
$config = Registry::get('addons.payqr');
//устанавливаем конфиг
payqr_config::init($config['merchid'], $config['keyin'], $config['keyout']);
if ($config['payqr_log'] == 'Y') payqr_config::setEnabledLog(true);
payqr_config::setLogFile('payqr.log');