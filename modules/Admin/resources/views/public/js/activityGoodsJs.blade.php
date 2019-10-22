<script>
    function OnInput(event, _self) {
        var that = $(_self);
        var key = that.parent().parent().data('key');

        var r = /^[0-9]+.?[0-9]*$/;
        var rate = event.target.value;
        if (!r.test(rate) || rate <= 1) {
            swal('折扣值必须是正数且大于1');
            that.val('');
            $('input[name="item[' + key + '][price]"]').val('');
            return;
        }

        var price = (that.parent().data('price') * event.target.value / 10).toFixed(2);
        $('input[name="item[' + key + '][price]"]').val(price);
    }

    $('body').on('click', '.switch', function () {
        var show = parseInt($(this).children('input').attr('value'));
        var that = $(this);
        var showObj = $(this).children('input');

        if (show == 1) {
            that.removeClass('fa-toggle-on');
            that.addClass('fa-toggle-off');
            showObj.val(0);
        } else {
            that.removeClass('fa-toggle-off');
            that.addClass('fa-toggle-on');
            showObj.val(1);
        }
    });

    /**
     * 移除所选商品
     * @param _self
     */
    function deleteSelect(_self, action) {
        var dom = $('#selected_spu');
        var btnVal = $(_self).data('id');
        var string = dom.val();
        var ids = string.split(',');
        var index = ids.indexOf(String(btnVal));
        if (!!~index) {
            ids.splice(index, 1);
        }
        var str = ids.join(',');
        dom.val(str);

        if (action == 'update') {
            var delete_dom = $('input[name="delete_item"]');
            var delete_id = $(_self).data('key');
            var delete_str = delete_dom.val() + ',' + delete_id;

            if (delete_str.substr(0, 1) == ',') delete_str = delete_str.substr(1);

            delete_dom.val(delete_str);


        }

        $(_self).parent().parent().remove();

    }
</script>