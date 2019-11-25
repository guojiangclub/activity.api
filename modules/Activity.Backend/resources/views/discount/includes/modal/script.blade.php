<script>
    var paraActivity = {_token: $('meta[name="_token"]').attr('content')};
    function changeSelect(_self) {
        var dom = $('#temp_selected_activity');

        if ($(_self).hasClass('select')) {

            var btnVal = $(_self).data('id');
            var string = dom.val();
            var ids = string.split(',');
            var index = ids.indexOf(String(btnVal));

            if (!!~index) {
                ids.splice(index, 1);
            }

            var str = ids.join(',');
            $(_self).removeClass('select btn-info').addClass('btn-warning unselect').find('i').removeClass('fa-check').addClass('fa-times');

        } else {
            var btnVal = $(_self).data('id');
            var str = dom.val() + ',' + btnVal;

            if (str.substr(0, 1) == ',') str = str.substr(1);

            $(_self).addClass('select btn-info').removeClass('btn-warning unselect').find('i').addClass('fa-check').removeClass('fa-times');

        }
        dom.val(str);
        paraActivity.ids = str;
    }


    function sendIds() {
        var dom = $('#temp_selected_activity');
        $('#selected_activity').val(dom.val());

        sendData();

        $('#activity_modal').modal('hide');
    }


</script>