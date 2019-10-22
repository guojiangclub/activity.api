<div class="form-group">
    <label class="col-sm-2 control-label">活动折扣名称：</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" name="base[title]" placeholder=""/>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">活动折扣说明：</label>
    <div class="col-sm-10">
        <textarea class="form-control" name="base[intro]" rows="4"></textarea>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">生效时间：</label>
    <div class="col-sm-5">
        <div class="input-group date form_datetime">
                                        <span class="input-group-addon" style="cursor: pointer">
                                            <i class="fa fa-calendar"></i>&nbsp;&nbsp;开始</span>
            <input type="text" class="form-control inline" name="base[starts_at]" value="{{date("Y-m-d h:m",time())}}"
                   placeholder="点击选择开始时间" readonly>
            <span class="add-on"><i class="icon-th"></i></span>
        </div>
    </div>

    <div class="col-sm-5">
        <div class="input-group date form_datetime">
                                        <span class="input-group-addon" style="cursor: pointer">
                                            <i class="fa fa-calendar"></i>&nbsp;&nbsp;截止</span>
            <input type="text" class="form-control" name="base[ends_at]"
                   value="{{date("Y-m-d h:m",time()+60*60*24*30)}}" placeholder="点击选择结束时间" readonly>
            <span class="add-on"><i class="icon-th"></i></span>
        </div>
    </div>
</div>


<div class="form-group">
    <label class="col-sm-2 control-label">优惠类型：</label>
    <div class="col-sm-10">
        {{--<label class="checkbox-inline i-checks"><input name="base[type]" type="radio" value="1" disabled>--}}
            {{--订单折扣</label>--}}
        <label class="checkbox-inline i-checks"><input name="base[type]" type="radio" value="2" checked> 活动通行证</label>
    </div>
</div>

<div class="form-group" id="coupon" style="display: none;">
    <label class="col-sm-2 control-label">优惠券：</label>
    <div class="col-sm-10">
        <label class="checkbox-inline i-checks"><input name="base[coupon_based]" type="radio" value="0" checked>
            否</label>
        <label class="checkbox-inline i-checks"><input name="base[coupon_based]" type="radio" value="1"> 是</label>
    </div>
</div>

<div id="code" style="display: block;">
    <div class="form-group">
        <label class="col-sm-2 control-label">兑换码：</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="base[code]" placeholder=""/>
        </div>
    </div>


    {{--<div class="form-group">--}}
        {{--<label class="col-sm-2 control-label">使用截止时间：</label>--}}
        {{--<div class="col-sm-3">--}}
            {{--<div class="input-group date form_datetime">--}}
                                        {{--<span class="input-group-addon" style="cursor: pointer">--}}
                                            {{--<i class="fa fa-calendar"></i>&nbsp;&nbsp</span>--}}
                {{--<input type="text" class="form-control inline" name="base[useend_at]"--}}
                       {{--value="{{date("Y-m-d h:m",time()+60*60*24*30)}}"--}}
                       {{--placeholder="点击选择时间" readonly>--}}
                {{--<span class="add-on"><i class="icon-th"></i></span>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

    <div class="form-group">
        <label class="col-sm-2 control-label">每人可领取数量：</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="base[per_usage_limit]" placeholder=""
                   value=""/>
        </div>
    </div>

</div>


<div class="form-group">
    <label class="col-sm-2 control-label">可用总数：</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" name="base[usage_limit]" placeholder=""/>
    </div>
</div>

