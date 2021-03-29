<?php
namespace Commands\Classes\Statistics\Metric;

use Illuminate\Support\Collection;
use Commands\Classes\Statistics\Statistics;

class ServerInfo extends Statistics
{
    /**
     * @param Collection $collection
     */
    public function handle(Collection $collection)
    {
        $this->keyInUse($collection, "cpus");
        $this->keyInUse($collection, "server_name");
        $this->keyInUse($collection, "server_type");
        try{
            $output = [];
            $json = "";
            exec('mpstat -o JSON', $output);
            foreach ($output as $item){
                $json .= $item;
            }
            $stat = json_decode(str_replace('-', '_', $json));
            $collection->put("server_name", (string) $stat->sysstat->hosts[0]->nodename);
            $collection->put("server_type", (string) "{$stat->sysstat->hosts[0]->sysname} {$stat->sysstat->hosts[0]->release}");
            $collection->put("cpus", (int) $stat->sysstat->hosts[0]->number_of_cpus);
        }catch(\Exception $e){
            $collection->put("server_name", (string) "X");
            $collection->put("server_type", (string) "X");
            $collection->put("cpus", (int) 0);
        }
        $this->next($collection);
    }
}