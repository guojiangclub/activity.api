<div class="row padding-top-7 items_control">
    <label class="col-md-2 control-label input-title" data-toggle="modal" data-target="#input_{{$field['index']}}_modal"><span class="input_{{$field['index']}}_title" title="点击修改">{{$field['title']}}</span>：</label>
    <div class="col col-md-3">
        <textarea class="form-control"></textarea>
    </div>
    <div class="col col-md-4 padding-top-7">
        <div class="col-md-3"><a title="下移" class="btn btn-xs btn-success move-items" data-action="down" style="cursor:pointer;"><i class="fa fa-angle-down"></i></a>&nbsp;<a title="上移" class="btn btn-xs btn-success move-items" data-action="up" style="cursor:pointer;"><i class="fa fa-angle-up"></i></a></div>&nbsp;@if(!isset($model))<a class="btn btn-xs btn-danger remove-items" data-name="{{$field['name']}}" style="cursor:pointer;"><i class="fa fa-trash"></i></a>@endif&nbsp;&nbsp;是否显示：<a class="switch-status" data-status="{{$field['status']==1 ? 'on' : 'off'}}" title="是否显示"><i class="fa switch fa-toggle-{{$field['status']==1 ? 'on' : 'off'}}"></i></a><input type="hidden" name="activityForm[input_{{$field['index']}}][status]" value="{{$field['status']}}">&nbsp;&nbsp;必填：<input type="checkbox" class="is_necessary" name="activityForm[input_{{$field['index']}}][is_necessary]" value="{{$field['is_necessary']}}" @if($field['is_necessary']==1) checked @endif>
    </div>
    <input type="hidden" name="activityForm[input_{{$field['index']}}][type]" value="{{$field['type']}}" />
    <input type="hidden" name="activityForm[input_{{$field['index']}}][index]" value="{{$field['index']}}" />
    <input type="hidden" class="input_{{$field['index']}}_name" name="activityForm[input_{{$field['index']}}][name]" value="{{$field['name']}}">
    <input class="input_{{$field['index']}}_title" type="hidden" name="activityForm[input_{{$field['index']}}][title]" value="{{$field['title']}}">
    <input type="hidden" name="activityForm[input_{{$field['index']}}][value]" value=""/>
    <div class="modal fade" id="input_{{$field['index']}}_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">修改属性</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-md-3 control-label">字段名称：</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control input_{{$field['index']}}_title" value="{{$field['title']}}" placeholder="多行文本框" oninput="updateTitle('input_{{$field['index']}}_title', this)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">确定</button>
                </div>
            </div>
        </div>
    </div>
</div>