<div class="tabs-container">
    <ul class="nav nav-tabs">
        <li class="{{ Active::query('status','') }}"><a href="{{route('activity.admin.discount.index')}}" no-pjax> 有效促销
                <span class="badge">{{$validCount}} </span></a></li>
        <li class="{{ Active::query('status','invalid') }}"><a
                    href="{{route('activity.admin.discount.index',['status'=>'invalid'])}}" no-pjax> 禁用
                <span class="badge">{{$invalidCount}} </span></a></li>
    </ul>

    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
            <div class="panel-body">
                {!! Form::open( [ 'route' => ['activity.admin.discount.index'], 'method' => 'get', 'id' => 'discount-form','class'=>'form-horizontal'] ) !!}
                <input type="hidden" value="{{$status_t}}" name="status">
                <div class="row">

                    <div class="col-md-2">
                        <select class="form-control" name="type">
                            <option value="">所有类型</option>
                            <option value="1" {{request('type')=='1'?'selected ':''}} >订单折扣</option>
                            <option value="2" {{request('type')=='2'?'selected ':''}} >活动通行证</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" name="title" value="{{request('title')}}" placeholder="搜索活动优惠名称"
                                   class=" form-control"> <span
                                    class="input-group-btn">
                                    <button type="submit" class="btn btn-primary">查找</button></span></div>
                    </div>

                </div>

                {!! Form::close() !!}

                <div class="hr-line-dashed"></div>

                <div class="table-responsive">
                    @if(count($discount)>0)
                        <table class="table table-hover table-striped">
                            <tbody>
                            <!--tr-th start-->
                            <tr>
                                <th>优惠活动名称</th>
                                <th>优惠类型</th>
                                <th>有效时间</th>
                                <th>总数 / 领取率 / 使用率</th>
                                <th>领取地址</th>
                                <th>优惠券</th>
                                <th>明细</th>
                                <th>操作</th>
                            </tr>
                            <!--tr-th end-->
                            @foreach ($discount as $item)
                                <tr>
                                    <td>{{$item->title}}</td>
                                    <td>{{$item->type_text}}</td>
                                    <td>
                                        {{$item->starts_at}}<br>
                                        {{$item->ends_at}}
                                    </td>
                                    <td>
                                        @if($item->coupon_based == 1)
                                            {{$item->usage_limit .' / '. $item->take_rate.'%'.' / '.$item->used_rate.'%'}}
                                        @endif
                                    </td>
                                    <td style="position: relative;">
                                        <input type="text" value="{{settings('mobile_domain_url')}}community/?#/coupon/get?code={{$item->code}}">
                                        <label class="label label-danger copyBtn">复制链接</label>
                                    </td>
                                    <td>{{$item->coupon_based == 1 ? '是' : '无券'}}</td>
                                    <td>
                                        @if($item->coupon_based == 1)
                                            <a href="{{route('activity.admin.discount.coupon_list',['id'=>$item->id])}}" class="btn btn-xs btn-success" no-pjax>
                                                <i class="fa fa-eye" data-toggle="tooltip" data-placement="top" title="查看明细"></i></a>
                                        @endif
                                    </td>
                                    <td>

                                        <a
                                                class="btn btn-xs btn-primary"
                                                href="{{route('activity.admin.discount.edit',['id'=>$item->id])}}" no-pjax>
                                            <i data-toggle="tooltip" data-placement="top"
                                               class="fa fa-pencil-square-o"
                                               title="编辑"></i></a>

                                        <a><i data-id="{{$item->id}}"
                                              class="fa switch {{$item->status ? 'fa-toggle-on' : 'fa-toggle-off'}}"
                                              title="切换状态">

                                            </i></a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="6" class="footable-visible">
                                    {!! $discount->render() !!}
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    @else
                        <div>
                            &nbsp;&nbsp;&nbsp;当前无数据
                        </div>
                    @endif
                </div><!-- /.box-body -->
            </div>
        </div>
    </div>
</div>
<div id="modal" class="modal inmodal fade"></div>
{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.zclip/jquery.zclip.js') !!}
<script>
    $('.copyBtn').zclip({
	    path: "{{url('assets/backend/libs/jquery.zclip/ZeroClipboard.swf')}}",
	    copy: function () {
		    return $(this).prev().val();
	    }
    });


    $('.switch').click(function () {
	    var switchStatusUrl = '{{route('activity.admin.discount.switchStatus')}}';

	    var data = {
		    _token: $('meta[name="_token"]').attr('content'),
		    id: $(this).data('id')
	    };

	    $.post(switchStatusUrl, data, function (result) {
		    if (result.status) {
			    swal({
				    title: "修改成功！",
				    text: "",
				    type: "success"
			    }, function () {
				    window.location.reload();
			    });
		    } else {
			    swal("修改失败!", result.message, "error")
		    }
	    });
    });
</script>