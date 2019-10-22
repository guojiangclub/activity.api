{!! Html::style(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.min.css') !!}
@if(Session::has('message'))
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-check"></i> 提示！</h4>
        {{ Session::get('message') }}
    </div>
@endif

<div class="tabs-container">
    <ul class="nav nav-tabs">
        <li class="{{ Active::query('status',0) }}"><a href="{{route('admin.activity.refund',['status'=>0])}}" no-pjax>待处理
            </a></li>
        <li class="{{ Active::query('status',8) }}"><a href="{{route('admin.activity.refund',['status'=>8])}}" no-pjax> 待退款
            </a></li>
        <li class="{{ Active::query('status',2) }}"><a href="{{route('admin.activity.refund',['status'=>2])}}" no-pjax> 已完成
            </a></li>
        <li class=""><a aria-expanded="false" data-toggle="tab" href="#tab-2">数据导出</a></li>
    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
            <div class="panel-body">
                <div class="row">
                    {!! Form::open( [ 'route' => ['admin.activity.refund'], 'method' => 'get', 'id' => 'ordersurch-form','class'=>'form-horizontal'] ) !!}
                    <input type="hidden" name="status" value="{{request('status')?request('status'):0}}">
                    <div class="col-md-2">
                        <select class="form-control" name="field">
                            <option value="refund_no">退款申请编号</option>
                            <option value="order_no">订单编号</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" name="value" placeholder="Search"
                                   class=" form-control"> <span
                                    class="input-group-btn">
                                            <button type="submit" class="btn btn-primary">查找</button></span></div>
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="hr-line-dashed"></div>
                <div class="box-body table-responsive">
                    <table class="table table-hover table-bordered">
                        <tbody>
                        <!--tr-th start-->
                        <tr>
                            <th>活动名称</th>
                            <th>活动ID</th>
                            <th>申请编号</th>
                            <th>申请金额</th>
                            <th>申请用户</th>
                            <th>订单号</th>
                            <th>申请类型</th>
                            <th>申请时间</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        <!--tr-th end-->

                        @foreach ($refunds as $item)
                            <tr>
                {{! $activity = \ElementVip\Activity\Core\Models\Activity::where('id',$item->order->activity_id)->first() }}
                                <td>{{$activity->title}}</td>
                                <td>{{$item->order->activity_id}}</td>
                                <td>{{$item->refund_no}}</td>
                                <td>{{$item->amount / 100}}</td>
                                <td>
                                    @if($item->user->name)
                                        {{$item->user->name}}
                                    @elseif($item->user->nick_name)
                                        {{$item->user->nick_name}}
                                    @elseif($item->user->mobile)
                                        {{$item->user->mobile}}
                                    @else

                                    @endif
                                </td>
                                <td>{{$item->order->order_no}}</td>
                                <td>退款</td>
                                <td>{{$item->created_at}}</td>
                                <td>{{$item->StatusText}}</td>
                                <td>
                                    <a class="btn btn-xs btn-primary"
                                       href="{{route('admin.activity.refund.show', ['id' => $item->id])}}">
                                        <i data-original-title="编辑" data-toggle="tooltip" data-placement="top"
                                           class="fa fa-pencil-square-o" title=""></i></a>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                    <div class="pull-left">
                        &nbsp;&nbsp;共&nbsp;{!! $refunds->total() !!} 条记录
                    </div>

                    <div class="pull-right">
                        {!! $refunds->render() !!}
                    </div>
                </div><!-- /.box-body -->
            </div>
        </div>

        <div id="tab-2" class="tab-pane">
            <div class="panel-body form-horizontal">
                @include('activity::refund.include.refund_export')
            </div>
        </div>
    </div>
</div>

<div id="modal" class="modal inmodal fade"></div>
{!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.zh-CN.js') !!}
@include('activity::refund.include.export_script')
