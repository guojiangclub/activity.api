<div class="form-group">
    <label class="col-md-2 control-label">人数限制
        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="人数限制：不填为不限制，0为不限制，当收费活动含有限制名额数量的电子票时，本设置不生效。"></i>：</label>
    <div class="col-md-4">
        {!! Form::text('member_limit', null, ['class' => 'form-control'] ) !!}
    </div>
</div>