<?php

namespace GuojiangClub\Activity\Server\Http\Controllers;

use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Helpers;

    public function api($data = array(), $status = true, $code = 200, $message = '')
    {
        return new Response(['status' => $status
            , 'code' => $code
            , 'message' => $message
            , 'data' =>  empty($data) ? null : $data]);
    }

    public function success($data = array(), $code = Response::HTTP_OK, $status = true)
    {
        return new Response(['status' => $status
            , 'code' => $code
            , 'data' => !$data ? null : $data]);
    }

    /**
     * @param $message
     * @param int $code
     * @param bool $status
     * @return mixed
     */
    public function failed($message, $code = Response::HTTP_BAD_REQUEST, $status = false)
    {
        return new Response(['status' => $status
                , 'code' => $code
                , 'message' => $message]
        );
    }

}