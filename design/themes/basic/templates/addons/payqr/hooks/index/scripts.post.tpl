{if $runtime.controller == 'products' && $runtime.mode == 'view'}
    {script src="js/addons/payqr/products.js"}
{/if}

{if $runtime.controller == 'checkout' && $runtime.mode == 'cart'}
    {script src="js/addons/payqr/checkout.js"}
{/if}

<script>
    var user_id = {$smarty.session.auth.user_id};
    var current_location = '{$config.http_host}{$config.http_path}'
</script>

{script src="js/addons/payqr/scripts.js"}