<?php


namespace Commands\Traits;

use Illuminate\Support\Collection;
use Commands\Classes\Statistics\Metric\{
    Disk,
    Memory,
    ServerInfo,
    LoadCurrent,
    LoadAverage
};

trait StatisticalTrait {
    protected function Statistics(): Collection
    {
        $collection = new Collection();

        $disk = new Disk();
        $memory = new Memory();
        $server_info = new ServerInfo();

        //TODO: consider a method to do this at runtime from a config

        $server_info->succeedWith($memory);
        $memory->succeedWith($disk);

        $server_info->handle($collection);

        $data_points = env('DATA_POINTS', "");
        if($data_points != ""){
            $data_points = explode(",", $data_points);
            $only = [];
            foreach($data_points as $data_point){
                $only[] = trim($data_point);
            }

            $filteredCollection = $collection->only($only);
            return $filteredCollection;
        }

        return $collection;
    }
}
