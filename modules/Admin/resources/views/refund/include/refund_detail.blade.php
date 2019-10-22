{!! Form::open( [ 'url' => [route('admin.activity.refund.store')], 'method' => 'POST','id' => 'base-form','class'=>'form-horizontal'] ) !!}
<input type="hidden" name="id" value="{{$refund->id}}">

<div class="form-group">
    <label class="control-label col-lg-2">售后编号：</label>
    <div class="col-lg-9">
        <p class="form-control-static">{{$refund->refund_no}}</p>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">订单编号：</label>
    <div class="col-lg-9">
        <p class="form-control-static">{{$refund->order->order_no}}</p>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">申请用户：</label>
    <div class="col-lg-9">
        <p class="form-control-static">
            @if($user_info=$refund->user)
                @if($user_info->nick_name)
                    {{$user_info->nick_name}}
                @elseif($user_info->name)
                    {{$user_info->name}}
                @else
                    {{$user_info->mobile}}
                @endif
            @endif
        </p>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">申请时间：</label>
    <div class="col-lg-9">
        <p class="form-control-static">{{$refund->created_at}}</p>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">申请类型：</label>
    <div class="col-lg-9">
        <p class="form-control-static">退款</p>
        <input type="hidden" name="type" value="{{$refund->type}}">
        <input type="hidden" name="typeText" value="{{$refund->TypeText}}">
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">申请金额：</label>
    <div class="col-lg-9">
        <p class="form-control-static">{{$refund->amount/100}} 元</p>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-2">退款原因：</label>
    <div class="col-lg-9">
        <p class="form-control-static">{{$refund->reason}}</p>
    </div>
</div>

<!----申请 待审核操作-->
@if($refund->status == 0)
    <div class="form-group">
        <label class="control-label col-lg-2">处理：</label>
        <div class="col-lg-9">
            <label>
                <input type="radio" name="opinion" value="1" checked="">
                同意
            </label>
            <label>
                <input type="radio" name="opinion" value="2">
                拒绝
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-2">处理意见：</label>
        <div class="col-lg-9">
            <textarea class="form-control" name="remarks" placeholder=""></textarea>
        </div>
    </div>
@endif

<!--商家打款操作-->
@if($refund->status==8)
    <div class="form-group">
        <label class="control-label col-lg-2">退款备注：</label>
        <div class="col-lg-9">
            <textarea class="form-control" name="remarks" placeholder=""></textarea>
        </div>
    </div>
@endif

<input type="hidden" name="status" value="{{$refund->status}}">
<div class="hr-line-dashed"></div>
<div class="form-group">
    <div class="col-md-offset-2 col-md-8 controls">
        {!! $refund->ActionBtnText !!}
    </div>
</div>
{!! Form::close() !!}