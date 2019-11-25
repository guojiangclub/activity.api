<script type="text/javascript">
    $('#activity_selector').select2({
        placeholder: '请选择',
        allowClear: true
    });

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

    $('.export-apply').on('click', function () {
        var url = '{{ route('activityOrder.admin.index') }}';
        var stime = $('input[name="stime"]').val();
        var etime = $('input[name="etime"]').val();
        var activity_id = $('[name="activity_id"]').val();
        var status = '{{ request('status') }}';
        var value = $('[name="value"]').val();
        var excel = $(this).attr('data-status');

        if (status <= 0) {
            status = '';
        }

        if(excel==3 && activity_id <= 0){
            swal("", "请选择一个活动", "warning");
            return false;
        }

        if (activity_id <= 0) {
            activity_id = '';
        }

        var payment_id = $('[name="payment_id"]').val();
        if (payment_id <= 0) {
            payment_id = '';
        }

        var field = $('[name="field"]').val();
        if (field <= 0) {
            field = '';
        }

        url = url + '?activity_id=' + activity_id + '&stime=' + stime + '&etime=' + etime + '&payment_id=' + payment_id + '&field=' + field + '&value=' + value + '&excel=' + excel + '&status=' + status;
        window.location.href = url;
    });

    var search_activity_id = '{{ request('activity_id') }}';
    if (search_activity_id) {
        loadPayments(search_activity_id);
    }


    $('[name="activity_id"]').change(function () {
        var activity_id = $(this).val();
        loadPayments(activity_id);
    });

    function loadPayments(activity_id) {
        $.ajax({
            type: 'POST',
            url: '{{ route('activity.admin.payment.select', ['_token'=>csrf_token()]) }}',
            data: 'activity_id=' + activity_id,
            dataType: 'json',
            success: function (res) {
                if (res.status) {
                    $('[name="payment_id"]').html(res.data.html);
                    var payment_id = '{{request('payment_id')}}';
                    if (payment_id) {
                        $('[name="payment_id"]').find('option').each(function () {
                            if ($(this).val() == payment_id) {
                                $(this).attr('selected', 'selected');
                            }
                        });
                    }
                }
            }
        });
    }


    $('.checkbox').on('ifChecked', function (event) {
        var val = $(this).val();
        $(this).parents('.order' + val).addClass('selected');
    });

    $('.checkbox').on('ifUnchecked', function (event) {
        var val = $(this).val();
        $(this).parents('.order' + val).removeClass('selected');
    });


    $('.change-status').on('click', function () {
        var changeUrl = $(this).data('href') + "?_token=" + $('meta[name="_token"]').attr('content');
        swal({
            title: $(this).data('title'),
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "确认",
            cancelButtonText: '取消',
            closeOnConfirm: false
        }, function () {
            $.post(changeUrl, function (res) {
                if (res.status) {
                    swal({
                        title: "操作成功！",
                        text: "",
                        type: "success"
                    }, function () {
                        location.reload();
                    });
                } else {
                    swal(res.message, "", "error");
                }
            });
        });
    });
</script>