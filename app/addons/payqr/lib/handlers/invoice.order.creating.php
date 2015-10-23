<?php
/**
 * Код в этом файле будет выполнен, когда интернет-сайт получит уведомление от PayQR о необходимости создания заказа в учетной системе интернет-сайта.
 * Это означает, что покупатель приблизился к этапу оплаты, а, значит, интернет-сайту нужно создать заказ в своей учетной системе, если такой заказ еще не создан, и вернуть в ответе PayQR значение orderId в объекте "Счет на оплату", если orderId там еще отсутствует.
 *
 * $Payqr->objectOrder содержит объект "Счет на оплату" (подробнее об объекте "Счет на оплату" на https://payqr.ru/api/ecommerce#invoice_object)
 *
 * Ниже можно вызвать функции своей учетной системы, чтобы особым образом отреагировать на уведомление от PayQR о событии invoice.order.creating.
 *
 * Важно: после уведомления от PayQR об invoice.order.creating в содержании конкретного объекта "Счет на оплату" должен быть обязательно заполнен параметр orderId (если он не заполнялся на уровне кнопки PayQR). По правилам PayQR оплата заказа не может быть начата до тех пор, пока в счете не появится номер заказа (orderId). Если интернет-сайт не ведет учет заказов по orderId, то на данном этапе можно заполнить параметр orderId любым случайным значением, например, текущими датой и временем. Также важно, что invoice.order.creating является первым и последним этапом, когда интернет-сайт может внести коррективы в параметры заказа (например, откорректировать названия позиций заказа).
 *
 * Часто используемые методы на этапе invoice.order.creating:
 *
 * * Получаем объект адреса доставки из "Счета на оплату"
 * $Payqr->objectOrder->getDelivery();
 * * вернет:
 * "delivery": { "country": "Россия", "region": "Москва", "city": "Москва", "zip": "115093", "street": "Дубининская ул.", "house": "80", "comment": "У входа в автосалон Хонда", }
 *
 * * Получаем объект содержания корзины из "Счета на оплату"
 * $Payqr->objectOrder->getCart();
 * * вернет:
 * [{ "article": "5675657", "name": "Товар 1", "imageUrl": "http://goods.ru/item1.jpg", "quantity": 5, "amount": 19752.25 }, { "article": "0", "name": "PROMO акция", "imageUrl": "http://goods.ru/promo.jpg", }]
 *
 * * Обновляем содержимое корзины в объекте "Счет на оплату" в PayQR
 * $Payqr->objectOrder->setCart($cartObject);
 *
 * * Получаем объект информации о покупателе из "Счета на оплату"
 * $Payqr->objectOrder->getCustomer();
 * * вернет:
 * { "firstName": "Иван", "lastName": "Иванов", "phone": "+79111111111", "email": "test@user.com" }
 *
 * * Устанавливаем orderId из учетной системы интернет-сайта в объекте "Счет на оплату" в PayQR
 * $Payqr->objectOrder->setOrderId($orderId);
 *
 * * Получаем сумму заказа из "Счета на оплату"
 * $Payqr->objectOrder->getAmount();
 *
 * * Изменяем сумму заказа в объекте "Счет на оплату" в PayQR (например, уменьшаем сумму, чтобы применить скидку)
 * $Payqr->objectOrder->setAmount($amount);
 *
 * * Если по каким-то причинам нам нужно отменить этот заказ сейчас (работает только при обработке события invoice.order.creating)
 * $Payqr->objectOrder->cancelOrder(); вызов этого метода отменит заказ
 */

//получаем данные пользователя и адрес доставки
$auth = $_SESSION['auth'];
$auth['user_id'] = $payqr_user_data['user_id'];
$payqr_cart = $Payqr->objectOrder->getCart();
$customer_data = $Payqr->objectOrder->getCustomer();
$delivery_address = $Payqr->objectOrder->getDelivery();
$user_data = fn_get_user_info($auth['user_id']); //получаем данные пользователя

// устанавливаем данные пользователя для заказа
if ($customer_data->email) $new_user_data['email'] = $customer_data->email;
if ($customer_data->firstName) $new_user_data['b_firstname'] = $customer_data->firstName;
if ($customer_data->firstName) $new_user_data['firstname'] = $customer_data->firstName;
if ($customer_data->lastName) $new_user_data['lastname'] = $customer_data->lastName;
if ($customer_data->lastName) $new_user_data['b_lastname'] = $customer_data->lastName;
if ($customer_data->phone) $new_user_data['b_phone'] = $customer_data->phone;
if ($delivery_address->street) $new_user_data['b_address'] = 'Улица: ' . $delivery_address->street . ', дом: ' . $delivery_address->house . ', корпус:' . $delivery_address->unit . ', строение: ' . $delivery_address->building . ', подъезд:' . $delivery_address->hallway . ', этаж: ' . $delivery_address->floor . ', кв: ' . $delivery_address->flat;
if ($delivery_address->city) $new_user_data['b_city'] = $delivery_address->city;
if ($delivery_address->zip) $new_user_data['b_zipcode'] = $delivery_address->zip;

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

//добавляем купон к заказу
//$cart['pending_coupon'] = 'qwerty'; //сбор купонов в приложении еще не реализован
$cart['recalculate'] = true;

$shipping_id = $Payqr->objectOrder->data->deliveryCasesSelected->article; //устанавливаем способ доставки
fn_checkout_update_shipping($cart, array($shipping_id)); //обновляем доставку в корзине

if (!empty($cart['chosen_shipping'][0])) {
    $cart['calculate_shipping'] = true;
}

fn_save_cart_content($cart, $auth['user_id']); //сохраняем корзину
fn_calculate_cart_content($cart, $auth, 'S', true, 'F', true); //пересчитываем ее
$cart['payment_id'] = fn_get_payqr_id(); //устанавливаем айди платежки PayQR
$cart['user_data'] = $user_data; //и данные пользователя
$Payqr->objectOrder->setAmount($cart['total']); //устанавливаем сумму заказа на случай если она изменилась после пересчета

list($order_id, $process_payment) = fn_place_order($cart, $auth); //сохраняем заказ
db_query('update ?:orders set ?u where order_id = ?i', $new_user_data, $order_id);
//в случае ошибки отменяем заказ
if (!$order_id)
{
    $Payqr->objectOrder->cancelOrder();
}

if ($Payqr->objectEvent->data->object == 'invoice') db_query('update ?:orders set payqr_invoice = ?s where order_id = ?i', $Payqr->objectEvent->data->id, $order_id); //устанавливаем айди инвойса к заказу в БД
$Payqr->objectOrder->setOrderId($order_id); //устанавливаем айди заказа в объект PayQR
$Payqr->objectEvent->data->orderGroup = json_encode($payqr_user_data); //устанавливаем данные пользователя в объект PayQR
