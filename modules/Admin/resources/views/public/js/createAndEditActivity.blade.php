<script>
    $(function () {
        $(".vim-button-box .vim-button-box-item-activity-category").on('click', function () {
            var $thisPoint = $(this);
            $thisPoint.addClass("btn-success");
            $thisPoint.siblings().removeClass("btn-success");
        });
        $(".create-activity-toggle-page").on('click', function () {
            $("#activity-set-category").toggle();
            $("#edit-activity-detail").toggle();
        });
        $("#entry-end-at-check").on('ifChecked', function () {
            var val = $("input[name=starts_at]").val();
            $("input[name=entry_end_at]").val(val);
        });
        $("#entry-end-at-check").on('ifUnchecked', function () {
            $("input[name=entry_end_at]").val("");
        });

        $("#activity-payment-type-offline-charge").on('ifToggled', function () {
            if ($(this).is(':checked')) {
                $('#offline-charge-detail').show();
            } else {
                var oc_detail = $('#offline-charge-detail');
                oc_detail.hide();
                $('input[name="payment-offline-charge"]').val('');
            }
        });


        $("#activity-payment-type-charge").on('ifToggled', function () {
            if ($(this).is(':checked')) {
                $('#activity-payment-detail').show();
            } else {
                var thisPoint = $('#activity-payment-detail');
                thisPoint.hide();
                thisPoint.find("#activity-payment-lists").empty();
            }
        });

        $("#add-activity-payment-item").on("click", function () {
            var dataObj = new Object();
            dataObj.paymentIndex = $("#activity-payment-lists").children("tr").length + 1;
            var html = $.convertTemplate('#payment-item-template', dataObj, '');
            $("#activity-payment-lists").append(html);
        });

        $('body').on('click', '.del_payment_cell', function () {
            $(this).parent('td').parent('tr').remove();
        });

        $('.switch_payment_status').on('click', function () {
            var status = $(this).children('i').attr('data-status');
            if (status == 1) {
                $(this).children('i').removeClass('fa-toggle-on');
                $(this).children('i').addClass('fa-toggle-off');
                $(this).children('i').attr('data-status', 0);
                $(this).children('i').children('input:hidden').val(0);
                $(this).prev('div').prev('label').children('div').children('.activity_form_checkbox').iCheck('uncheck');
            } else {
                $(this).children('i').removeClass('fa-toggle-off');
                $(this).children('i').addClass('fa-toggle-on');
                $(this).children('i').attr('data-status', 1);
                $(this).children('i').children('input:hidden').val(1);
            }
        });

        $("#activity-obtain-point").on('ifChecked', function () {
            $("#activity-point-detail").show();
        });

        $(".activity_form_checkbox").on('ifChecked', function () {
            $(this).val(1);
        });

        $(".activity_form_checkbox").on('ifUnchecked', function () {
            $(this).val(0);
        });

        $("#activity-obtain-point").on('ifUnchecked', function () {
            $("#activity-point-detail").hide();
        });

        $("#activity-support-refund").on('ifChecked', function () {
            var val = $("#refund-template").text();
            $("#activity-refund").append(val);
            $("#activity-refund").show();
        });
        $("#activity-support-refund").on('ifUnchecked', function () {
            $("#activity-refund").empty();
            $("#activity-refund").hide();
        });

	    $("#activity-package-settings").on('ifChecked', function () {
		    $(".package_settings").hide();
	    });
	    $("#activity-package-settings").on('ifUnchecked', function () {
		    $(".package_settings").show();
	    });

        $("#activity-payment-type-pass").on('ifChecked', function () {
            $('#refund-detail').hide();
        });

        $("#activity-payment-type-pass").on('ifUnchecked', function () {
            $('#refund-detail').show();
        });

        function getFeeDiscount() {
            fee_discount = 'yes';
            //以下代码暂时注释20180309
            {{--$.ajax({--}}
                {{--type: 'POST',--}}
                {{--url: '{{route('activity.admin.filterFreeDiscount')}}',--}}
                {{--data: {_token: $('meta[name="_token"]').attr('content')},--}}
                {{--async: false,--}}
                {{--success: function (result) {--}}
                    {{--if (result.status) {--}}
                        {{--fee_discount = 'yes';--}}
                    {{--} else {--}}
                        {{--fee_discount = 'no';--}}
                    {{--}--}}
                {{--}--}}
            {{--});--}}
        }

        $(".vim-activity-store").on('click', function () {
            if ($(this).attr('data-status') != 1) {
                swal('错误', '请勿重复发布', 'error');
                return false;
            }

            $('.vim-activity-store').attr('data-status', 0);
            getFeeDiscount();
            var pay_type_radio = $("input[name='activity_payment_radio']:checked").val();

            var status = parseInt($(this).val());
            var html = "";
            if (fee_discount == 'yes' || pay_type_radio == 'OFFLINE_CHARGES' || pay_type_radio == 'CHARGING') {
                html = "<input type='hidden' name='status' value=" + status + " />";
                $("#store-activity-form").append(html);
                $("#store-activity-form").trigger("submit");
            } else {
                html = "<input type='hidden' name='status' value='0' />";
                $("#store-activity-form").append(html);
                swal({
                            title: "警告！",
                            text: "当前暂无可用活动通行证，请保存之后创建通行证",
                            type: "warning",
                            showCancelButton: false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "确认",
                            closeOnConfirm: false
                        },
                        function () {
                            $("#store-activity-form").trigger("submit");
                        });
            }


        });
        $('#store-activity-form').ajaxForm({
            beforeSend: function () {
                $('.vim-activity-store').attr('disabled', true);
            },
            success: function (result) {
                $('.vim-activity-store').attr('data-status', 1);
                $('.vim-activity-store').attr('disabled', false);
                if (!result.status) {
                    swal("保存失败!", result.error, "error")
                } else {
                    swal({
                        title: "保存成功！",
                        text: "",
                        type: "success"
                    }, function () {
                        window.location = '{!!route('activity.admin.index')!!}';
                    });
                }
            },
            error: function () {
                $('.vim-activity-store').attr('data-status', 1);
                $('.vim-activity-store').attr('disabled', false);
                swal("保存失败!", '服务器内部错误', "error")
            }
        });

        $("#vim-search-coach-by-name").keyup(function () {
            var val = $(this).val();
            if (val) {
                var coachObjs = $("div.vim-modal-coach-list[value*=" + val + "]");
                $("div.vim-modal-coach-list").hide();
                coachObjs.show();
            } else {
                $("div.vim-modal-coach-list").show();
            }
        });

        $("#vim-store-selected-coach").on('click', function () {
            var checkedCoach = $(".vim-modal-coach-list input:checked");
            checkedCoach.each(function () {
                var parentNode = $(this).closest(".vim-modal-coach-list");
                var dataObj = new Object();

                if ($("#vim-activity-selected-coach input[value=" + parentNode.find("input[name=user_id]").val() + "]").length == 0) {
                    dataObj.name = parentNode.find("input[name=name]").val();
                    dataObj.phone = parentNode.find("input[name=mobile]").val();
                    dataObj.avatar = parentNode.find("img.img").attr('src');
                    dataObj.id = parentNode.find("input[name=user_id]").val();
                    var html = $.convertTemplate('#select-coach-template', dataObj, '');
                    $("#vim-activity-selected-coach").append(html);
                }

            });
            $('#coach-list').modal('toggle');
        });
        $('body').on('click', '.delete-coach-i', function () {
            $(this).closest('.vim-coach-list-item').remove();
        });

    });
</script>
