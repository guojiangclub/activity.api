<div class="hr-line-dashed"></div>
<div class="table-responsive">
    <table id="order-table" class="table table-hover table-striped">
        <tbody>
        <!--tr-th start-->
        <tr>
            {{--<th><input type="checkbox" class="check-all"></th>--}}
            <th>姓名</th>
            <th>电话</th>
            <th>订单编号</th>
            <th>活动ID</th>
            <th>报名时间</th>
            <th>签到时间</th>
            <th>报名取消时间</th>
            <th>状态</th>

            <th style="width: 150px;">操作</th>
        </tr>
        <!--tr-th end-->
        @foreach ($orders as $order)
            {{! $user = $order->user}}
            <tr class="order{{$order->id}}">
                {{--<td><input class="checkbox" type="checkbox" value="{{$order->id}}" name="ids[]"></td>--}}
                <td>@if($user->name) {{$user->name}} @elseif($user->nick_name) {{$user->nick_name}} @else @endif</td>
                <td>{{$user->mobile}}</td>
                <td>{{$order->order_no}}</td>
                <td>{{$order->activity_id}}</td>
                <td>{{$order->joined_at}}</td>
                <td>{{$order->signed_at}}</td>
                <td>{{$order->cancel_at}}</td>
                <td>
                    @if($order->status == 0)
                        待支付
                    @elseif($order->status ==1 )
                        已报名
                    @elseif($order->status == 2)
                        已签到
                    @elseif($order->status == 3)
                        已取消
                    @elseif($order->status == 4)
                        待审核
                    @endif
                </td>

                <td style="position: relative;">
                    @if($order->status==1)
                        <a data-href="{{route('activityOrder.admin.changeStatus',['id'=>$order->id])}}"
                           class="btn btn-xs btn-success change-status" href="javascript:;"
                           data-title="确认将该订单修改为已签到状态吗?">
                            <i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top"
                               title="修改状态"></i></a>
                    @endif

                    @if($order->status==4)
                        <a data-href="{{route('activityOrder.admin.audit',['id'=>$order->id])}}"
                           class="btn btn-xs btn-success change-status" href="javascript:;" data-title="确认审核通过吗">
                            <i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top"
                               title="审核"></i></a>
                    @endif

                    <a href="{{route('activityOrder.admin.detail',['id'=>$order->id])}}"
                       class="btn btn-xs btn-success" no-pjax>
                        <i class="fa fa-eye" data-toggle="tooltip" data-placement="top" title="查看"></i></a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="pull-left">
        &nbsp;&nbsp;共&nbsp;{!! $orders->total() !!} 条记录
    </div>

    <div class="pull-right id='ajaxpag'">
        {!! $orders->appends(['status' => request('status'), 'stime' => request('stime'), 'etime' => request('etime'), 'activity_id' => request('activity_id'), 'payment_id' => request('payment_id'), 'field' => request('field'), 'value' => request('value'), 'excel' => request('excel')])->render() !!}
    </div>
</div>












