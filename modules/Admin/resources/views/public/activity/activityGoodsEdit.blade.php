<input type="hidden" id="selected_spu">

<div class="form-group">
    <label class="col-md-2 control-label">活动关联商品：</label>
    <div class="col-md-8">
        @if($model->status==0)
            <a class="btn btn-success" id="chapter-create-btn" data-toggle="modal"
               data-target="#modal" data-backdrop="static" data-keyboard="false"
               data-url="{{route('admin.activity.getSpu')}}">
                添加商品
            </a>
        @endif
    </div>
</div>

<div class="form-group">
    <div class="col-md-8 col-md-offset-2">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>商品信息</th>
                <th width="100">吊牌价(元)</th>
                <th width="180">折扣</th>
                <th width="100">折后价(元)</th>
                <th width="80">是否必选</th>
                <th width="50">操作</th>
            </tr>
            </thead>
            <tbody id="select-goods-box">
            @if($num=count($model->goods)>0)
                @foreach($model->goods as $key=>$item)
                    <tr data-key="{{$num+$key}}">
                        <td>
                            <input type="hidden" name="item[{{$num+$key}}][goods_id]" value="{{$item->id}}">
                            <img src="{{$item->img}}" width="50">
                            <br>
                            <a href="{{route('admin.goods.edit',['id'=>$item->id])}}" target="_blank">
                                {{$item->name}}
                            </a>
                        </td>
                        <td>{{$item->market_price}}</td>
                        <td data-price="{{$item->market_price}}">
                            <input type="text" class="form-control" name="item[{{$num+$key}}][rate]"
                                   {{$model->status!=0?'readonly':''}}
                                   value="{{$item->pivot->rate}}"
                                   oninput="OnInput(event,this)"
                                   placeholder="如7折，直接输入数字7">
                        </td>
                        <td><input type="text" class="form-control" name="item[{{$num+$key}}][price]" readonly
                                   value="{{$item->pivot->price}}"></td>
                        <td>
                            <a>
                                <i class="fa {{$model->status!=0?'':'switch'}} {{$item->pivot->required?'fa-toggle-on':'fa-toggle-off'}}"
                                   title="切换状态">
                                    <input type="hidden" value="1"
                                           name="item[{{$num+$key}}][required]">
                                </i>
                            </a>
                        </td>

                        <td>

                            <a class="btn btn-xs btn-danger" onclick="deleteSelect(this)" data-id="{{$item->id}}"
                               href="javascript:;">
                                <i data-toggle="tooltip" data-placement="top"
                                   class="fa fa-trash"
                                   title="删除"></i></a>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>

    </div>
</div>