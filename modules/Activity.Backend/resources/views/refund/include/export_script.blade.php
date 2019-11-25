<script type="text/javascript">
    $('.refund_status').change(function () {
        var sel_status = $(this).val();
        if (sel_status == 2) {
            $('#complete_time').show();
        } else {
            $('#complete_time').hide();
        }
    });


    $('#refunds_xls').on('click', function () {
        var url = '{{route('admin.activity.refund.export')}}';
        var refund_status = $('.refund_status').val();
        var stime = $('input[name="stime"]').val();
        var etime = $('input[name="etime"]').val();
        var c_stime = '';
        var c_etime = '';

        if (refund_status == 2) {
            c_stime = $('input[name="c_stime"]').val();
            c_etime = $('input[name="c_etime"]').val();
        }


        url = url + '?refund_status=' + refund_status + '&stime=' + stime + '&etime=' + etime + '&c_stime=' + c_stime + '&c_etime=' + c_etime;

        window.location.href = url;
    });
</script>