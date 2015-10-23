<?php
use Tygh\Registry;
if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_payqr_get_url_input()
{
    return Registry::get('view')->fetch('addons/payqr/settings/url.tpl');
}

function fn_get_payqr_button($product_data = false)
{
    require_once 'lib/payqr_config.php'; // подключаем основной класс
    $config = Registry::get('addons.payqr'); //получаем настройки payqr

    //check, if the button is required here
    $_arr = array(
        'categories.view' => 'categories_button',
        'products.view' => 'product_details_button',
        'checkout.cart' => 'checkout_button'
    );
    if ($config[$_arr[$_REQUEST['dispatch']]] != 'Y') return;

    if ($product_data)
    {
        $amount = $product_data['price'];
        $user_data = array(
            $product_data['product_code'] => array(
                'product_id' => $product_data['product_id']
            )
        );
        $products_array = array(
            '0' => array(
                'article' => $product_data['product_code'],
                'name' => str_replace("&quot;", '"', $product_data['product']),
                'category' => fn_get_payqr_category_id($product_data['main_category']),
                'quantity' => '1',
                'amount' => $product_data['price'],
                'imageurl' => $product_data['main_pair']['detailed']['image_path']
            )
        );
    }
    else
    {
        $amount = $_SESSION['cart']['total'];
        foreach($_SESSION['cart']['products'] as $cart_id => $product)
        {
            $user_data = array(
                $product['product_code'] => array(
                    'product_id' => $product['product_id'],
                    'cart_id' => $cart_id,
                    'product_options' => $product['product_options']
                )
            );
            $products_array []= array(
                'article' => $product['product_code'],
                'name' => str_replace("&quot;", '"', $product_data['product']),
                'category' => '01.00.00',
                'quantity' => $product['amount'],
                'amount' => $product['price'] * $product['amount'],
                'imageurl' => $product['main_pair']['detailed']['image_path']
            );
        }
    }
    $button = new payqr_button($amount, $products_array); // создаем объект кнопки

    // Данные для кнопки PayQR
    $button->promo_required = $config['promo_required']; // включает сбор промо-кодов или номеров карт лояльности
    $button->setPromoDescription($config['promo_text']); // описание поля для ввода промо-кода или номера карты лояльности

    $button->firstname_required = $config['name']; // включает запрос имени покупателя
    $button->lastname_required = $config['lastname']; // включает запрос фамилии покупателя
    $button->middlename_required = $config['middlename']; // включает запрос фамилии покупателя
    $button->phone_required = $config['phone']; // включает запрос телефона покупателя
    $button->email_required = 'required'; // включает запрос e-mail покупателя
    $button->delivery_required = $config['shipping_address']; // включает запрос адреса доставки покупателя
    $button->deliverycases_required = 'required'; // включает выбор покупателем способа доставки
    $button->pickpoints_required = $config['pickpoints_required']; // включает выбор покупателем пункта самовывоза
    $ordergroup = json_encode(array('user_id' => $_SESSION['auth']['user_id']));
    $button->setOrderGroup($ordergroup);

    if ($config['order_message'] == 'Y')
    {
        $button->setMessageText($config['order_message_text']); // устанавливает текст сообщения от продавца к покупкам, совершаемым через PayQR
        $button->setMessageImageUrl($config['order_message_image_url']); // устанавливает изображение к тексту сообщения от продавца к покупкам, совершаемым через PayQR
        $button->setMessageUrl($config['order_message_website_image_url']); // устаналивает адрес, куда покупатель будет перенаправляться при нажатии на сообщение от продавца к покупкам, совершаемым через PayQR
    }

    $button->setUserData(json_encode($user_data)); // устанавливает userdata (любые дополнительные служебные/аналитические данные в свободном формате)

// Изменение стиля кнопки PayQR
    $controller = Registry::get('runtime.controller');
    if ($controller == 'products') $setting_prefix = 'product_button_';
    if ($controller == 'categories') $setting_prefix = 'categories_button_';
    if ($config[$setting_prefix . 'custom_button'] == 'Y')
    {
        $button->setColor($config[$setting_prefix . 'color']); // изменяет цвет кнопки PayQR (доступные варианты в ключах массива payqr_button::color)
        $button->setBorderRadius($config[$setting_prefix . 'border_radius']); // изменяет границы кнопки PayQR (доступные варианты в ключах массива payqr_button::borderRadius)
        $button->setFontWeight($config[$setting_prefix . 'text_weight']); // изменяет стиль текста в кнопке PayQR (доступные варианты в ключах массива payqr_button::fontWeight)
        $button->setFontSize($config[$setting_prefix . 'text_size']); // изменяет размер текста в кнопке PayQR (доступные варианты в ключах массива payqr_button::fontSize)
        $button->setGradient($config[$setting_prefix . 'gradient']); // изменяет градиент в кнопке PayQR (доступные вариант в ключах массива payqr_button::gradient)
        $button->setShadow($config[$setting_prefix . 'shadow']); // изменяет тень на кнопке PayQR (доступные вариант в ключах массива payqr_button::shadow)
        $button->setTextTransform($config[$setting_prefix . 'text_transform']);

// Размеры кнопки PayQR
        $button->setHeight($config[$setting_prefix . 'text_height']); // изменяет высоту кнопки PayQR (px)
        $button->setWidth($config[$setting_prefix . 'text_width']); // изменяет ширину кнопки PayQR (px)
    }

    return $button->getHtmlButton();
}

function fn_install_payqr()
{
    $payment_id = db_query("insert into ?:payments set ('status') values ('D')");
    db_query("insert into ?:payment_descriptions set ('payment_id', 'payment') values (?i, ?s)", $payment_id, 'PayQR');

    if (($handle = fopen("app/addons/payqr/lib/payqr_classifier.csv", "r")) !== FALSE) {
        db_query('TRUNCATE ?:payqr_categories');
        $i=0;
        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            $i++;
            if ($i == 1) continue;
            $data = str_getcsv($data[0], ';');
            $category_data['category_id'] = $data[0];
            $category_data['description'] = $data[1];
            $categories []= $category_data;
        }
        fclose($handle);

        db_query('insert into ?:payqr_categories ?m', $categories);
    }
}

function fn_uninstall_payqr()
{
    $payment_id = fn_get_payqr_id();
    db_query('delete from ?:payments where payment_id = ?i', $payment_id);
    db_query('delete from ?:payment_descriptions where payment_id = ?i', $payment_id);
}

function fn_get_payqr_id()
{
    return db_get_field('select payment_id from ?:payment_descriptions where payment = ?s', 'PayQR');
}

function fn_objectToArray($d) {
    if (is_object($d)) {
        // Gets the properties of the given object
        // with get_object_vars function
        $d = get_object_vars($d);
    }

    if (is_array($d)) {
        /*
        * Return array converted to object
        * Using __FUNCTION__ (Magic constant)
        * for recursive call
        */
        return array_map(__FUNCTION__, $d);
    }
    else {
        // Return array
        return $d;
    }
}

function fn_get_payqr_invoice($order_id)
{
    return db_get_field('select payqr_invoice from ?:orders where order_id = ?i', $order_id);
}

function fn_get_payqr_reverts($order_id)
{
    $reverts = db_get_field('select payqr_reverts from ?:orders where order_id = ?i', $order_id);
    $reverts = explode(';', $reverts);
    foreach($reverts as $revert)
    {
        if ($revert)
        {
            $revert = fn_get_payqr_revert_details($revert);
            $revert_list[strtotime($revert['created'])] = $revert;
        }
    }

    ksort($revert_list);
    return $revert_list;
}

function fn_get_payqr_revert_details($revert_id)
{
    $payqr_revert = new payqr_revert_action();
    return fn_objectToArray(json_decode($payqr_revert->get_revert($revert_id)));
}

function fn_payqr_set_revert($order_id, $revert_id)
{
    $reverts = db_get_field('select payqr_reverts from ?:orders where order_id = ?i', $order_id);
    db_query('update ?:orders set payqr_reverts = ?s where order_id = ?i', $reverts . ';' . $revert_id , $order_id);
}

function fn_get_payqr_order_info($order_id)
{
    $payqr_invoice_obj = new payqr_invoice_action();
    $info = json_decode($payqr_invoice_obj->get_invoice(fn_get_payqr_invoice($order_id)));

    return $info;
}

function fn_get_payqr_order_actions($order_id)
{
    $payqr_order_info = fn_get_payqr_order_info($order_id);

    $actions = array('payqr_update');
    switch ($payqr_order_info->status)
    {
        case 'new':
            $actions []= 'payqr_cancel';
            break;
        case 'paid':
            $actions []= 'payqr_revert';
            $actions []= 'payqr_confirm';
            $actions []= 'payqr_execution_confirm';
            $actions []= 'payqr_message';
            break;
        case 'revertedPartially':
            $actions []= 'payqr_revert';
            $actions []= 'payqr_confirm';
            $actions []= 'payqr_execution_confirm';
            $actions []= 'payqr_message';
            $actions []= 'payqr_show_reverts';
            break;
        case 'reverted':
            $actions []= 'payqr_confirm';
            $actions []= 'payqr_message';
            $actions []= 'payqr_show_reverts';
            break;
        case 'none':
            $actions []= 'payqr_confirm';
            $actions []= 'payqr_execution_confirm';
            break;
    }
    if (time() - strtotime($payqr_order_info->created) <= 259200 * 60) $actions []= 'payqr_message';

    return array_unique($actions);
}

function fn_get_payqr_order_actions_popup_types()
{
    return array(
        'payqr_cancel' => 'alert',
        'payqr_update' => 'alert',
        'payqr_revert' => 'popup',
        'payqr_confirm' => 'alert',
        'payqr_execution_confirm' => 'alert',
        'payqr_message' => 'popup',
        'payqr_show_reverts' => 'popup'
    );
}

function fn_get_payqr_type_lang_var($payqr_type)
{
    return __("confirm_" . $payqr_type);
}

function fn_get_payqr_categories()
{
    return db_get_array('select * from ?:payqr_categories');
}

function fn_get_payqr_category_id($category_id)
{
    return db_get_field('select payqr_category_id from ?:categories where category_id = ?i', $category_id);
}