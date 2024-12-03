<?php

namespace Commands\Classes\Statistics\Metric;

use Illuminate\Support\Collection;
use Commands\Classes\Statistics\Statistics;

class Disk extends Statistics
{
    /**
     * @param Collection $collection
     */
    public function handle(Collection $collection)
    {
        $this->keyInUse($collection, "total_disk_space_mb");
        $this->keyInUse($collection, "free_disk_space_mb");
        $this->keyInUse($collection, "used_disk_space_mb");
        $this->keyInUse($collection, "free_disk_space_percent");
        $this->keyInUse($collection, "used_disk_space_percent");

        try{
            $output = [];
            $formatted = [];
            $i = 0;

            if (env('FAKE_DATA', 'false') == 'true') {
                $output = $this->fake();
            } else {
                exec('df --out=target --output=used,avail --block-size=1MB', $output);
            }

            foreach ($output as $line) {
                $formatted[$i] = [];
                foreach (explode(' ', $line) as $v) {
                    if ($v) {
                        $formatted[$i][] = $v;
                    }
                }
                $i++;
            }
            foreach ($formatted as $disk) {
                if (count($disk) == 3) {
                    if ($disk[0] == "/") {

                        $total = $disk[2] + $disk[1];
                        $used = $disk[1];
                        $free = $disk[2];

                        $collection->put("total_disk_space_mb", (int) $total);
                        $collection->put("free_disk_space_mb", (int) $disk[2]);
                        $collection->put("used_disk_space_mb", (int) $disk[1]);

                        $used_disk_percent = number_format(($used / $total) * 100, 2);
                        $free_disk_percent = number_format(($free / $total) * 100, 2);

                        $collection->put("used_disk_space_percent", (float) $used_disk_percent);
                        $collection->put("free_disk_space_percent", (float) $free_disk_percent);
                    }
                }
            }
        }catch(\Exception $e){
            $collection->put("total_disk_space_mb", (int) 0);
            $collection->put("free_disk_space_mb", (int) 0);
            $collection->put("used_disk_space_mb", (int) 0);
            $collection->put("used_disk_space_percent", (float) 0.00);
            $collection->put("free_disk_space_percent", (float) 0.00);
        }

        $this->next($collection);
    }

    protected function fake()
    {
        return [
            'Mounted on         Used Avail',
            '/dev                  0  2038',
            '/run                  2   411',
            '/                  9862 73175',
            '/dev/shm              0  2056',
            '/run/lock             0     6',
            '/sys/fs/cgroup        0  2056',
            '/boot/efi             8   102',
            '/snap/core20/2434    67     0',
            '/snap/lxd/29619      97     0',
            '/snap/core18/2829    59     0',
            '/snap/lxd/24061      97     0',
            '/snap/core20/2379    68     0',
            '/snap/snapd/23258    47     0',
            '/snap/core18/2846    59     0',
            '/snap/snapd/21759    41     0',
            '/run/user/1000        0   412'
        ];
    }
}
