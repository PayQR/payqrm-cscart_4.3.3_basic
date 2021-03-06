<?php
/**
 * Код в этом файле будет выполнен, когда интернет-сайт получит уведомление от PayQR о необходимости предоставить покупателю способы доставки конкретного заказа.
 * Это означает, что интернет-сайт на уровне кнопки PayQR активировал этап выбора способа доставки покупателем, и сейчас покупатель дошел до этого этапа.
 *
 * $Payqr->objectOrder содержит объект "Счет на оплату" (подробнее об объекте "Счет на оплату" на https://payqr.ru/api/ecommerce#invoice_object)
 *
 * Ниже можно вызвать функции своей учетной системы, чтобы особым образом отреагировать на уведомление от PayQR о событии invoice.deliverycases.updating.
 *
 * Важно: на уведомление от PayQR о событии invoice.deliverycases.updating нельзя реагировать как на уведомление о создании заказа, так как иногда оно будет поступать не от покупателей, а от PayQR для тестирования доступности функционала у конкретного интернет-сайта, т.е. оно никак не связано с реальным формированием заказов. Также важно, что в ответ на invoice.deliverycases.updating интернет-сайт может передать в PayQR только содержимое параметра deliveryCases объекта "Счет на оплату". Передаваемый в PayQR от интернет-сайта список способов доставки может быть многоуровневым.
 *
 * Пример массива способов доставки:
 * $delivery_cases = array(
 *          array(
 *              'article' => '2001',
 *               'number' => '1.1',
 *               'name' => 'DHL',
 *               'description' => '1-2 дня',
 *               'amountFrom' => '0',
 *               'amountTo' => '70',
 *              ),
 *          .....
 *  );
 * $Payqr->objectOrder->setDeliveryCases($delivery_cases);
 */

//получаем данные пользователя и адрес доставки
$auth = $_SESSION['auth'];
$payqr_cart = $Payqr->objectOrder->getCart();
$customer_data = $Payqr->objectOrder->getCustomer();
$delivery_address = $Payqr->objectOrder->getDelivery();
$auth['user_id'] = $payqr_user_data['user_id'] ? $payqr_user_data['user_id'] : db_get_field('select user_id from ?:users where email = ?s', $customer_data->email);

// если пользователь не зарегистирован или его данные не обновлены, регистрируем его/обновляем данные
if (!$payqr_user_data['user_created'] && !$auth['user_id'])
{
	// устанавливаем данные пользователя для регистрации
    if ($customer_data->email) $user_data['email'] = $customer_data->email;
    if ($customer_data->firstName) $user_data['b_firstname'] = $customer_data->firstName;
    if ($customer_data->lastName) $user_data['b_lastname'] = $customer_data->lastName;
    if ($customer_data->phone) $user_data['b_phone'] = $customer_data->phone;
    if ($delivery_address->street) $user_data['b_address'] = $user_data['s_address'] = 'Улица: ' . $delivery_address->street . ', дом: ' . $delivery_address->house . ', корпус:' . $delivery_address->unit . ', строение: ' . $delivery_address->building . ', подъезд:' . $delivery_address->hallway . ', этаж: ' . $delivery_address->floor . ', кв: ' . $delivery_address->flat;
    if ($delivery_address->city) $user_data['b_city'] = $user_data['s_city'] = $delivery_address->city;
    $user_data['b_country'] = $user_data['s_country'] = "RU";
    if ($delivery_address->zip) $user_data['b_zipcode'] = $delivery_address->zip;
    $user_data['password1'] =  $user_data['password2'] = fn_generate_password();
    //$user_data = array_filter($user_data);

    list($user_id, $profile_id) = fn_update_user($auth['user_id'], $user_data, $auth, true, true, true); //создаем/обновляем пользователя

    $auth['user_id'] = $user_id;
    $payqr_user_data['user_created'] = true;
}

fn_clear_cart($cart); //очищаем корзину
//добавляем в нее продукты
foreach($payqr_cart as $product)
{
    $product_id = db_get_field('select product_id from ?:products where product_code = ?s', $product->article);
    $product_data = array(
        $product_id => array(
            'product_id' => $product_id,
            'amount' => $product->quantity
        )
    );
    //устанавливаем опции продукта
    if (!empty($payqr_user_data['product_options']))
    {
        $product_data[$product_id]['product_options'] = $payqr_user_data['product_options'][$product_id];
    }
    fn_add_product_to_cart($product_data, $cart, $auth); //само добавление в корзину
}
fn_save_cart_content($cart, $auth['user_id']); //сохраняем корзину
$cart['calculate_shipping'] = true; //устанавливаем флажок "посчитать стоимость доставки"

fn_calculate_cart_content($cart, $auth, 'A', true, 'F', true); //пересчитываем корзину

//генерируем массив способов доставки
foreach($cart['product_groups']as $product_group)
{
    foreach($product_group['shippings'] as $key => $shipping)
    {
        $delivery_cases []= array(
            'number' => $key,
            'article' => $shipping['shipping_id'],
            'name' => $shipping['shipping'],
            'description' => $shipping['delivery_time'],
            'amountFrom' => $shipping['rate'],
            'amountTo' => $shipping['rate'],
        );
    }
}
$payqr_user_data['user_id'] = $auth['user_id'];
$Payqr->objectOrder->setDeliveryCases($delivery_cases); //устанавливаем способы доставки в PayQR
$Payqr->objectEvent->data->orderGroup = json_encode($payqr_user_data); //устанавливаем данные пользователя в объект PayQR
