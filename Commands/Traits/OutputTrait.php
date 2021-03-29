<?php


namespace Commands\Traits;

use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait OutputTrait {


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Collection $collection
     */
    protected function tabulate(InputInterface $input, OutputInterface $output, Collection $collection): void
    {
        //If the quiet option is used do not display the data
        if(!$input->getOption('quiet')){
            $rows = [];
            $collection->each(function ($value, $key) use(& $rows) {
                $rows[] = [$key, $value];
            });
            $table = new Table($output);
            $table->setHeaders(['Stat', 'Value'])->setRows($rows);
            $table->render();
        }
    }





//    public function Statistics(): Collection
//    {
//        $collection = new Collection();
//        $server_info = new ServerInfo();
//        $load_average = new LoadAverage();
//
//        $server_info->succeedWith($load_average);
//
//        $server_info->handle($collection);
//
//        return $collection;
//    }




}