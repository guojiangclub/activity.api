<div class="form-group">
    <label class="col-sm-2 control-label">减免类型：</label>
    <div class="col-sm-10">
        <select class="form-control m-b action-select" name="action[type]" onchange="actionChange(this)">
            <option selected="selected" value="activity_discount">免费活动通行一次</option>
        </select>
        <input type="hidden" name="action[configuration]" value="free">
    </div>
</div>


{{--<div class="form-group">--}}
    {{--<label class="col-sm-2 control-label">金额动作：</label>--}}
    {{--<div class="col-sm-10">--}}
        {{--<select class="form-control m-b action-select" name="action[type]" onchange="actionChange(this)">--}}
            {{--<option selected="selected" value="order_fixed_discount">订单减金额</option>--}}
            {{--<option value="order_percentage_discount">订单打折</option>--}}
        {{--</select>--}}
    {{--</div>--}}

    {{--<div class="col-sm-10 col-sm-offset-2" id="order-discount-action">--}}
        {{--<div class="input-group m-b">--}}
            {{--<span class="input-group-addon">$</span>--}}
            {{--<input class="form-control" type="text" name="action[configuration]" value="0">--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}

{{--<div class="form-group">--}}
    {{--<label class="col-sm-2 control-label">积分动作：</label>--}}
    {{--<div class="col-sm-10">--}}
        {{--<select class="form-control m-b action-select" name="action[type]" onchange="actionChange(this)">--}}
            {{--<option selected="selected" value="point_percentage_discount">按百分比</option>--}}
            {{--<option value="point_discount">按具体值</option>--}}
        {{--</select>--}}
    {{--</div>--}}
    {{--<div class="col-sm-10 col-sm-offset-2" id="point-discount-action">--}}
        {{--<div class="input-group m-b">--}}
            {{--<input class="form-control" type="text" name="action[configuration]" value="">--}}
            {{--<span class="input-group-addon">%</span>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
