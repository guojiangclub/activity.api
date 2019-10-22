<div class="form-group">
    <label class="col-md-2 control-label">免责声明：</label>
    <div class="col-md-4">
        <select class="form-control" name="statement_id">
            <option value="0">请选择</option>
            @if(count($statements))
                @foreach($statements as $statement)
                    <option {{isset($model) && isset($model->statement_id) && $model->statement_id && $model->statement_id==$statement->id ? 'selected' : ''}} value="{{$statement->id}}">{{$statement->title}}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>