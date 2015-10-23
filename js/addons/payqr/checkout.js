var product_data;
$(document).ready(function() {
    product_data = jQuery.parseJSON($(".payqr-button").attr('data-cart'));
    user_data = jQuery.parseJSON($(".payqr-button").attr('data-userdata'));
});

$(document).on('mousedown','.payqr-button', function(){
    var ordergroup = {};
    ordergroup['product_options'] = {};
    var product_options = {};
    product_data.forEach(function(item, i, arr)
    {
        var cart_id = user_data[item.article].cart_id;
        var product_id = user_data[item.article].product_id;
        var name = 'cart_products[' + cart_id + '][product_options]';
        $("[name^='" + name + "']").each(function ()
        {
            var option_id = this.id.split('_');
            product_options[option_id[2]] = this.value;
        });
        ordergroup['product_options'][product_id] = product_options;
    });
    ordergroup['user_id'] = user_id;

    $(".payqr-button").attr('data-ordergroup', JSON.stringify(ordergroup));
    $(".payqr-button").attr('data-cart', JSON.stringify(product_data));
});