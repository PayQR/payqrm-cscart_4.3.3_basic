var product_data;
$(document).ready(function() {
    product_data = jQuery.parseJSON($(".payqr-button").attr('data-cart')); //получаем данные продукта из кнопки PayQR
});

//устанавливаем в кнопку PayQR выбранные опции продукта
$(document).on('mousedown','.payqr-button', function(){
    var ordergroup = {};
    ordergroup['product_options'] = {};
    var product_options = {};
    product_data[0]['quantity'] = $(".cm-amount").val();
    var name = 'product_data[' + product_data[0]['product_id'] + '][product_options]';
    $("[name^='" + name + "']").each(function ()
    {
        var option_id = this.id.split('_');
        product_options[option_id[2]] = this.value;
    });
    ordergroup['product_options'][product_data[0]['product_id']] = product_options;
    ordergroup['user_id'] = user_id;
    $(".payqr-button").attr('data-ordergroup', JSON.stringify(ordergroup));
    $(".payqr-button").attr('data-cart', JSON.stringify(product_data));
    $(".payqr-button").attr('data-amount', product_data[0]['quantity'] * product_data[0]['amount']);
});
