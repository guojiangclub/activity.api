    <div class="ibox float-e-margins">
        <div class="ibox-content" style="display: block;">
            <div class="box-header with-border">
                <h3 class="box-title">添加活动分类</h3>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="row">
                <div class="panel-body">
                    {!! Form::open( [ 'url' => [route('activity.admin.category.store')], 'method' => 'POST', 'id' => 'activity-category','class'=>'form-horizontal'] ) !!}
                    <input type="hidden" name="id" value="{{ isset($model) && $model ? $model->id : ''}}">
                    <div class="form-group">
                        <label class="col-md-2 control-label"><span style="color: red">*</span>分类名称：</label>
                        <div class="col-md-4">
                            <input class="form-control" name="name" value="{{ isset($model) && $model->name ? $model->name : ''}}">
                        </div>
                    </div>
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
	        $('#activity-category').ajaxForm({
		        success: function (res) {
			        if (res.status) {
				        swal({
					        title: '保存成功',
					        text: '',
					        type: "success",
					        showConfirmButton: true
				        }, function () {
					        window.location = '{{route('activity.admin.category')}}';
				        });
			        } else {
				        swal("保存失败!", res.message, "error");
                    }
		        }
	        });
        });
    </script>