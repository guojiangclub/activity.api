@foreach($discount->actions as $item)
<div class="form-group">
    <label class="col-sm-2 control-label">减免类型：</label>
    <div class="col-sm-10">
        <input type="hidden" value="{{$item->id}}" name="action_id">

        <select class="form-control m-b action-select" name="action[type]" onchange="actionChange(this)">
            <option value="activity_discount" {{$item->type == 'activity_discount'?'selected':''}}>免费活动通行一次</option>
        </select>
        <input type="hidden" name="action[configuration]" value="free">
    </div>
</div>

<div class="form-group">
    <div class="col-sm-9 col-sm-offset-3" id="promotion-action">

    </div>
</div>
@endforeach