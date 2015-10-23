<div id="revert_list_{$o.order_id}">
    {if !$reverts}{assign var="reverts" value=$o.order_id|fn_get_payqr_reverts}{/if}

    <div class="payqr-table">
        <div class="payqr-row">
            <div class="payqr-cell"><b>Дата совершения</b></div>
            <div class="payqr-cell"><b>Сумма счета до возврата</b></div>
            <div class="payqr-cell"><b>Сумма возврата</b></div>
            <div class="payqr-cell"><b>Сумма счета после возврата</b></div>
        </div>
        {assign var="before" value=$o.total}
        {foreach from=$reverts item="revert"}
            {assign var="date" value=$revert.created|strtotime}
            <div class="payqr-row">
                <div class="payqr-cell">{"d.m.Y H:i:s"|date:$date}</div>
                <div class="payqr-cell">{include file="common/price.tpl" value=$before}</div>
                <div class="payqr-cell">{include file="common/price.tpl" value=$revert.amount}</div>
                {assign var="after" value=$before - $revert.amount}
                <div class="payqr-cell">{include file="common/price.tpl" value=$after}</div>
                {assign var="before" value=$after}
            </div>
        {/foreach}
    </div>
    <br>
    {include file="buttons/button.tpl" but_role="button" but_onclick="update_reverts_{$o.order_id}()" but_text="Обновить"}

    <script>
        function update_reverts_{$o.order_id}()
        {
            var url = fn_url('orders.update_reverts?order_id={$o.order_id}');
            Tygh.$.ceAjax('request', url, {ldelim}
                result_ids: 'revert_list_{$o.order_id}',
                skip_result_ids_check: true
            {rdelim});
        }
    </script>
<!--revert_list_{$o.order_id}--></div>