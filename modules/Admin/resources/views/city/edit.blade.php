    {!! Html::style('assets/backend/libs/webuploader-0.1.5/webuploader.css') !!}
    <style>
        #distPicker select{
            display: inline-block;
            width: 30%;
        }
    </style>
    <div class="ibox float-e-margins">
        <div class="ibox-content" style="display: block;">
            <div class="box-header with-border">
                <h3 class="box-title">城市编辑</h3>
            </div><!-- /.box-header -->
            <div class="hr-line-dashed"></div>
            <div class="row">
                <div class="panel-body">
                    {!! Form::open( [ 'url' => [route('activity.admin.city.store')], 'method' => 'POST', 'id' => 'activity-city','class'=>'form-horizontal'] ) !!}
                    <input type="hidden" name="id" value="{{$city->id}}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">名称：</label>
                        <div class="col-sm-4">
                            <input name="name" class="form-control" value="{{$city->name}}" placeholder="城市名称">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">地址：</label>
                        <div class="col-sm-4" id="distPicker">
                            <select class="form-control" id="province" name="province"></select>
                            <select class="form-control" id="city" name="city"></select>
                            <select class="form-control" id="area" name="area"></select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">图片：</label>
                        <div class="col-sm-10">
                            <div class="pull-left" id="activity-poster">
                                <img src="{{!empty($city->img) ? $city->img : '/assets/backend/activity/backgroundImage/pictureBackground.png'}}"
                                     alt="" class="img" width="226px" height="91px" style="margin-right: 23px;">
                                <input type="hidden" name="img" class="form-control" value="{{$city->img}}">
                            </div>
                            <div width="226px" height="91px" class="">
                                <div class="clearfix" style="padding-top: 22px;">
                                    <div id="filePicker">添加图片</div>
                                    <p style="color: #b6b3b3">温馨提示：图片尺寸建议为：750*300, 图片小于4M</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit">保存</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    {!! Html::script('assets/backend/libs/distpicker/distpicker-2.0.js') !!}
    {!! Html::script('assets/backend/libs/webuploader-0.1.5/webuploader.js') !!}
    <script>
        // 初始化Web Uploader
        $(document).ready(function () {
            var postImgUrl = '{{route('upload.image',['_token'=>csrf_token()])}}';
            // 初始化Web Uploader
            var uploader = WebUploader.create({
                auto: true,
                swf: '{{url('assets/backend/libs/webuploader-0.1.5/Uploader.swf')}}',
                server: '{{route('upload.image',['_token'=>csrf_token()])}}',
                pick: '#filePicker',
                fileVal: 'upload_image',
                accept: {
                    title: 'Images',
                    extensions: 'jpg,jpeg,png',
                    mimeTypes: 'image/jpg,image/jpeg,image/png'
                }
            });
            // 文件上传成功，给item添加成功class, 用样式标记上传成功。
            uploader.on('uploadSuccess', function (file, response) {
                $('#activity-poster img').attr("src", response.url);
                $('#activity-poster input').val( response.url);
            });
            $('#distPicker').distpicker('reset');
            $('#distPicker').distpicker('destroy');
            $('#distPicker').distpicker({
                autoSelect: false,
                province: '{{$city->province}}',
                city: '{{$city->city}}',
                district: '{{$city->area}}',
                valueType: 'code'
            });

	        $('#activity-city').ajaxForm({
		        success: function (res) {
			        if (res.status) {
				        swal({
					        title: '保存成功',
					        text: '',
					        type: "success",
					        showConfirmButton: true
				        }, function () {
					        window.location = '{{route('activity.admin.city')}}';
				        });
			        } else {
				        swal("保存失败!", res.message, "error");
			        }
		        }
	        });
        });
    </script>