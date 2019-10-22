<table class="table table-hover table-striped">
    <tbody>
    <tr>
        <th>活动订单号</th>
        <th>下单会员</th>
    </tr>
    <tr>
        <td>{{$member->order_no}}</td>
        <td><a href="{{route('admin.users.edit',['id' => $member->user->id])}}"
               target="_blank">@if($member->user->name) {{$member->user->name}} @elseif($member->user->nick_name) {{$member->user->nick_name}}@else @endif</a></td>
    </tr>
    <tr>
        <th>订单状态</th>
        <th>下单时间</th>
    </tr>
    <tr>
        <td>
            @if($member->status==0)
                待支付
            @elseif($member->status==1)
                已报名
            @elseif($member->status==2)
                已签到
            @elseif($member->status==3)
                已取消
            @elseif($member->status==4)
                待审核
            @endif
        </td>
        <td>{{$member->created_at}}</td>
    </tr>
    <tr>
        <th>活动状态</th>
        <th>教练</th>
    </tr>
    <tr>
        <td>
            @if($member->activity->status==1)
                报名中
            @elseif($member->activity->status==2)
                已开始
            @elseif($member->activity->status==3)
                已结束
            @endif
        </td>
        <td>
            {{$coach}}
        </td>
    </tr>
    @if(($member->payment->type==1 || $member->payment->type==2) && ($member->pay_status==1 || $member->pay_status==2))
        <tr>
            <th>支付平台</th>
            <th>支付金额</th>
        </tr>
        <tr>
            <td>{{isset($member->paymentDetail) && isset($member->paymentDetail->channel) ? $member->paymentDetail->channel_text : ''}}</td>
            <td>{{isset($member->paymentDetail) && isset($member->paymentDetail->amount) ? $member->paymentDetail->amount / 100 : ''}}</td>
        </tr>
        <tr>
            <th>pingxx交易号</th>
            <th>支付平台交易流水号</th>
        </tr>
        <tr>
            <td>{{isset($member->paymentDetail) && isset($member->paymentDetail->pingxx_no) ? $member->paymentDetail->pingxx_no : ''}}</td>
            <td>{{isset($member->paymentDetail) && isset($member->paymentDetail->channel_no) ? $member->paymentDetail->channel_no : ''}}</td>
        </tr>
    @endif
    </tbody>
</table>




