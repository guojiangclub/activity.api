<?php

namespace GuoJiangClub\Member\Backend\Repository;

use iBrand\Common\Exceptions\Exception;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use GuoJiangClub\Member\Backend\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

/**
 * Class UserRepositoryEloquent
 * @package namespace App\Repositories\Backend;
 */
class UserRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param $where
     * @param int $limit
     * @param string $order_by
     * @param string $sort
     * @return mixed
     */

    public function searchUserPaginated($where, $limit = 50, $order_by = 'id', $sort = 'desc')
    {
        $data = $this->scopeQuery(function ($query) use ($where) {
            if (is_array($where)) {
                foreach ($where as $key => $value) {
                    if ($key == 'integral') {
                        $query = $query->whereBetween($key, $value);
                    } elseif (is_array($value)) {
                        list($operate, $va) = $value;
                        $query = $query->where($key, $operate, $va);
                    } else {
                        $query = $query->where($key, $value);
                    }
                }
            }
            return $query->with('roles')->orderBy('created_at', 'desc');
        });
        if ($limit == 0) {
            return $data->all();
        }
        return $data->paginate($limit);

    }


    /**
     * @param $id
     * @param $status
     * @return bool
     * @throws GeneralException
     */
    public function mark($id, $status)
    {
        $user = $this->find($id);
        $user->status = $status;

        if ($user->save())
            return true;

        throw new Exception("?????????????????????????????????");
    }

    /**
     * @param $where
     * @param int $limit
     * @param string $order_by
     * @param string $sort
     * @return mixed
     */

    public function getDeletedUsersPaginated($where, $limit = 50, $order_by = 'id', $sort = 'desc')
    {

        return $this->scopeQuery(function ($query) use ($where) {
            if (is_array($where)) {
                foreach ($where as $key => $value) {
                    if (is_array($value)) {
                        list($operate, $va) = $value;
                        $query = $query->where($key, $operate, $va);
                    } else {
                        $query = $query->where($key, $value);
                    }
                }
            }
            return $query->with('roles')->orderBy('updated_at', 'desc')->onlyTrashed();
        })->paginate($limit);
    }


    /**
     * ???????????????????????????????????????????????????????????????
     * @param $userId
     * @param $integral
     * @return mixed
     */
    public function addIntegral($userId, $integral)
    {
        $integral = (int)$integral;
        if ($integral == 0)
            return;
        $user = $this->find($userId);
        if ($integral < 0) {
            $user->available_integral = $user->available_integral + $integral;

        } else {
            $user->available_integral = $user->available_integral + $integral;
            $user->integral = $user->integral + $integral;
        }
        $user->save();
        return $user;
    }



    /**
     * ????????????
     * @param $where
     * @param $time
     * @return array
     */
    public function getUserExportList($where, $time)
    {

        $data = [];
        $user_list = $this->scopeQuery(function ($query) use ($where, $time) {
            if (is_array($where) && count($where)) {
                foreach ($where as $key => $value) {
                    if (is_array($value)) {
                        list($operate, $va) = $value;
                        $query = $query->where($key, $operate, $va);

                    } else {
                        $query = $query->where($key, $value);
                    }

                }
            }
            if (is_array($time)) {
                foreach ($time as $key => $value) {
                    if (is_array($value) && count($time)) {
                        list($operate, $va) = $value;
                        $query = $query->where($key, $operate, $va);
                    } else {
                        $query = $query->where($key, $value);
                    }
                }
            }
            return $query->orderBy('updated_at', 'desc');
        })->with(['card', 'bind'])->all();
        $i = 0;
        foreach ($user_list as $item) {
            if (isset($item->bind)) {
                if (!empty($item->bind->app_id)) {
                    $item->open_id = $item->bind->app_id;
                } else {
                    $item->open_id = $item->bind->open_id;
                }
            }
            $i++;
            $data[$i][] = $item->name;
            $data[$i][] = isset($item->email) ? $item->email : '';
            $data[$i][] = isset($item->mobile) ? $item->mobile : '';
            $data[$i][] = isset($item->available_integral) ? $item->available_integral : 0;
            $data[$i][] = isset($item->roles->display_name) ? $item->roles->display_name : '';
            $data[$i][] = $item->created_at;
            $data[$i][] = isset($item->card->number) ? $item->card->number : '';
            $data[$i][] = isset($item->card->created_at) ? $item->card->created_at : '';
            $data[$i][] = isset($item->card->name) ? $item->card->name : '';
            $data[$i][] = isset($item->card->mobile) ? $item->card->mobile : '';
            $data[$i][] = isset($item->card->birthday) ? $item->card->birthday : '';
            $data[$i][] = isset($item->open_id) ? $item->open_id : '';
        }
        return $data;

    }


    /**
     * ??????????????????????????????
     * @param $where
     * @param $time
     * @return array
     */
    public function getExportUserData($where, $time, $limit = 50)
    {
        $data = [];
        $user_list = $this->scopeQuery(function ($query) use ($where, $time) {
            if (is_array($where) && count($where)) {
                foreach ($where as $key => $value) {
                    if (is_array($value)) {
                        list($operate, $va) = $value;
                        $query = $query->where($key, $operate, $va);

                    } else {
                        $query = $query->where($key, $value);
                    }

                }
            }
            if (is_array($time)) {
                foreach ($time as $key => $value) {
                    if (is_array($value) && count($time)) {
                        list($operate, $va) = $value;
                        $query = $query->where($key, $operate, $va);
                    } else {
                        $query = $query->where($key, $value);
                    }
                }
            }
            return $query->orderBy('updated_at', 'desc');
        })->with(['card', 'bind'])->paginate($limit);

        $lastPage = $user_list->lastPage();
        //\Log::info($lastPage);

        $i = 0;
        foreach ($user_list as $item) {
            if (isset($item->bind)) {
                $item->open_id = $item->bind->open_id;
            }
            $card_name = isset($item->card->name) ? $item->card->name : '';
            $i++;
            $data[$i][] = $item->id;
            $data[$i][] = $this->filterName($item->name);
            $data[$i][] = isset($item->email) ? $item->email : '';
            $data[$i][] = isset($item->mobile) ? $item->mobile : '';
            $data[$i][] = isset($item->available_integral) ? $item->available_integral : 0;
            $data[$i][] = isset($item->roles->display_name) ? $item->roles->display_name : '';
            $data[$i][] = $item->created_at->toDateTimeString();
            $data[$i][] = isset($item->card->number) ? $item->card->number : '';
            $data[$i][] = isset($item->card->created_at) ? $item->card->created_at : '';
            $data[$i][] = $this->removeEmoJi($card_name);
            $data[$i][] = isset($item->card->mobile) ? $item->card->mobile : '';
            $data[$i][] = isset($item->card->birthday) ? $item->card->birthday : '';
            $data[$i][] = isset($item->open_id) ? $item->open_id : '';
        }


        return [
            'users' => $data,
            'lastPage' => $lastPage
        ];

    }

    protected function filterName($name)
    {
        $regex = "/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|/";
        if (preg_match($regex, $name) OR str_contains($name, 'base64')) {
            return '';
        }
        return $name;
    }

    /**
     * ??????????????????emoji??????
     *
     * @param $nickname
     *
     * @return string
     */
    function removeEmoJi($nickname)
    {
        $string = preg_replace_callback('/./u', function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        }, $nickname);

        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $string = preg_replace($regexEmoticons, '', $string);

        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $string = preg_replace($regexSymbols, '', $string);

        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $string = preg_replace($regexTransport, '', $string);

        $regexMisc = '/[\x{2600}-\x{26FF}]/u';
        $string = preg_replace($regexMisc, '', $string);

        $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
        $string = preg_replace($regexDingbats, '', $string);

        $regex = "/\/|\~|\!|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|\"|\???|\, |\???|\???|/";
        $string = preg_replace($regex, '', $string);

        return $string;
    }
}
