<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Providers\MidtransReceiver;
use App\Repositories\MidtransRepository;
use Illuminate\Http\Request;
use App\Services\Midtrans\CallbackMidtransService;
use Illuminate\Http\Response;

// use App\Validation\MidtransValidation;

class PaymentCallbackController extends Controller
{
    public function __construct()
    {
        // $this->validation = new MidtransValidation();
        $this->repository = new MidtransRepository();
    }

    // private $validation;
    private $repository;

    public function receive()
    {
        $callback = new CallbackMidtransService();

        if ($callback->isSignatureKeyVerified()) {
            $notification = $callback->getNotification();
            $order = $callback->getOrder();
            $param['status'] = 0;
            if ($callback->isSuccess()) $param['status'] = 1;

            if ($callback->isExpire()) $param['status'] = 2;

            if ($callback->isCancelled()) $param['status'] = 3;

            $save = $this->repository->updateOrderByNumber($order->number, $param);

            if(!$save['res']) return parent::getRespnse(Response::HTTP_INTERNAL_SERVER_ERROR, $save['message'], null);

            event(new MidtransReceiver('Payment Notification', 'your payment has been successful', '7m.helmi@gmail.com'));

            return parent::getRespnse(Response::HTTP_CREATED, "Order's updated has been successful", null);

        } else {
            return parent::getRespnse(Response::HTTP_FORBIDDEN, "Signature key tidak terverifikasi", null);
        }
    }
}
