@extends('store-backend::layouts.bootstrap_modal')

@section('modal_class')
    modal-lg
@stop
@section('title')
    选择商品
@stop

@section('after-styles-end')
    {!! Html::style(env("APP_URL").'/assets/backend/libs/ladda/ladda-themeless.min.css') !!}
@stop


@section('body')
    <div class="row">

        <div class="col-md-12 clearfix">
            <div class="col-sm-3">
                <select class="form-control" name="field">
                    <option value="name" {{!empty(request('field')=='name')?'selected ':''}}>商品名称</option>
                    <option value="goods_no" {{!empty(request('field')=='goods_no')?'selected ':''}}>商品编码</option>
                </select>
            </div>

            <div class="col-sm-7">
                <input type="text" name="value" placeholder="Search"
                       value="{{!empty(request('value'))?request('value'):''}}" class=" form-control">
            </div>
            <div class="col-sm-2">
                <button type="button" id="send" class="ladda-button btn btn-primary">搜索
                </button>

            </div>
        </div>


        <div class="clearfix"></div>
        <div class="hr-line-dashed "></div>

        <div class="panel-body">
            <h3 class="header">请选择商品：</h3>
            <div class="table-responsive" id="goodsList">
                <table class="table table-hover table-striped">
                    <thead>
                    <!--tr-th start-->
                    <tr>
                        <th>商品名称</th>
                        <th>销售价</th>
                        <th>库存</th>
                        <th>操作</th>
                    </tr>
                    <!--tr-th end-->
                    </thead>

                    <tbody class="page-goods-list">

                    </tbody>
                </table>
            </div><!-- /.box-body -->

            <div class="pages">

            </div>
        </div>
    </div>


    <script type="text/html" id="page-temp">
        <tr>
            <td>
                <img src="{#img#}" alt="" style="width: 30px; height: 30px"> &nbsp;
                {#name#}
            </td>
            <td>
                {#sell_price#}
            </td>
            <td>
                {#store_nums#}
            </td>
            <td>
                {#note#}
                <button onclick="changeSelect(this)" class="btn btn-circle {#class#} {#hidden#}"
                        type="button" data-id="{#id#}"><i class="fa fa-{#icon#}"></i>
                </button>
            </td>
        </tr>
    </script>
@stop
{!! Html::script(env("APP_URL").'/assets/backend/libs/ladda/spin.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/ladda/ladda.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/ladda/ladda.jquery.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/loader/jquery.loader.min.js') !!}


@section('footer')
    <input type="hidden" id="temp_selected_spu">
    <input type="hidden" id="temp_exclude_spu">

    <button type="button" class="btn btn-link" data-dismiss="modal">取消</button>

    <button type="button" onclick="sendIds();" class="ladda-button btn btn-primary"> 确定
    </button>

    @include('activity::activityGoods.script')
    {!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/common.js') !!}
    {!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/jquery.http.js') !!}
    {!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/page/jquery.pages.js') !!}
    <script>
        var paraDiscount = {_token: _token};

        function getList() {

            var postUrl = '{{route('admin.activity.getSpuData')}}';

            $('.pages').pages({
                page: 1,
                url: postUrl,
                get: $.http.post.bind($.http),
                body: {
                    _token: _token,
                    ids: paraDiscount.ids,
                    id: $('input[name="id"]').val(),
                    price: $("select[name=price] option:selected").val(),
                    price_begin: $("input[name=price_begin]").val(),
                    price_end: $("input[name=price_end]").val(),
                    store_begin: $("input[name=store_begin]").val(),
                    store_end: $("input[name=store_end]").val(),
                    field: $("select[name=field] option:selected").val(),
                    value: $("input[name=value]").val()
                },
                marks: {
                    total: 'data.last_page',
                    index: 'data.current_page',
                    data: 'data'
                }
            }, function (data) {
                var html = '';
                var ids = data.ids;

                data.data.forEach(function (item) {
                    item.note = '';
                    if (!~ids.indexOf(String(item.id))) {
                        item.class = 'btn-warning unselect';
                        item.icon = 'times';

                    } else {

                        item.class = 'hidden';
                        item.note = '<i class="red-bg">已加入</i>';
                    }

                    html += $.convertTemplate('#page-temp', item, '');
                });
                $('.page-goods-list').html(html);
            });
        }

        $(document).ready(function () {
            var temp_selected_spu = $('#temp_selected_spu');
            temp_selected_spu.val($('#selected_spu').val());
            paraDiscount.ids = temp_selected_spu.val();

            getList();
        });

        $('#send').on('click', function () {
            getList();
        });
    </script>
@stop






