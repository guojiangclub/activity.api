{!! Html::style('assets/backend/libs/datepicker/bootstrap-datetimepicker.min.css') !!}
{!! Html::style('assets/backend/libs/webuploader-0.1.5/webuploader.css') !!}
{!! Html::style('assets/backend/libs/Tagator/fm.tagator.jquery.css') !!}
{!! Html::style('assets/backend/activity/css/jquery-ui.min.css') !!}
{!! Html::style('assets/backend/activity/css/getpoint.css') !!}
@include("activity::public.css.createAndEditActivity")
@include("activity::public.activity.activitySetCategory")
<div class="ibox float-e-margins" id="edit-activity-detail" hidden>
    <div class="ibox-content">
        <div class="container-fluid">
            <div>
                <div>
                    <div class="activity-detail-category-item">
                        <button class="btn create-activity-toggle-page activity-detail-category-item">返回上一步</button>
                        <span class="activity-detail-category-item">当前活动类型</span>
                        <span style="color: #008cee; font-size:25px" class="activity-detail-category-item">通用</span>
                    </div>
                </div><!-- /.box-header -->
                <div class="hr-line-dashed"></div>
                <div class="box-body">
                    <div class="container-fluid">
                        {!! Form::open( [ 'url' => [route("activity.admin.store")], 'method' => "POST", 'class' => "form-horizontal", 'id' => "store-activity-form"] ) !!}
                        <input type="hidden" name="type" value="TRAIN">
                        @include("activity::public.activity.activityTitle")
                        @include("activity::public.activity.activityPoster")
                        @include("activity::public.activity.activityTime")
                        @include("activity::public.activity.activityMessage")
                        @include("activity::public.activity.activitySite")
                        @include("activity::public.activity.activityMember")
                        @include("activity::public.activity.activityContent")
                        @include("activity::public.activity.activityPayment")
                        @include("activity::public.activity.activityRefund")
                        @include("activity::public.activity.activityFinishTime")
                        @include("activity::public.activity.activityForm")
                        @include("activity::public.activity.activityStatement")
                        @include("activity::public.activity.activityPoint")
                        @include("activity::public.activity.activityCoach")
                        @include("activity::public.activity.activitySubmit")
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include("activity::public.modal.coachModal")
<div id="modal" class="modal inmodal fade" data-keyboard=false data-backdrop="static"></div>
<script>
    var fee_discount = '';
</script>
{!! Html::script('assets/backend/libs/md5.js') !!}
{!! Html::script('assets/backend/activity/js/common.js') !!}
{!! Html::script('assets/backend/activity/js/jquery-ui-1.10.4.min.js') !!}
<script charset="utf-8" src="//map.qq.com/api/js?v=2.exp&key={{ env('QQ_MAP_KEY') }}"></script>

@include('UEditor::head')
{!! Html::script('assets/backend/libs/jquery.form.min.js') !!}
{!! Html::script('assets/backend/libs/webuploader-0.1.5/webuploader.js') !!}
{!! Html::script('assets/backend/libs/Tagator/fm.tagator.jquery.js') !!}
{!! Html::script('assets/backend/libs/artTemplate/artTemplate.js') !!}
{!! Html::script('assets/backend/libs/artTemplate/artTemplate-plugin.js') !!}
{!! Html::script('assets/backend/libs/datepicker/bootstrap-datetimepicker.js') !!}
{!! Html::script('assets/backend/libs/datepicker/bootstrap-datetimepicker.zh-CN.js') !!}
@include("activity::public.script")
@include("activity::public.js.createAndEditActivity")
@include("activity::public.js.getPoint")