    <div class="tabs-container">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_1"  data-toggle="tab" aria-expanded="true">基本信息</a></li>
            <li class=""><a href="#tab_2"  data-toggle="tab" aria-expanded="false">报名人信息</a></li>
            <li class=""><a href="#tab_3"  data-toggle="tab" aria-expanded="false">活动清单</a></li>
            <li class=""><a href="#tab_4" data-toggle="tab" aria-expanded="false">优惠信息</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
                <div class="panel-body">
                    @include('activity::public.activityOrder.order_basic')
                </div>
            </div>
            <div class="tab-pane" id="tab_2">
                <div class="panel-body">
                    @include('activity::public.activityOrder.activity_user_info')
                </div>
            </div>
            <div class="tab-pane" id="tab_3">
                <div class="panel-body">
                    @include('activity::public.activityOrder.order_activity')
                </div>
            </div>
            <div class="tab-pane" id="tab_4">
                <div class="panel-body">
                    @include('activity::public.activityOrder.order_discount')
                </div>
            </div>
        </div>
    </div>
    {!! Html::script('assets/backend/libs/webuploader-0.1.5/webuploader.js') !!}
    {!! Html::script('assets/backend/libs/datepicker/bootstrap-datetimepicker.js') !!}
    {!! Html::script('assets/backend/libs/datepicker/bootstrap-datetimepicker.zh-CN.js') !!}