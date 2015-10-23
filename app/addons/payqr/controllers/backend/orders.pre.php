<?php
if (!defined('BOOTSTRAP')) { die('Access denied'); }
use Tygh\Registry;
$payqr_invoice_obj = new payqr_invoice_action();

if ($mode == 'details')
{
	//debug
    //$info = fn_get_payqr_order_info($_REQUEST['order_id']);
    //fn_print_r($info);
}

//запросы payqr при изменении статуса заказа
if ($mode == 'update_status')
{
    $order_id = $_REQUEST['id'];
    $order_info = fn_get_order_short_info($order_id);
    $old_status = $order_info['status'];
    $new_status = $_REQUEST['status'];

    $payqr_invoice = fn_get_payqr_invoice($order_id);
    $payqr_order_info = fn_get_payqr_order_info($order_id);

    $paid_statuses = array('C', 'P', 'X', 'W');
    $unpaid_statuses = array('O', 'F', 'D', 'B', 'I');
    $cancel_statuses = array('B', 'D', 'F', 'I');
    $complete_statuses = array('C', 'P', 'W', 'X');

    if ($payqr_invoice)
    {
        if (in_array($new_status, $cancel_statuses) && $payqr_order_info->status == 'new')
        {
            $payqr_invoice_obj->invoice_cancel($payqr_invoice);
        }

        if (in_array($new_status, $complete_statuses) && $payqr_order_info->status == 'paid')
        {
            $payqr_invoice_obj->invoice_execution_confirm($payqr_invoice);
        }
    }
}

//запуск расчетов
if ($mode == 'payqr_confirm')
{
    if ($payqr_invoice_obj->invoice_confirm(fn_get_payqr_invoice($_REQUEST['order_id']))) fn_set_notification('N', 'OK', 'Расчеты успешно запущены');
    else fn_set_notification('E', 'Ошибка', 'Ошибка запуска расчетов');
    exit;
}

//подтверждение заказа
if ($mode == 'payqr_execution_confirm')
{
    if ($payqr_invoice_obj->invoice_execution_confirm(fn_get_payqr_invoice($_REQUEST['order_id']))) fn_set_notification('N', 'OK', 'Заказ успешно подтвержден');
    else fn_set_notification('E', 'Ошибка', 'Ошибка подтверждения заказа');
    exit;
}

//дослать/изменить сообщение
if ($mode == 'payqr_message')
{
    $order_id = explode('.', $_REQUEST['dispatch']);
    if ($payqr_invoice_obj->invoice_message(fn_get_payqr_invoice($order_id[2]), $_REQUEST['payqr_data'][$order_id[2]]['text'],$_REQUEST['payqr_data'][$order_id[2]]['img'], $_REQUEST['payqr_data'][$order_id[2]]['web'])) fn_set_notification('N', 'Сообщение изменено', 'Сообщение успешно изменено');
    else fn_set_notification('E', 'Ошибка', 'Ошибка изменения сообщения');
}

//синхронизация с PayQR
if ($mode == 'payqr_update')
{
    $info = fn_get_payqr_order_info($_REQUEST['order_id']);
    //пилим массив артикулов из объекта payqr
    foreach($info->cart as $product)
    {
        $payqr_products []= $product->article;
    }

    //удаляем лишние продукты из цмс
    $order_info = fn_get_order_info($_REQUEST['order_id']);
    foreach($order_info['products'] as $cart_id => $product)
    {
        if (!in_array($product['product_code'], $payqr_products)) db_query('delete from ?:order_details where item_id = ?i', $cart_id);
    }
    if ($order_info['total'] != $info->amount) db_query('update ?:orders set total = ?i where order_id = ?i', $info->amount, $_REQUEST['order_id']);
    //fn_print_die($order_info);
    fn_set_notification('N', 'Выполнено', 'Синхронизация успешно завершена');

    exit;
}

//возврат
if ($mode == 'payqr_revert')
{
    $order_id = explode('.', $_REQUEST['dispatch']);
    $payqr_order_info = fn_get_payqr_order_info($order_id[2]);
    if (is_numeric($_REQUEST['payqr_data'][$order_id[2]]['revert_amount']) && $_REQUEST['payqr_data'][$order_id[2]]['revert_amount'] > 0
        //&& $_REQUEST['payqr_data'][$order_id[2]]['revert_amount'] <= $payqr_order_info->revertAmount
        )
    {
        $revert = $payqr_invoice_obj->invoice_revert(fn_get_payqr_invoice($order_id[2]), $_REQUEST['payqr_data'][$order_id[2]]['revert_amount']);
        $revert = json_decode($revert);
        if ($revert->id)
        {
            fn_payqr_set_revert($order_id[2], $revert->revertId);
            fn_set_notification('N', 'Успешно', 'Возврат успешно выполнен');
        }
        else fn_set_notification('E', 'Ошибка', 'Ошибка выполнения возврата');
    }
    else fn_set_notification('E', 'Ошибка', 'Введено некорректное значение суммы возврата');
}

//обновить список возвратов
if ($mode == 'update_reverts')
{
    $reverts = fn_get_payqr_reverts($_REQUEST['order_id']);
    $order_info = fn_get_order_info($_REQUEST['order_id']);
    Registry::get('view')->assign('reverts', $reverts);
    Registry::get('view')->assign('o', $order_info);
    Registry::get('view')->assign('order_id', $_REQUEST['order_id']);
    Registry::get('view')->display('addons/payqr/components/payqr_show_reverts.tpl');
    exit();
}

//отмена заказа
if ($mode == 'payqr_cancel')
{
    $result = $payqr_invoice_obj->invoice_cancel(fn_get_payqr_invoice($_REQUEST['order_id']));
    if ($result->id)
    {
        fn_set_notification('N', 'Успешно', 'Счет успешно аннулирован');
    }
    else fn_set_notification('E', 'Ошибка', 'Ошибка анулирования счета');
}
