<td>
    <div class="cm-popup-box dropleft dropdown ">
        <a id="sw_select_{$o.order_id}_wrap" class="dropdown-toggle" data-toggle="dropdown">
            <img src="images/payqr.jpg">
        </a>
        <ul class="dropdown-menu">
            {assign var="payqr_actions" value=$o.order_id|fn_get_payqr_order_actions}
            {assign var="payqr_actions_types" value=""|fn_get_payqr_order_actions_popup_types}
            {foreach from=$payqr_actions item="payqr_action"}
                <li>
                    {if $payqr_actions_types.$payqr_action == 'alert'}
                        <a class="status-link-b" onclick="confirm_{$o.order_id}_{$payqr_action}();">{__($payqr_action)}</a>
                        <script>
                            function confirm_{$o.order_id}_{$payqr_action}()
                            {
                                if (confirm("{$payqr_action|fn_get_payqr_type_lang_var}"))
                                {
                                    Tygh.$.ceAjax('request', 'admin.php?dispatch=orders.{$payqr_action}&order_id={$o.order_id}', {literal}{method: 'POST', cache: false}{/literal});
                                }
                            }
                        </script>
                    {/if}
                    {if $payqr_actions_types.$payqr_action == 'popup'}
                        {assign var="capture_name" value=$payqr_action|cat:"_":$o.order_id}
                        <a class="cm-dialog-opener cm-dialog-auto-size" data-ca-target-id="{$capture_name}">{__($payqr_action)}</a>
                    {/if}
                </li>
            {/foreach}
        </ul>
    </div>
</td>
{assign var="payqr_actions" value=$o.order_id|fn_get_payqr_order_actions}
{foreach from=$payqr_actions item="payqr_action"}
    {if $payqr_actions_types.$payqr_action == 'popup'}
        {assign var="capture_name" value=$payqr_action|cat:"_":$o.order_id}
        <div class="hidden" id="{$capture_name}" title="{__($payqr_action)}">
            {include file="addons/payqr/components/"|cat:$payqr_action:".tpl"}
        </div>
    {/if}
{/foreach}