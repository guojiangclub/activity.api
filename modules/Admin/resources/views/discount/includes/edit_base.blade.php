<div class="form-group">
    <label class="col-sm-2 control-label">活动折扣名称：</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" name="base[title]" placeholder="" value="{{$discount->title}}"/>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">活动折扣说明：</label>
    <div class="col-sm-10">
        <textarea class="form-control" name="base[intro]" rows="4">{{$discount->intro}}</textarea>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">状态：</label>
    <div class="col-sm-10">
        <label class="checkbox-inline i-checks"><input name="base[status]" type="radio"
                                                       value="1" {{$discount->status == 1?'checked' : ''}}> 启用</label>
        <label class="checkbox-inline i-checks"><input name="base[status]" type="radio"
                                                       value="0" {{$discount->status == 0?'checked' : ''}}> 禁用</label>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">生效时间：</label>
    <div class="col-sm-3">
        <div class="input-group date form_datetime">
                                        <span class="input-group-addon" style="cursor: pointer">
                                            <i class="fa fa-calendar"></i>&nbsp;&nbsp;开始</span>
            <input type="text" class="form-control inline" name="base[starts_at]" value="{{$discount->starts_at}}"
                   placeholder="点击选择开始时间" readonly>
            <span class="add-on"><i class="icon-th"></i></span>
        </div>
    </div>

    <div class="col-sm-3">
        <div class="input-group date form_datetime">
                                        <span class="input-group-addon" style="cursor: pointer">
                                            <i class="fa fa-calendar"></i>&nbsp;&nbsp;截止</span>
            <input type="text" class="form-control" name="base[ends_at]" value="{{$discount->ends_at}}"
                   placeholder="点击选择结束时间" readonly>
            <span class="add-on"><i class="icon-th"></i></span>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">优惠类型：</label>
    <div class="col-sm-10">
        {{--<label class="checkbox-inline i-checks"><input name="base[type]" type="radio" value="1"--}}
                                                       {{--{{$discount->type ==1?'checked':''}} disabled>--}}
            {{--订单折扣</label>--}}
        <label class="checkbox-inline i-checks"><input name="base[type]" type="radio" value="2"
                                                       {{$discount->type ==2?'checked':''}} disabled> 活动通行证</label>
    </div>
</div>


<div class="form-group" id="coupon" style="display: {{$discount->type ==2?'none':'block'}};">
    <label class="col-sm-2 control-label">优惠券：</label>
    <div class="col-sm-10">
        <label class="checkbox-inline i-checks"><input name="base[coupon_based]" type="radio"
                                                       value="0" {{$discount->coupon_based == 0?'checked' : ''}}>
            否</label>
        <label class="checkbox-inline i-checks"><input name="base[coupon_based]" type="radio"
                                                       value="1" {{$discount->coupon_based == 1?'checked' : ''}}>
            是</label>
    </div>
</div>

<div id="code" style="display: {{$discount->coupon_based == 0?'none' : 'block'}};">
    <div class="form-group">
        <label class="col-sm-2 control-label">兑换码：</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="base[code]" value="{{$discount->code}}"/>
        </div>
    </div>

    {{--<div class="form-group">--}}
        {{--<label class="col-sm-2 control-label">使用截止时间：</label>--}}
        {{--<div class="col-sm-3">--}}
            {{--<div class="input-group date form_datetime">--}}
                                        {{--<span class="input-group-addon" style="cursor: pointer">--}}
                                            {{--<i class="fa fa-calendar"></i>&nbsp;&nbsp</span>--}}
                {{--<input type="text" class="form-control inline" name="base[useend_at]" value="{{$discount->useend_at}}"--}}
                       {{--placeholder="点击选择时间" readonly>--}}
                {{--<span class="add-on"><i class="icon-th"></i></span>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

    <div class="form-group">
        <label class="col-sm-2 control-label">每人可领取数量：</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="base[per_usage_limit]" placeholder=""
                   value="{{$discount->per_usage_limit}}"/>
        </div>
    </div>

</div>

<div class="form-group">
    <label class="col-sm-2 control-label">可用总数：</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" name="base[usage_limit]" placeholder=""
               value="{{$discount->usage_limit}}"/>
    </div>
</div>


