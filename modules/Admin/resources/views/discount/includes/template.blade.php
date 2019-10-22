<!--template for pass-->
<script type="text/x-template" id="rules_template">
    <div class="promotion_rules_box" id="promotion_rules_{NUM}">
        <div class="form-group">
            <label class="col-sm-2 control-label">规则类型：</label>
            <div class="col-sm-10">
                <select class="form-control m-b rules-select" data-num="{NUM}" name="rules[{NUM}][type]"
                        onchange="rulesChange(this)">
                    <option selected="selected" value="contains_activity">指定活动</option>
                    <option value="item_number">活动人次</option>
                    <option value="item_total">订单总金额</option>
                </select>
            </div>
        </div>

        <fieldset id="configuration_{NUM}">
            <div class="form-group">
                <label class="col-sm-2 control-label">指定活动：</label>
                <div class="col-sm-10">
                    <label class="checkbox-inline i-checks"><input name="select_all_activity" value="all"  type="checkbox"> 所有活动</label>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>活动名称</th>
                                <th>类型</th>
                                <th>活动起止时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody class="selected_activity_box">

                            </tbody>
                        </table>
                    </div>

                    <a class="btn btn-success" id="chapter-create-btn" data-toggle="modal"
                       data-target="#activity_modal" data-backdrop="static" data-keyboard="false"
                       data-url="{{route('activity.admin.discount.modal.modalActivity')}}">
                        添加指定活动
                    </a>
                    <input type="hidden" id="selected_activity" name="rules[{NUM}][value][spu]">
                </div>
            </div>
        </fieldset>

        {{--<button type="button" class="col-lg-offset-5 btn btn-w-m btn-danger" onclick="delRules(this)">删除</button>--}}
        <div class="hr-line-dashed"></div>
    </div>
</script>

<!-- specify_activity configuration-->
<script type="text/x-template" id="rules_specify_activity_template">
    <div class="form-group">
        <label class="col-sm-2 control-label">指定活动：</label>
        <div class="col-sm-10">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>活动名称</th>
                        <th>类型</th>
                        <th>活动起止时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody class="selected_activity_box">

                    </tbody>
                </table>
            </div>
            <a class="btn btn-success" id="chapter-create-btn" data-toggle="modal"
               data-target="#activity_modal" data-backdrop="static" data-keyboard="false"
               data-url="{{route('activity.admin.discount.modal.modalActivity')}}">
                添加指定活动
            </a>
        </div>
    </div>
</script>

<!--已选择活动模板-->
<script type="text/html" id="selected_activity_template">
    <tr>
        <td>
            {#title#}
        </td>
        <td>
            {#type_text#}
        </td>
        <td>
            {#starts_at#}<br>
            {#ends_at#}
        </td>
        <td>
            <button class="btn btn-xs btn-danger" data-id="{#id#}" onclick="deleteSelect(this)"><i class="fa fa-trash" data-toggle="tooltip" data-placement="top"
                                                     title="删除活动"></i></button>

        </td>
    </tr>
</script>

<!-- item_total configuration-->
<script type="text/x-template" id="rules_item_total_template">
    <div class="form-group">
        <label class="col-sm-2 control-label">金额：</label>
        <div class="col-sm-10">
            <div class="input-group m-b">
                <span class="input-group-addon">$</span>
                <input class="form-control" type="text" name="rules[{NUM}][value]" placeholder="订单总金额大于或等于输入值时执行所设定的动作">
            </div>
        </div>
    </div>
</script>

<!--item_number configuration-->
<script type="text/x-template" id="rules_item_number_template">
    <div class="form-group">
        <label class="col-sm-2 control-label">数量：</label>
        <div class="col-sm-10">
            <input class="form-control" type="text" name="rules[{NUM}][value]" placeholder="人次大于或等于输入值时执行所设定的动作">
        </div>
    </div>
</script>

<!--order percentage action -->
<script type="text/x-template" id="percentage_action_template">
    <div class="input-group m-b">
        <input class="form-control" type="text" name="action[configuration]" value="">
        <span class="input-group-addon">%</span>
    </div>
</script>
<!--order discount action -->
<script type="text/x-template" id="discount_action_template">
    <div class="input-group m-b">
        <span class="input-group-addon">$</span>
        <input class="form-control" type="text" name="action[configuration]" value="">
    </div>
</script>

<!--point percentage action -->
<script type="text/x-template" id="point_percentage_action_template">
    <div class="input-group m-b">
        <input class="form-control" type="text" name="action[configuration]" value="">
        <span class="input-group-addon">%</span>
    </div>
</script>
<!--point discount action -->
<script type="text/x-template" id="point_action_template">
    <input class="form-control" type="text" name="action[configuration]" value="">
</script>