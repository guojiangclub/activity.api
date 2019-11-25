@include("activity::public.css.createAndEditActivity")
<div class="ibox float-e-margins">
    <div class="ibox-content" style="display: block;">
        <div class="box-header with-border">
            <h3 class="box-title">添加活动报名表单</h3>
        </div>
        <div class="hr-line-dashed"></div>
        <div class="row">
            <div class="panel-body">
                {!! Form::open( [ 'url' => [route('activity.admin.form.store')], 'method' => 'POST', 'id' => 'activity-form-factory','class'=>'form-horizontal'] ) !!}
                <input type="hidden" name="id" value="{{ isset($model) && $model ? $model->id : ''}}">
                <div class="form-group">
                    <label class="col-md-2 control-label"><span style="color: red">*</span>表单名称：</label>
                    <div class="col-md-4">
                        <input class="form-control" name="name" value="{{ isset($model) && $model->name ? $model->name : ''}}">
                        <p style="color: #b6b3b3; margin: 0;margin-top: 5px;">温馨提示：标题长度不超过64个汉字</p>
                    </div>
                </div>
                @if(isset($model) && $model->id)
                    @include("activity::public.activity.activityApplyFormEdit")
                @else
                    @include("activity::public.activity.activityApplyFormCreate")
                @endif
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button type="submit" class="btn btn-primary">保存</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#activity-form-factory').ajaxForm({
            success: function (res) {
                if (res.status) {
                    swal({
                        title: '保存成功',
                        text: '',
                        type: "success",
                        showConfirmButton: true
                    }, function () {
                        window.location = '{{route('activity.admin.form')}}';
                    });
                } else {
                    swal("保存失败!", res.message, "error");
                }
            }
        });
    });
</script>
@include("activity::public.js.activityFormCreate")