<?php


namespace Commands\Classes\Statistics;

use Illuminate\Support\Collection;

abstract class Statistics {

    protected $successor;

    public abstract function handle(Collection $collection);

    /**
     * @param Statistics $successor
     */
    public function succeedWith(Statistics $successor)
    {
        $this->successor = $successor;
    }

    /**
     * @param Collection $collection
     */
    public function next(Collection $collection)
    {
        if ($this->successor){
            $this->successor->handle($collection);
        }
    }

    /**
     * @param Collection $collection
     * @param $check
     * @return bool
     */
    public function keyInUse(Collection $collection, $check)
    {
        return $collection->contains(function ($value, $key) use ($check) {
            if($check == $key){
                throw new \Exception("The key {$key} is already in use");
            }
        });
    }
}