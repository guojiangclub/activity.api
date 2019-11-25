<div class="form-group">
    <label class="col-sm-2 control-label">优惠条件：</label>
    <div class="col-sm-10">
        {{--<div class="alert alert-danger">--}}
            {{--提示：请勿添加重复的规则类型--}}
        {{--</div>--}}
        <fieldset id="rules_box">
            <div class="promotion_rules_box" id="promotion_rules_1">
                <div class="form-group">
                    <label class="col-sm-2 control-label">规则类型：</label>
                    <div class="col-sm-10">
                        <select class="form-control m-b rules-select" data-num="1" name="rules[1][type]"
                                onchange="rulesChange(this)">
                            <option selected="selected" value="contains_activity">指定活动</option>
                        </select>
                    </div>
                </div>

                <fieldset id="configuration_1">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">指定活动：</label>
                        <div class="col-sm-10">
                            <label class="checkbox-inline i-checks"><input id="select_all_activity" value="all"
                                                                           type="checkbox"> 所有活动</label>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="blue-bg">
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
                            <input type="hidden" id="selected_activity" name="rules[1][value]">
                        </div>
                    </div>
                </fieldset>

                {{--<button type="button" class="col-lg-offset-5 btn btn-w-m btn-danger" onclick="delRules(this)">删除--}}
                {{--</button>--}}
                <div class="hr-line-dashed"></div>
            </div>
        </fieldset>


        {{--<div class="form-group">--}}
            {{--<button type="button" id="add-rules" class="btn btn-w-m btn-info">添加规则</button>--}}
        {{--</div>--}}
    </div>
</div>

@include('activity::discount.includes.template')