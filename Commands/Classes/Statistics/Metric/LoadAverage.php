<?php
namespace Commands\Classes\Statistics\Metric;

use Illuminate\Support\Collection;
use Commands\Classes\Statistics\Statistics;

class LoadAverage extends Statistics
{
    /**
     * @param Collection $collection
     */
    public function handle(Collection $collection)
    {
        $this->keyInUse($collection, "avg_cpu_load");
        $this->keyInUse($collection, "avg_cpu_idle");
        try{
            $output = [];
            $json = "";
            exec('mpstat -o JSON', $output);
            foreach ($output as $item){
                $json .= $item;
            }
            $stat = json_decode(str_replace('-', '_', $json));
            $idle = number_format($stat->sysstat->hosts[0]->statistics[0]->cpu_load[0]->idle, 2);
            $load = number_format(100 - number_format($stat->sysstat->hosts[0]->statistics[0]->cpu_load[0]->idle, 2), 2);
            $collection->put("avg_cpu_load", (float) $load);
            $collection->put("avg_cpu_idle", (float) $idle);
        }catch(\Exception $e){
            $collection->put("avg_cpu_load", (float) 0.00);
            $collection->put("avg_cpu_idle", (float) 0.00);
        }
        $this->next($collection);
    }
}