<fieldset>
    {assign var="order_info" value=$o.order_id|fn_get_payqr_order_info}
    <div class="control-group">
        <label class="control-label" for="payqr_msg_text_{$o.order_id}">Текст сообщения к покупке</label>
        <div class="controls">
            <input type="text" size="3" name="payqr_data[{$o.order_id}][text]" value="{$order_info->message->text}" class="input-medium" id="payqr_msg_text_{$o.order_id}">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="payqr_msg_img_{$o.order_id}">URL изображения для сообщения к покупке</label>
        <div class="controls">
            <input type="text" size="3" name="payqr_data[{$o.order_id}][img]" value="{$order_info->message->imageUrl}" class="input-medium" id="payqr_msg_img_{$o.order_id}">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="payqr_msg_web_{$o.order_id}">URL сайта при нажатии на изображение для сообщения
                        к покупке</label>
        <div class="controls">
            <input type="text" size="3" name="payqr_data[{$o.order_id}][web]" value="{$order_info->message->url}" class="input-medium" id="payqr_msg_web_{$o.order_id}">
        </div>
    </div>
</fieldset>
<input class="btn btn-primary " type="submit" name="dispatch[orders.payqr_message.{$o.order_id}]" value="Дослать\изменить">