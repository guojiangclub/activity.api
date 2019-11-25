<div class="form-group">
    <label class="col-sm-2 control-label">优惠条件：</label>
    <div class="col-sm-10">
        {{--<div class="alert alert-danger">--}}
            {{--提示：请勿添加重复的规则类型--}}
        {{--</div>--}}
        <fieldset id="rules_box">
            @foreach($discount->rules as $key => $item)
                <div class="promotion_rules_box" id="promotion_rules_{{$key + 1}}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">规则类型：</label>
                        <div class="col-sm-10">
                            <select class="form-control m-b rules-select" data-num="{{$key + 1}}"
                                    name="rules[{{$key + 1}}][type]" onchange="rulesChange(this)">
                                <option value="contains_activity" {{$item->type == 'contains_activity'?'selected' : ''}}>指定活动
                                </option>
                            </select>
                        </div>
                    </div>

                    <fieldset id="configuration_{{$key + 1}}">
                        @if($item->type == 'contains_activity')
                            <div class="form-group">
                                <label class="col-sm-2 control-label">指定活动：</label>
                                <div class="col-sm-10">
                                    <label class="checkbox-inline i-checks"><input id="select_all_activity" value="all"
                                                                                   {{$item->configuration == 'all'?'checked':''}}
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

                                    <a class="btn btn-success" id="chapter-create-btn"
                                       {{$item->configuration == "all"?"data-toggle='' disabled":"data-toggle=modal"}}
                                       data-target="#activity_modal" data-backdrop="static" data-keyboard="false"
                                       data-url="{{route('activity.admin.discount.modal.modalActivity')}}">
                                        添加指定活动
                                    </a>
                                    <input type="hidden" id="selected_activity" name="rules[1][value]" value="{{$item->configuration}}">
                                </div>
                            </div>
                        @endif
                    </fieldset>

                    {{--<button type="button" class="col-lg-offset-5 btn btn-w-m btn-danger" onclick="delRules(this)">删除--}}
                    {{--</button>--}}
                    <div class="hr-line-dashed"></div>
                </div>
            @endforeach
        </fieldset>


        {{--<div class="form-group">--}}
            {{--<button type="button" id="add-rules" class="btn btn-w-m btn-info">添加规则</button>--}}
        {{--</div>--}}
    </div>
</div>

@include('activity::discount.includes.template')