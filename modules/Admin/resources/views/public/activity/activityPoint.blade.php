<div class="form-group">
    <label class="col-md-2 control-label">报名奖励积分：</label>
    <div class="col-md-4" style="min-width: 400px;">
        {!! Form::text('point_join', !empty($point['join']) ? $point['join']->value : null, ['class' => 'form-control', 'placeholder' => "不填 或 0 表示不使用此规则"] ) !!}
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label">签到奖励积分：</label>
    <div class="col-md-4" style="min-width: 400px;">
        {!! Form::text('point_sign', !empty($point['sign']) ? $point['sign']->value : null, ['class' => 'form-control', 'placeholder' => "不填 或 0 表示不使用此规则"] ) !!}
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label">教练奖励积分总额上限：</label>
    <div class="col-md-4" style="min-width: 400px;">
        {!! Form::text('point_rewards', !empty($point['rewards']) ? $point['rewards']->value : null, ['class' => 'form-control', 'placeholder' => "发放奖励的活动成员的积分总额不得超过此数值"] ) !!}
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label">教练奖励积分期限：</label>
    <div class="col-md-4" style="min-width: 400px;">
        {!! Form::text('point_rewards_limit', !empty($point['rewards']) ? $point['rewards']->limit : null, ['class' => 'form-control', 'placeholder' => "教练需在活动结束多少天之内发放奖励"] ) !!}
    </div>
</div>
