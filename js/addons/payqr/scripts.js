$(function() {
    payQR.onPaid(function(data) {
        // data.orderGroup
        // data.orderId
        // data.amount
        // data.userData
        window.location.replace('index.php?dispatch=checkout.complete&order_id=' + data.orderId); //переходим на страницу успешного совершения заказа
    });
});
