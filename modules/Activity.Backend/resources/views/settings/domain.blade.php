<div class="ibox float-e-margins">
        <div class="ibox-content" style="display: block;">
            <form method="post" action="{{route('activity.admin.settings.domainStore')}}" class="form-horizontal" id="setting_site_form">
                {{csrf_field()}}
                <div class="form-group">
                    <label class="col-sm-2 control-label">前端活动发布表单id：</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" value="{{settings('activity_publish_form_id') ? settings('activity_publish_form_id') : ''}}" name="activity_publish_form_id" placeholder="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">前端活动发布免责声明id：</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" value="{{settings('activity_publish_statement_id') ? settings('activity_publish_statement_id') : ''}}" name="activity_publish_statement_id" placeholder="">
                    </div>
                </div>

                <div class="form-group"><label class="col-sm-2 control-label">前端活动发布列表图片:</label>
                    <div class="col-sm-10">
                        <input type="hidden" name="activity_publish_img_list" value="{{settings('activity_publish_img_list')?settings('activity_publish_img_list'):''}}">
                        <img class="activity_publish_img_list" src="{{settings('activity_publish_img_list')?settings('activity_publish_img_list'):''}}" alt="" style="max-width: 100px;">
                        <div id="imgListPicker">选择图片</div>
                        <div class="clearfix"></div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label">公众号APPID：</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" value="{{settings('activity_app_id') ? settings('activity_app_id') : ''}}" name="activity_app_id" placeholder="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">小程序 appid：</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control"
                               value="{{settings('activity_mini_program_app_id') ? settings('activity_mini_program_app_id') : ''}}" name="activity_mini_program_app_id" placeholder="">

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">小程序appsecret：</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control"
                               value="{{settings('activity_mini_program_secret') ? settings('activity_mini_program_secret') : ''}}" name="activity_mini_program_secret" placeholder="">

                    </div>
                </div>



                {{--<div class="form-group">
                    <label class="col-sm-2 control-label">短信模板id：</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" value="{{settings('activity_sms_template_id') ? settings('activity_sms_template_id') : ''}}" name="activity_sms_template_id" placeholder="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">活动报名成功通知：</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control"
                               value="{{settings('activity_join_notice') ? settings('activity_join_notice') : ''}}"
                               name="activity_join_notice" placeholder="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">活动签到成功通知：</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control"
                               value="{{settings('activity_sign_notice') ? settings('activity_sign_notice') : ''}}"
                               name="activity_sign_notice" placeholder="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">报名状态通知：</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control"
                               value="{{settings('activity_sign_status_notice') ? settings('activity_sign_status_notice') : ''}}"
                               name="activity_sign_status_notice" placeholder="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">活动结束通知：</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control"
                               value="{{settings('activity_end_notice') ? settings('activity_end_notice') : ''}}"
                               name="activity_end_notice" placeholder="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">积分到账通知：</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control"
                               value="{{settings('activity_point_notice') ? settings('activity_point_notice') : ''}}"
                               name="activity_point_notice" placeholder="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">活动提醒通知：</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control"
                               value="{{settings('activity_remind_notice') ? settings('activity_remind_notice') : ''}}"
                               name="activity_remind_notice" placeholder="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">微信领取PASS code：</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control"
                               value="{{settings('activity_coupon_code_wx') ? settings('activity_coupon_code_wx') : ''}}"
                               name="activity_coupon_code_wx" placeholder="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">签到获取PASS code：</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control"
                               value="{{settings('activity_coupon_code_sign') ? settings('activity_coupon_code_sign') : ''}}"
                               name="activity_coupon_code_sign" placeholder="">
                    </div>
                </div>--}}

                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit">保存设置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
	        var imgListPicker = WebUploader.create({
		        auto: true,
		        swf: '{{url(env("APP_URL").'/assets/backend/libs/webuploader-0.1.5/Uploader.swf')}}',
		        server: '{{route('file.upload',['_token'=>csrf_token()])}}',
		        pick: '#imgListPicker',
		        fileVal: 'file',
		        accept: {
			        title: 'Images',
			        extensions: 'gif,jpg,jpeg,bmp,png',
			        mimeTypes: 'image/*'
		        }
	        });

	        imgListPicker.on('uploadSuccess', function (file, response) {
		        var img_url = response.url;

		        $('input[name="activity_publish_img_list"]').val(img_url);
		        $('.activity_publish_img_list').attr('src', img_url);
	        });
        });


        $('#setting_site_form').ajaxForm({
	        success: function (result) {
		        if (!result.status) {
			        swal("保存失败!", result.error, "error")
		        } else {
			        swal({
				        title: "保存成功！",
				        text: "",
				        type: "success"
			        }, function () {
				        location.reload();
			        });
		        }
	        },
	        error: function () {
		        swal("保存失败!", '服务器内部错误', "error")
	        }
        });
    </script>