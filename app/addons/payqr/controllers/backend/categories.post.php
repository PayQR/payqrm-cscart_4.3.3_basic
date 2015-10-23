<?php
if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
if ($mode == 'update')
{
	//установка id категории по классификатору PayQR
    if ($_REQUEST['category_id']) db_query('update ?:categories set payqr_category_id = ?s where category_id = ?i', $_REQUEST['payqr_category_id'], $_REQUEST['category_id']);
}
}
