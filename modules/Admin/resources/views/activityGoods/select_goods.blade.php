@foreach($goods as $key=>$item)
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
            <input type="text" class="form-control" name="item[{{$num+$key}}][rate]" oninput="OnInput(event,this)"
                   placeholder="如7折，直接输入数字7">
        </td>
        <td><input type="text" class="form-control" name="item[{{$num+$key}}][price]" readonly></td>
        <td>
            <a>
                <i class="fa switch fa-toggle-on"
                   title="切换状态">
                    <input type="hidden" value="1" name="item[{{$num+$key}}][required]">
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