<div class="ibox float-e-margins">
    <div class="ibox-content" style="display: block;">
        <div class="row">
            <div class="panel-body">
                {!! Form::open( [ 'url' => [route('activity.admin.coach.store', $coach->id)], 'method' => 'POST', 'id' => 'activity.admin.coach.store','class'=>'form-horizontal'] ) !!}
                <div class="form-group">
                    <label class="col-sm-2 control-label">教练ID：</label>
                    <div class="col-sm-10">
                        <label class="control-label">{{$coach->id}}</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">教练称呼：</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="coach_name" placeholder="" value="{{$coach->coach_name}}"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">头衔：</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="title" placeholder="" value="{{$coach->title}}"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">描述：</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="describe" placeholder="" style="resize: none" rows="5">{{$coach->describe}}</textarea>
                    </div>
                </div>

                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit">保存</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>