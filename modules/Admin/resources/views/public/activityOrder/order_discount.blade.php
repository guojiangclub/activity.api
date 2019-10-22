<table class="table table-hover table-striped">
    <tbody>

    <tr>
        <th>优惠金额</th>
        <th>优惠的积分</th>
    </tr>
            <tr>
                <td>{{empty($member->adjustment) ? 0 : $member->adjustment->price }}</td>
                <td>{{empty($member->adjustment) ? 0 : $member->adjustment->point }}</td>
            </tr>
    </tbody>
</table>
