{!! Html::style('assets/backend/libs/webuploader-0.1.5/webuploader.css') !!}
{!! Html::style('assets/backend/libs/ladda/ladda-themeless.min.css') !!}
{!! Html::style('assets/backend/libs/datepicker/bootstrap-datetimepicker.min.css') !!}

@if(Session::has('message'))
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-check"></i> 提示！</h4>
        {{ Session::get('message') }}
    </div>
@endif

<div class="tabs-container">
    <ul class="nav nav-tabs">
        <li class="{{ Active::query('status', 0) }}"><a href="{{route('activityOrder.admin.index')}}" no-pjax>所有订单
                <span class="badge">{{ElementVip\Activity\Core\Models\Member::whereBetween('status',[0,4])->where('role', 'user')->count()}}</span></a></li>
        <li class="{{ Active::query('status',1) }}"><a href="{{route('activityOrder.admin.index',['status'=>1])}}" no-pjax>已报名未签到
                <span class="badge">{{ElementVip\Activity\Core\Models\Member::where('status',1)->where('role', 'user')->count()}}</span></a></li>
        <li class="{{ Active::query('status',2) }}"><a href="{{route('activityOrder.admin.index',['status'=>2])}}" no-pjax>已签到
                <span class="badge">{{ElementVip\Activity\Core\Models\Member::where('status',2)->where('role', 'user')->count()}}</span></a></li>
        <li class="{{ Active::query('status',3) }}"><a href="{{route('activityOrder.admin.index',['status'=>3])}}" no-pjax>已取消
                <span class="badge">{{ElementVip\Activity\Core\Models\Member::where('status',3)->where('role', 'user')->count()}}</span></a></li>
        <li class="{{ Active::query('status',4) }}"><a href="{{route('activityOrder.admin.index',['status'=>4])}}" no-pjax>待审核
                <span class="badge">{{ElementVip\Activity\Core\Models\Member::where('status',4)->where('role', 'user')->count()}}</span></a></li>
    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
            <div class="panel-body">
                {!! Form::open( [ 'route' => ['activityOrder.admin.index'], 'method' => 'get', 'id' => 'ordersurch-form','class'=>'form-horizontal'] ) !!}
                <div class="panel-body" style="border-bottom: none; margin-bottom: 0">
                    <div class="row">
                        <input type="hidden" id="status" name="status" value="{{!empty(request('status'))?request('status'):0}}">
                        <div class="col-sm-2">
                            <div class="input-group date form_datetime">
                                    <span class="input-group-addon" style="cursor: pointer">
                                        <i class="fa fa-calendar"></i>&nbsp;&nbsp;时间</span>
                                <input type="text" class="form-control inline" name="stime" value="{{request('stime')}}" placeholder="开始 " readonly>
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group date form_datetime">
                                <span class="input-group-addon" style="cursor: pointer">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input type="text" class="form-control" name="etime" value="{{request('etime')}}" placeholder="截止" readonly>
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-md-5 control-label" style="text-align: center;">所属活动：</label>
                                <div class="col-md-6">
                                    <select class="form-control" name="activity_id" id="activity_selector">
                                        <option value="0">请选择</option>
                                        @if(count($activities)>0)
                                            @foreach($activities as $activity)
                                                <option value="{{ $activity->id }}" {{ request('activity_id') ==$activity->id ? 'selected' : '' }}>{{$activity->title}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-md-5 control-label" style="text-align: center;">电子票：</label>
                                <div class="col-md-6">
                                    <select class="form-control" name="payment_id">
                                        <option value="0">请选择</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body" style="border-top: none; margin-top: -30px">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <select class="form-control" name="field">
                                        <option value=0>请选择搜索条件</option>
                                        <option value="order_no" {{request('field')=='order_no'?'selected':''}} >订单编号</option>
                                        <option value="mobile" {{request('field')=='mobile'?'selected':''}} >联系电话</option>
                                        <option value="email" {{request('field')=='email'?'selected':''}} >邮箱</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" name="value" value="{{request('value')}}" placeholder="Search" class=" form-control">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-primary">查找</button>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <input type="hidden" name="excel" value="0">
                            <div class="btn-group">
                                <a class="btn btn-primary dropdown-toggle batch" data-toggle="dropdown" href="javascript:;">导出 <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    @if($status!='all')
                                        <li><a class="export-apply" data-status="1" href="javascript:;">导出当前订单</a></li>
                                    @endif
                                    <li><a class="export-apply" data-status="2" href="javascript:;">导出所有订单</a></li>
                                    <li><a class="export-apply" data-status="3" href="javascript:;">导出报名信息</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
                <div class="table-responsive">
                    <div id="orders">
                        @include('activity::public.activityOrder.orders_activity_list')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modal" class="modal inmodal fade"></div>
<div id="modal_invoice" class="modal inmodal fade"></div>
<div id="modal_produce" class="modal inmodal fade"></div>
{!! Html::script('assets/backend/libs/webuploader-0.1.5/webuploader.js') !!}
{!! Html::script('assets/backend/libs/datepicker/bootstrap-datetimepicker.js') !!}
{!! Html::script('assets/backend/libs/datepicker/bootstrap-datetimepicker.zh-CN.js') !!}
{!! Html::script('assets/backend/libs/formvalidation/dist/js/formValidation.min.js') !!}
{!! Html::script('assets/backend/libs/formvalidation/dist/js/framework/bootstrap.min.js') !!}
{!! Html::script('assets/backend/libs/formvalidation/dist/js/language/zh_CN.js') !!}
{!! Html::script('assets/backend/libs/sortable/Sortable.min.js') !!}
{!! Html::script('assets/backend/libs/jquery.form.min.js') !!}
{!! Html::script('assets/backend/admin/js/plugins/ladda/spin.min.js') !!}
{!! Html::script('assets/backend/admin/js/plugins/ladda/ladda.min.js') !!}
{!! Html::script('assets/backend/admin/js/plugins/ladda/ladda.jquery.min.js') !!}
{!! Html::script('assets/backend/libs/loader/jquery.loader.min.js') !!}
@include('activity::public.activityOrder.script')