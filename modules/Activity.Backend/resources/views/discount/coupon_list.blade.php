<div class="ibox float-e-margins">
    <div class="ibox-content" style="display: block;">

        @if($coupons->count()>0)
            <div class="box-body table-responsive">
                <table class="table table-hover table-bordered">
                    <tbody>
                    <!--tr-th start-->
                    <tr>
                        <th>领取时间</th>
                        <th>优惠券码</th>
                        <th>领取用户</th>
                        <th>是否使用</th>
                        <th>使用时间</th>
                    </tr>
                    <!--tr-th end-->
                    @foreach ($coupons as $coupon)
                        <tr>
                            <td>{{$coupon->created_at}}</td>
                            <td>{{$coupon->code}}</td>
                            <td>{{$coupon->user->name ? $coupon->user->name :$coupon->user->mobile}} </td>
                            <td>
                                @if(empty($coupon->used_at))
                                    <label class="label label-danger">No</label>
                                @else
                                    <label class="label label-success">Yes</label>
                                @endif
                            </td>
                            <td>{{$coupon->used_at}}</td>

                    @endforeach
                    </tbody>
                </table>
            </div><!-- /.box-body -->

            <div class="pull-left">
                &nbsp;&nbsp;共&nbsp;{!! $coupons->total() !!} 条记录
            </div>

            <div class="pull-right">
                {!! $coupons->render() !!}
            </div>
        @else
            &nbsp;&nbsp;&nbsp;当前无数据
        @endif

        <div class="box-footer clearfix">
        </div>
    </div>

</div>