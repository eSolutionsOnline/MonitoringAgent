<?php

namespace Commands\Classes\Statistics\Metric;

use Illuminate\Support\Collection;
use Commands\Classes\Statistics\Statistics;

class Memory extends Statistics
{
    /**
     * @param Collection $collection
     */
    public function handle(Collection $collection)
    {
        $this->keyInUse($collection, "total_memory_mb");
        $this->keyInUse($collection, "used_memory_mb");
        $this->keyInUse($collection, "free_memory_mb");
        $this->keyInUse($collection, "used_memory_percent");
        $this->keyInUse($collection, "free_memory_percent");
        try {
            $output = [];

            if (env('FAKE_DATA', 'false') == 'true') {
                $output = $this->fake();
            } else {
                exec('free --mega -t', $output);
            }
            if(count($output) === 4){
                $result = [];
                foreach (explode(" ",$output[3]) as $stat){
                    if($stat != "Total:" && $stat != ""){
                        $result[] = $stat;
                    }
                }


                if(count($result) === 3){

                    $total = $result[1]+$result[2];
                    $used = $result[1];
                    $free = $result[2];

                    $collection->put("total_memory_mb", (int) $total);
                    $collection->put("used_memory_mb", (int) $used);
                    $collection->put("free_memory_mb", (int) $free);

                    $used_memory_percent = number_format(($used / $total) * 100);
                    $free_memory_percent = number_format(($free / $total) * 100);

                    $collection->put("used_memory_percent", (float) $used_memory_percent);
                    $collection->put("free_memory_percent", (float) $free_memory_percent);
                }else{
                    $collection->put("total_memory_mb", (int) 0);
                    $collection->put("used_memory_mb", (int) 0);
                    $collection->put("free_memory_mb", (int) 0);
                    $collection->put("buffer_memory_mb", (int) 0);
                    $collection->put("used_memory_percent", (float) 0.00);
                    $collection->put("free_memory_percent", (float) 0.00);
                }
            }else{
                $collection->put("total_memory_mb", (int) 0);
                $collection->put("used_memory_mb", (int) 0);
                $collection->put("free_memory_mb", (int) 0);
                $collection->put("buffer_memory_mb", (int) 0);
                $collection->put("used_memory_percent", (float) 0.00);
                $collection->put("free_memory_percent", (float) 0.00);
            }
        } catch (\Exception $e) {
            $collection->put("total_memory_mb", (int) 0);
            $collection->put("used_memory_mb", (int) 0);
            $collection->put("free_memory_mb", (int) 0);
            $collection->put("buffer_memory_mb", (int) 0);
            $collection->put("used_memory_percent", (int) 0);
            $collection->put("free_memory_percent", (int) 0);
        }
        $this->next($collection);
    }

    protected function fake()
    {
        return [
            '            total        used        free      shared  buff/cache   available',
            'Mem:           4110         277        2581          15        1251        3556',
            'Swap:          1073           0        1073',
            'Total:         5184         277        3655',
        ];
    }
}
