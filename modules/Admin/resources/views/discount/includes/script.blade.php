{!! Html::script(env("APP_URL").'/assets/backend/libs/formvalidation/dist/js/formValidation.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/formvalidation/dist/js/framework/bootstrap.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/formvalidation/dist/js/language/zh_CN.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.form.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/Tagator/fm.tagator.jquery.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.zh-CN.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/linkchecked/el.linkchecked.js') !!}

<script>

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



    $("input[class='type']").on('ifUnchecked', function (event) {
       $(this).parent().parent().find('input[type="text"]').val('');
    });

    //兑换码切换
    $("input[name='base[coupon_based]']").on('ifClicked', function (event) {
       var value = $(this).val();
        if(value == 1){
            $('#code').show();
        }else{
            $('#code').hide();
        }
    });

    //类型切换
    $("input[name='base[type]']").on('ifClicked', function (event) {
        var value = $(this).val();
        if(value == 1){
            $('#code').hide();
            $('#coupon').show();
        }else{
            $('#coupon').hide();
            $('#code').show();
        }
    });


    /*获取已选择活动*/
    function sendData() {
        var postUrl = '{{route('activity.admin.discount.getSelectedActivity')}}';

        var selected_spu = $('#selected_activity').val();

        $.ajax({
            type: 'POST',
            url: postUrl,
            data:{
                _token: $('meta[name="_token"]').attr('content'),
                ids: selected_spu
            },
            success: function(result){
                var html = '';
                result.data.forEach(function (item) {
                    html += $.convertTemplate('#selected_activity_template', item, '');
                });
                $('.selected_activity_box').html(html);
            }});

    }


    //添加规则
    var rules_html = $('#rules_template').html();
    $('#add-rules').click(function() {
        //var check_type = $("input[name='base[type]']:checked").val();

        var num = $('.promotion_rules_box').length;
        $('#rules_box').append(rules_html.replace(/{NUM}/g, num+1));
    });

    //删除操作
    function delRules(_self){
        $(_self).parent().remove();
    }

    //删除已选择的活动
    function deleteSelect(_self) {
        var dom = $('#selected_activity');
        var btnVal = $(_self).data('id');

        var string = dom.val();
        var ids = string.split(',');
        var index = ids.indexOf(String(btnVal));

        if(!!~index)
        {
            ids.splice(index, 1);
        }
        var str = ids.join(',');
        dom.val(str);
        $('#temp_selected_activity').val(str);

        $(_self).parent().parent().remove();
    }

    //选择所有活动切换
    $("input[id='select_all_activity']").on('ifChanged', function () {
        var chapter_btn = $('#chapter-create-btn');
        if($(this).is(':checked'))
        {
            $('#selected_activity').val($(this).val());
            chapter_btn.attr('disabled',true);
            chapter_btn.attr('data-toggle','');
        }else{
            $('#selected_activity').val($('#temp_selected_activity').val());
            chapter_btn.attr('disabled',false);
            chapter_btn.attr('data-toggle','modal');
        }

    });

    //rules select下拉动作
    function rulesChange(_self) {
        var value = $(_self).children('option:selected').val();
        var num = $(_self).data('num');
        var configuration_html;

        if (value == 'contains_activity') {
            configuration_html = $('#rules_specify_activity_template').html();
            $('#promotion_rules_' + num).find('#configuration_' + num).html(configuration_html.replace(/{NUM}/g, num));
            $('#select_all_activity').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
                increaseArea: '20%'
            });
        }

        if (value == 'item_total') {
            configuration_html = $('#rules_item_total_template').html();
            $('#promotion_rules_' + num).find('#configuration_' + num).html(configuration_html.replace(/{NUM}/g, num));
        }

        if (value == 'item_number') {
            configuration_html = $('#rules_item_number_template').html();
            $('#promotion_rules_' + num).find('#configuration_' + num).html(configuration_html.replace(/{NUM}/g, num));
        }

    }


    function actionChange(_self) {
        var value = $(_self).children('option:selected').val();
        var action_html;

        if(value =='order_fixed_discount')
        {
            action_html = $('#discount_action_template').html();
            $('#order-discount-action').html(action_html);
        }

        if(value=='order_percentage_discount')
        {
            action_html = $('#percentage_action_template').html();
            $('#order-discount-action').html(action_html);
        }

        if(value=='point_percentage_discount')
        {
            action_html = $('#point_percentage_action_template').html();
            $('#point-discount-action').html(action_html);
        }

        if(value=='point_discount')
        {
            action_html = $('#point_action_template').html();
            $('#point-discount-action').html(action_html);
        }
    }





</script>