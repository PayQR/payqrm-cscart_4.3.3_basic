{if $category_data.category_id}
{include file="common/subheader.tpl" title="Категория PayQR" target="#payqr_categories"}
<div id="payqr_categories" class="in collapse">
    <div class="control-group">
        <label class="control-label" for="payqr_category">Категория PayQR</label>
        <div class="controls">
            {assign var="payqr_categories" value=""|fn_get_payqr_categories}
            <select name="payqr_category_id" id="payqr_category">
                <option value="0"></option>
                {foreach from=$payqr_categories item="payqr_category"}
                    <option value="{$payqr_category.category_id}"{if $category_data.payqr_category_id == $payqr_category.category_id} selected{/if}>{$payqr_category.description}</option>
                {/foreach}
            </select>
        </div>
    </div>
</div>
{/if}