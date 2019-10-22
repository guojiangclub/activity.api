<?php
namespace GuoJiangClub\Activity\Core\Console;

use GuoJiangClub\Activity\Core\Models\City;
use Illuminate\Console\Command;

class ActivityCityCommand extends Command
{

    protected $signature = 'activity-city:factory';

    protected $description = 'Activity city factory.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = $this->cityData();
        if (count($data) > 0) {
            foreach ($data as $item) {
                City::create($item);
            }
        }
    }

    private function cityData()
    {
        $data = [
            ['name' => '北京', 'province' => 110000, 'city' => 110100, 'area' => 110101],
            ['name' => '天津', 'province' => 120000, 'city' => 120100, 'area' => 120101],
            ['name' => '石家庄', 'province' => 130000, 'city' => 130100, 'area' => 130102],
            ['name' => '太原', 'province' => 140000, 'city' => 140100, 'area' => 140105],
            ['name' => '呼和浩特', 'province' => 150000, 'city' => 150100, 'area' => 150102],
            ['name' => '沈阳', 'province' => 210000, 'city' => 210100, 'area' => 210102],
            ['name' => '长春', 'province' => 220000, 'city' => 220100, 'area' => 220102],
            ['name' => '哈尔滨', 'province' => 230000, 'city' => 230100, 'area' => 230102],
            ['name' => '上海', 'province' => 310000, 'city' => 310100, 'area' => 310101],
            ['name' => '南京', 'province' => 320000, 'city' => 320100, 'area' => 320102],
            ['name' => '杭州', 'province' => 330000, 'city' => 330100, 'area' => 330102],
            ['name' => '合肥', 'province' => 340000, 'city' => 340100, 'area' => 340102],
            ['name' => '福州', 'province' => 350000, 'city' => 350100, 'area' => 350102],
            ['name' => '南昌', 'province' => 360000, 'city' => 360100, 'area' => 360102],
            ['name' => '济南', 'province' => 370000, 'city' => 370100, 'area' => 370102],
            ['name' => '郑州', 'province' => 410000, 'city' => 410100, 'area' => 410102],
            ['name' => '武汉', 'province' => 420000, 'city' => 420100, 'area' => 420102],
            ['name' => '长沙', 'province' => 430000, 'city' => 430100, 'area' => 430102],
            ['name' => '广州', 'province' => 440000, 'city' => 440100, 'area' => 440103],
            ['name' => '南宁', 'province' => 450000, 'city' => 450100, 'area' => 450102],
            ['name' => '海口', 'province' => 460000, 'city' => 460100, 'area' => 460105],
            ['name' => '重庆', 'province' => 500000, 'city' => 500100, 'area' => 500101],
            ['name' => '成都', 'province' => 510000, 'city' => 510100, 'area' => 510104],
            ['name' => '贵阳', 'province' => 520000, 'city' => 520100, 'area' => 520102],
            ['name' => '昆明', 'province' => 530000, 'city' => 530100, 'area' => 530100],
            ['name' => '拉萨', 'province' => 540000, 'city' => 540100, 'area' => 540102],
            ['name' => '西安', 'province' => 610000, 'city' => 610100, 'area' => 610102],
            ['name' => '兰州', 'province' => 620000, 'city' => 620100, 'area' => 620102],
            ['name' => '西宁', 'province' => 630000, 'city' => 630100, 'area' => 630102],
            ['name' => '银川', 'province' => 640000, 'city' => 640100, 'area' => 640104],
            ['name' => '乌鲁木齐', 'province' => 650000, 'city' => 650100, 'area' => 650102]
        ];

        foreach ($data as $key => $item) {
            if (City::where('city', $item['city'])->first()) {
                unset($data[$key]);
            }
        }

        return $data;
    }

}