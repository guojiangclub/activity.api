<div class="form-group">
    <label class="col-md-2 control-label">目标完赛时间：</label>
    <div class="col-md-5" style="padding-left: 0">
        <div class="row">
            <label class="col-md-2 control-label">最小时间：</label>
            <div class="col-md-10">
                <div class="col-xs-5">
                    <div class="input-group">
                        <input type="text" name="finish_min_hours" class="form-control" value="{{isset($model) && $model->finish_min_hours ? $model->finish_min_hours : 0}}">
                        <div class="input-group-addon">小时</div>
                    </div>
                </div>
                <div class="col-xs-1" style="text-align: center; line-height: 30px;">
                    <span>-</span>
                </div>
                <div class="col-xs-5">
                    <div class="input-group">
                        <input type="text" name="finish_min_minutes" class="form-control" value="{{isset($model) && $model->finish_min_minutes ? $model->finish_min_minutes : 0}}">
                        <div class="input-group-addon">分钟</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row padding-top-7">
            <label class="col-md-2 control-label">最大时间：</label>
            <div class="col-md-10">
                <div class="col-xs-5">
                    <div class="input-group">
                        <input type="text" name="finish_max_hours" class="form-control" value="{{isset($model) && $model->finish_max_hours ? $model->finish_max_hours : 0}}">
                        <div class="input-group-addon">小时</div>
                    </div>
                </div>
                <div class="col-xs-1" style="text-align: center; line-height: 30px;">
                    <span>-</span>
                </div>
                <div class="col-xs-5">
                    <div class="input-group">
                        <input type="text" name="finish_max_minutes" class="form-control" value="{{isset($model) && $model->finish_max_minutes ? $model->finish_max_minutes : 0}}">
                        <div class="input-group-addon">分钟</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>