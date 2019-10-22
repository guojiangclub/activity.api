<script type="text/javascript">
    $('.form_datetime').datetimepicker({
	    minView: 0,
	    format: "yyyy-mm-dd hh:ii:ss",
	    autoclose: 1,
	    language: 'zh-CN',
	    weekStart: 1,
	    todayBtn: 1,
	    todayHighlight: 1,
	    startView: 2,
	    forceParse: 0,
	    showMeridian: true,
	    minuteStep: 1,
	    maxView: 4
    });

    $('body').on('click', '.vim-button-box-item-activity-category', function () {
	    var that = $(this);
	    var value = that.data('value');
	    $('input[name="type"]').val(value);
    });

</script>
<script type="text/javascript">
    var ue = UE.getEditor('container', {
	    initialFrameHeight: 560,
	    allowDivTransToP: false,
	    autoHeightEnabled: false
    });
    ue.ready(function () {
	    ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');
    });

    var uepc = UE.getEditor('containerpc');
    uepc.ready(function () {
	    uepc.execCommand('serverparam', '_token', '{{ csrf_token() }}');
    });
</script>
<!--相册-->
{!! Html::style(env("APP_URL").'/assets/backend/file-manage/el-Upload/css/pop.css') !!}
{!! Html::script(env("APP_URL").'/assets/backend/file-manage/bootstrap-treeview/bootstrap-treeview.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/jquery.http.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/page/jquery.pages.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/activity/js/pop.js') !!}
<script>
    $("#upload").on("click", function () {
	    var el_list = $(this);
	    $.addImage(el_list, "selectHighlight");
    });
</script>
<script>
    // 初始化Web Uploader
    $(document).ready(function () {
	    var postImgUrl = '{{route('upload.image',['_token'=>csrf_token()])}}';
	    // 初始化Web Uploader
	    var uploader = WebUploader.create({
		    auto: true,
		    swf: '{{url('assets/backend/libs/webuploader-0.1.5/Uploader.swf')}}',
		    server: postImgUrl,
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
		    $('#activity-poster input').val(response.url);
	    });
	    var uploaderImgList = WebUploader.create({
		    auto: true,
		    swf: '{{url('assets/backend/libs/webuploader-0.1.5/Uploader.swf')}}',
		    server: postImgUrl,
		    pick: '#filePickerImgList',
		    fileVal: 'upload_image',
		    accept: {
			    title: 'Images',
			    extensions: 'jpg,jpeg,png',
			    mimeTypes: 'image/jpg,image/jpeg,image/png'
		    }
	    });
	    // 文件上传成功，给item添加成功class, 用样式标记上传成功。
	    uploaderImgList.on('uploadSuccess', function (file, response) {
		    $('#activity-poster-list img').attr("src", response.url);
		    $('#activity-poster-list input').val(response.url);
	    });
    });
</script>