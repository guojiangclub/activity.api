<?php

namespace GuojiangClub\Activity\Core\Listeners;

use GuojiangClub\Activity\Core\Models\ActivityOrders;
use ElementVip\Component\Order\Models\Order;
use ElementVip\Component\Order\Processor\OrderProcessor;

class ActivityEventListener
{
    protected $orderProcessor;
    public function __construct(OrderProcessor $orderProcessor)
    {
        $this->orderProcessor=$orderProcessor;
    }

    public function onMemberSigned()
    {

    }

    public function onMemberCancel($member_id, $activity_id)
    {
        if (!$shopOrder = ActivityOrders::where('member_id', $member_id)->where('activity_id', $activity_id)->first()) return;

        if (!$order = Order::find($shopOrder->order_id) OR $order->status != Order::STATUS_NEW) return;

        $this->orderProcessor->cancel($order);
        foreach ($order->getItems() as $item) {
            $model = $item->type;
            $model = new $model();
            $product = $model->find($item->item_id);
            $product->restoreStock($item->quantity);
            $product->restoreSales($item->quantity);
            $product->save();
        }
    }

    public function subscribe($events)
    {
        $events->listen(
            'activity.member.signed',
            'GuojiangClub\Activity\Core\Listeners\ActivityEventListener@onMemberSigned'
        );

        $events->listen(
            'activity.member.cancel',
            'GuojiangClub\Activity\Core\Listeners\ActivityEventListener@onMemberCancel'
        );
    }

}