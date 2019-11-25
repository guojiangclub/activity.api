<?php

/*
 * This file is part of guojiangclub/activity-backend.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Backend\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function postUpload(Request $request)
    {
        $prefix = 'storage/';
        $file = $request->file('upload_image');
        $path = $prefix.$file->store('uploads/images/'.date('Y_m_d'), 'public');
        $url = $this->replaceImgCDN(asset($path));

        return response()->json(['success' => true, 'file' => asset($path), 'url' => $url]);
    }

    // 替换图片CDN
    protected function replaceImgCDN($value)
    {
        $parse = parse_url($value);
        $parse_path = isset($parse['path']) ? $parse['path'] : '';
        $parse_host = isset($parse['host']) ? $parse['host'] : '';
        $app_parse = parse_url(env('APP_URL'));
        if ($app_parse['host'] !== $parse_host) {
            return $value;
        }
        $cdn_status = settings('store_img_cdn_status') ? settings('store_img_cdn_status') : 0;
        if ($cdn_status && $value) {
            $cdn_url = settings('store_img_cdn_url') ? settings('store_img_cdn_url') : '';
            $parse_path = isset($parse['path']) ? $parse['path'] : '';

            return $cdn_url.$parse_path;
        }

        return $value;
    }
}
