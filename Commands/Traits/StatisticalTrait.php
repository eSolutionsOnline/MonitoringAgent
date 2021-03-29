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
        $load_average = new LoadAverage();
        $load_current = new LoadCurrent();

        //TODO: consider a method to do this at runtime from a config

        $server_info->succeedWith($load_average);
        $load_average->succeedWith($load_current);
        $load_current->succeedWith($memory);
        $memory->succeedWith($disk);

        $server_info->handle($collection);

        return $collection;
    }
}
