<div class="form-group">
    <label for="" class="col-md-2 control-label">
        <span style="color: red">*</span>活动开始时间：
    </label>
    <div class="col-md-7">
        <div class="col-md-1 padding-clear padding-top-7" style="min-width: 220px;">
            <div class="date form_datetime">
                <span class="add-on"><i class="fa fa-calendar"></i></span>
                {!! Form::text('starts_at', null, ['class' => '', 'placeholder' => "手动选择开始时间", "readonly", "required"]) !!}
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="" class="col-md-2 control-label">
        <span style="color: red">*</span>活动结束时间：
    </label>
    <div class="col-md-7">
        <div class="col-md-1 padding-clear padding-top-7" style="min-width: 220px;">
            <div class="date form_datetime">
                <span class="add-on"><i class="fa fa-calendar"></i></span>
                {!! Form::text('ends_at', null, ['class' => '', 'placeholder' => "手动选择结束时间", "readonly", "required"]) !!}
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="" class="col-md-2 control-label">
        <span style="color: red">*</span>报名截止时间：
    </label>

    <div class="col-md-7">
        <div class="col-md-1 padding-clear padding-top-7" style="min-width: 220px;">
            <div class="date form_datetime" id="entry-end-at">
                <span class="add-on"><i class="fa fa-calendar"></i></span>
                {!! Form::text('entry_end_at', null, ['class' => '', 'placeholder' => "报名截止时间", "readonly" , "required"] ) !!}
            </div>
        </div>
        <div class="col-md-1 padding-clear padding-top-7" style="min-width: 220px;">
            <input type="checkbox" class="checkbox" id="entry-end-at-check" {{isset($model) AND $model->starts_at == $model->entry_end_at ? "checked" : ""}}><span> 设置为活动开始时间</span>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label">报名人数：</label>
    <div class="col-md-5">
        {!! Form::text('member_count', null, ['class' => 'form-control', 'placeholder' => "报名人数"] ) !!}
    </div>
</div>

<div class="form-group">
    <label for="" class="col-md-2 control-label">
        延迟签到时间：
    </label>
    <div class="col-md-7">
        {!! Form::text('delay_sign', null, ['class' => 'form-control', 'placeholder' => "延迟签到时间"] ) !!}
        <i>活动结束后 N 分钟可继续签到</i>
    </div>
</div>
