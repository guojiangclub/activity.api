<div class="form-group">
    <label class="col-md-2 control-label">报名费用<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="活动发布后不可修改"></i>：
    </label>
    <div class="col-md-8 padding-top-7">
        {{--暂时隐藏，用户标准产品--}}
        <input name="activity_payment_radio" id="activity-payment-type-charge"
               @if(isset($model) && $model->status > 0) disabled @endif
               type="radio" value="CHARGING"
               @if(isset($model) AND $model->fee_type=='CHARGING') checked @elseif(!isset($model)) checked @else @endif />
        收费活动（在线支付）&nbsp;<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="支持在线支付“现金”、“积分”、及“现金+积分”作为活动报名费用；也可通过添加多个电子票对活动设置多级报名费；"></i>
        &nbsp;<input name="activity_payment_radio" id="activity-payment-type-pass" type="radio" @if(isset($model) && $model->status > 0) disabled @endif value="PASS"
                     @if(isset($model) AND $model->fee_type=='PASS') checked @endif /> 活动通行证&nbsp;<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="即通过发放活动通行证，预先授予指定用户对本活动的报名资格，仅持有有效活动通行证的用户才可成功报名本活动；"></i>
        &nbsp;<input name="activity_payment_radio" id="activity-payment-type-offline-charge"
                     type="radio" @if(isset($model) && $model->status > 0) disabled @endif
                     value="OFFLINE_CHARGES"
                     @if(isset($model) AND $model->fee_type=='OFFLINE_CHARGES') checked @else   @endif /> VIP活动（预约审核）&nbsp;<i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="用户在前台仅可提交预约活动申请，由后台审核通过后才可成功报名活动。"></i>
        <!--收费活动offline text-->
        <div id="offline-charge-detail"
             style="margin-top:10px;display: {{(isset($model) AND $model->fee_type=='OFFLINE_CHARGES') ?"": "none"}};">
            {!! Form::label('name','活动金额(元)：', ['class' => 'col-md-3 control-label']) !!}
            <div class="col-md-9">
                @if(isset($model) AND $model->fee_type=='OFFLINE_CHARGES')
                    <input type="text" class="form-control" name="payment-offline-charge" placeholder=""
                           {{$model->status>0?'disabled':''}}   value="{{$payment->first()->price}}">
                @else
                    <input type="text" class="form-control" name="payment-offline-charge" placeholder=""
                           value="0">
                @endif
            </div>
        </div>
        <!--收费活动offline text /-->
        <div class="" style="display: @if(isset($model) AND $model->fee_type=='CHARGING') block @elseif(!isset($model)) block @else none @endif" id="activity-payment-detail">
            <div class="panel panel-default" style="margin-top: 20px;min-width: 650px;">
                <div class="row panel-body" style="padding-top: 0">
                    <table class="table table-hover table-striped">
                        <thead class="panel-title">
                        <tr>
                            <th>编号</th>
                            <th>电子票名称</th>
                            <th>金额</th>
                            <th>积分</th>
                            <th>名额限制</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody id="activity-payment-lists">
                        @if(isset($payment))
                            @foreach($payment as $key => $item)
                                <tr>
                                    <input type="hidden" name="activity-payment-id[]" value="{{$item->id}}">
                                    <td>
                                        {{$key+1}}
                                    </td>
                                    <td>
                                        <input type="text" name="activity-payment-title[]" value="{{$item->title}}" style="width: 80px;">
                                    </td>
                                    <td>
                                        ￥<input type="text" name="activity-payment-price[]" value="{{$item->price | 0}}" style="width: 80px;">
                                    </td>
                                    <td>
                                        <input type="text" name="activity-payment-point[]" value="{{$item->point | 0}}" style="width: 80px;">
                                    </td>
                                    <td>
                                        <input type="text" name="activity-payment-limit[]" value="{{$item->limit | 0}}" style="width: 80px;">
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)" class="btn btn-xs btn-danger del_payment_cell">
                                            <i data-toggle="tooltip" data-placement="top" class="fa fa-trash" title="" data-original-title="删除"></i>
                                        </a>
                                        <a class="switch_payment_status">
                                            <i class="fa switch  fa-toggle-{{ $item->status == 1 ? 'on' : 'off' }} " title="切换状态" data-status="{{ $item->status }}"><input type="hidden" name="activity-payment-status[]" value="{{$item->status}}"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="row panel-footer vim-margin-zero">
                    <div class="col-md-3 col-md-offset-4">
                        <button type="button" class="btn btn-primary" id="add-activity-payment-item">+增加电子票
                        </button>
                    </div>
                    <div class="col-md-5">
                        <div class="vim-flex-center-vertically">
                            <div>
                                <i class="fa fa-exclamation-triangle  fa-1x" style="color: #e77f0d"></i><span> 金额填0为纯积分支付</span>
                                , 名额限制填0为不限制
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/html" id="payment-item-template">
    <tr>
        <input type="hidden" name="activity-payment-id[]" value="new_{#paymentIndex#}">
        <td> {#paymentIndex#}</td>
        <td><input type="text" name="activity-payment-title[]" value=""  style="width: 80px;"></td>
        <td>￥ <input type="text" name="activity-payment-price[]" value="0"  style="width: 80px;"></td>
        <td><input type="text" name="activity-payment-point[]" value="0"  style="width: 80px;"></td>
        <td><input type="text" name="activity-payment-limit[]" value="0"  style="width: 80px;"></td>
        <td>
            <a href="javascript:void(0)" class="btn btn-xs btn-danger del_payment_cell">
                <i data-toggle="tooltip" data-placement="top" class="fa fa-trash" title="" data-original-title="删除"></i>
            </a>
        </td>
    </tr>
</script>


