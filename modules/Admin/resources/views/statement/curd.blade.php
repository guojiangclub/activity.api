<div class="ibox float-e-margins">
    <div class="ibox-content" style="display: block;">
        <div class="box-header with-border">
            <h3 class="box-title">添加免责声明</h3>
        </div>
        <div class="hr-line-dashed"></div>
        <div class="row">
            <div class="panel-body">
                {!! Form::open( [ 'url' => [route('activity.admin.statement.store')], 'method' => 'POST', 'id' => 'activity-statement','class'=>'form-horizontal'] ) !!}
                <input type="hidden" name="id" value="{{ isset($model) && $model ? $model->id : ''}}">
                <div class="form-group">
                    <label class="col-md-2 control-label"><span style="color: red">*</span>标题：</label>
                    <div class="col-md-4">
                        <input class="form-control" name="title" value="{{ isset($model) && $model->title ? $model->title : ''}}">
                        <p style="color: #b6b3b3; margin: 0;margin-top: 5px;">温馨提示：标题长度不超过64个汉字</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><span style="color: red">*</span>免责声明：</label>
                    <div class="col-md-8">
                        <script id="container" name="statement" type="text/plain">
                            @if(isset($model) && $model){!!  $model->statement !!}@endif
                        </script>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
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

{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/jquery.http.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/page/jquery.pages.js') !!}

@include('UEditor::head')
<script>
    var ue = UE.getEditor('container', {
        initialFrameHeight: 560,
        allowDivTransToP: false,
        autoHeightEnabled: false
    });
    ue.ready(function () {
        ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');
    });


    $("#upload").on("click", function () {
        var el_list = $(this);
        $.addImage(el_list, "selectHighlight");
    });

    $(function () {
        $('#activity-statement').ajaxForm({
            success: function (res) {
                if (res.status) {
                    swal({
                        title: '保存成功',
                        text: '',
                        type: "success",
                        showConfirmButton: true
                    }, function () {
                        window.location = '{{route('activity.admin.statement')}}';
                    });
                } else {
                    swal("保存失败!", res.message, "error");
                }
            }
        });
    });
</script>