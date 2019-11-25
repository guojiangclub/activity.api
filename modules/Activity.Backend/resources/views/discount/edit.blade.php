
{!! Html::style(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.min.css') !!}
{!! Html::style(env("APP_URL").'/assets/backend/libs/loader/jquery.loader.min.css') !!}
{!! Html::style(env("APP_URL").'/assets/backend/libs/formvalidation/dist/css/formValidation.min.css') !!}
{!! Html::style(env("APP_URL").'/assets/backend/libs/Tagator/fm.tagator.jquery.css') !!}
{!! Html::style(env("APP_URL").'/assets/backend/libs/pager/css/kkpager_orange.css') !!}
<style type="text/css">
    table.category_table > tbody > tr > td{border: none}
    .table-responsive{margin-top: 10px}
    .blue-bg{background-color:#eaf3ff; color: #000}
</style>
<div class="tabs-container">
    {{--<ul class="nav nav-tabs">--}}
        {{--<li class="active"><a aria-expanded="true" data-toggle="tab" href="#tab-1">基础信息</a></li>--}}
        {{--<li class=""><a aria-expanded="false" data-toggle="tab" href="#tab-2">详细配置</a></li>--}}
    {{--</ul>--}}
    {!! Form::open( [ 'url' => [route('activity.admin.discount.store')], 'method' => 'POST', 'id' => 'create-discount-form','class'=>'form-horizontal'] ) !!}
    <input type="hidden" value="{{$discount->id}}" name="id">
    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
            <div class="panel-body">
                <fieldset class="form-horizontal">
                    @include('activity::discount.includes.edit_base')
                </fieldset>

                <fieldset class="form-horizontal">
                    @include('activity::discount.includes.edit_configuration')
                </fieldset>

                <fieldset class="form-horizontal">
                    @include('activity::discount.includes.edit_action')
                </fieldset>

            </div>
        </div>


    </div>

    <div class="hr-line-dashed"></div>
    <div class="form-group">
        <div class="col-sm-4 col-sm-offset-2">
            <button class="btn btn-primary" type="submit">保存设置</button>
        </div>
    </div>
    {!! Form::close() !!}
</div>

<div id="activity_modal" class="modal inmodal fade"></div>
{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/common.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/admin/js/plugins/ladda/spin.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/admin/js/plugins/ladda/ladda.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/admin/js/plugins/ladda/ladda.jquery.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/loader/jquery.loader.min.js') !!}
@include('activity::discount.includes.script')
@include('activity::discount.includes.script_for_edit')
<script>
    $(function () {
        getSelectActivityData();

        // save return
        $('#create-discount-form').ajaxForm({
            success: function (result) {
                if(result.status){
                    swal({
                        title: "保存成功！",
                        text: "",
                        type: "success"
                    }, function() {
                        window.location = '{{route('activity.admin.discount.index')}}';
                    });
                } else {
                    swal({
                        title: "保存失败！",
                        text: result.message,
                        type: "error"
                    }, function() {

                    });
                }
            }
        });
    })
</script>