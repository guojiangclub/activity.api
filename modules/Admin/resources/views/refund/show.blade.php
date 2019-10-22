<div class="tabs-container">
    <ul class="nav nav-tabs">
        <li class="active"><a aria-expanded="true" data-toggle="tab" href="#tab-1">申请详情</a></li>
        <li class=""><a aria-expanded="false" data-toggle="tab" href="#tab-2"> 操作日志</a></li>
    </ul>

    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
            <div class="panel-body">
                @include('activity::refund.include.refund_detail')
            </div>
        </div>

        <div id="tab-2" class="tab-pane">
            <div class="panel-body">
                @include('activity::refund.include.refund_log')
            </div>
        </div>

    </div>

</div>


{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.form.min.js') !!}
<script>
    $('#base-form').ajaxForm({
        success: function (result) {
            if (result.status) {
                swal({
                    title: "操作成功！",
                    text: "",
                    type: "success"
                }, function () {
                    location.reload();
                });
            } else {
                swal('操作失败', '', 'error');
            }

        }
    });
</script>