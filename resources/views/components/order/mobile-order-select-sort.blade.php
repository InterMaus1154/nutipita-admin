<div class="sm:hidden">
    <x-form.form-label id="mobile_sort" text="Sort by"/>
    <x-form.form-select id="mobile_sort" wireModelLive="mobileSort">
        <option value="desc:customer">Customer Name (desc)</option>
        <option value="asc:customer">Customer Name (asc)</option>
        <option value="desc:order_placed_at">Placed At (desc)</option>
        <option value="asc:order_placed_at">Placed At (asc)</option>
        <option value="desc:order_due_at">Due At (desc)</option>
        <option value="asc:order_due_at">Due At (asc)</option>
        <option value="desc:order_status">Status (desc)</option>
        <option value="asc:order_status">Status (asc)</option>
        <option value="desc:total_pita">Total Pita (desc)</option>
        <option value="asc:total_pita">Total Pita (asc)</option>
        <option value="desc:total_price">Total Price (desc)</option>
        <option value="asc:total_price">Total Price (asc)</option>
    </x-form.form-select>
</div>
