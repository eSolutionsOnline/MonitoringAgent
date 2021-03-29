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
        try{
            $output = [];
            $formatted = [];
            $i = 0;
            exec('df --out=target --output=used,avail --block-size=1MB', $output);
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
                        $collection->put("total_disk_space_mb", (int) $disk[2] + $disk[1]);
                        $collection->put("free_disk_space_mb", (int) $disk[2]);
                        $collection->put("used_disk_space_mb", (int) $disk[1]);
                    }
                }
            }
        }catch(\Exception $e){
            $collection->put("total_disk_space_mb", (int) 0);
            $collection->put("free_disk_space_mb", (int) 0);
            $collection->put("used_disk_space_mb", (int) 0);
        }

        $this->next($collection);
    }
}
