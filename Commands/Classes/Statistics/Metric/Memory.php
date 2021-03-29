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
        $this->keyInUse($collection, "buffer_memory_mb");
        try {
            $output = [];
            exec('free --mega -t', $output);
            if(count($output) === 4){
                $result = [];
                foreach (explode(" ",$output[3]) as $stat){
                    if($stat != "Total:" && $stat != ""){
                        $result[] = $stat;
                    }
                }
                if(count($result) === 3){
                    $collection->put("total_memory_mb", (int) $result[0]);
                    $collection->put("used_memory_mb", (int) $result[1]);
                    $collection->put("free_memory_mb", (int) $result[2]);
                    $collection->put("buffer_memory_mb", (int) $result[0] - ($result[1]+$result[2]));
                }else{
                    $collection->put("total_memory_mb", (int) 0);
                    $collection->put("used_memory_mb", (int) 0);
                    $collection->put("free_memory_mb", (int) 0);
                    $collection->put("buffer_memory_mb", (int) 0);
                }
            }else{
                $collection->put("total_memory_mb", (int) 0);
                $collection->put("used_memory_mb", (int) 0);
                $collection->put("free_memory_mb", (int) 0);
                $collection->put("buffer_memory_mb", (int) 0);
            }
        } catch (\Exception $e) {
            $collection->put("total_memory_mb", (int) 0);
            $collection->put("used_memory_mb", (int) 0);
            $collection->put("free_memory_mb", (int) 0);
            $collection->put("buffer_memory_mb", (int) 0);
        }
        $this->next($collection);
    }
}
