<fieldset>
    <div class="control-group">
        <label class="control-label" for="payqr_rvt_amount_{$o.order_id}">Сумма возврата</label>
        <div class="controls">
            <input type="text" size="3" name="payqr_data[{$o.order_id}][revert_amount]" value="" class="input-medium" id="payqr_rvt_amount_{$o.order_id}">
        </div>
    </div>
</fieldset>
<input class="btn btn-primary " type="submit" name="dispatch[orders.payqr_revert.{$o.order_id}]" value="Подтвердить возврат">