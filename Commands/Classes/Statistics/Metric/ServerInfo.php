<?php
namespace Commands\Classes\Statistics\Metric;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Commands\Classes\Statistics\Statistics;

class ServerInfo extends Statistics
{
    /**
     * @param Collection $collection
     */
    public function handle(Collection $collection)
    {
        $this->keyInUse($collection, 'cpus');
        $this->keyInUse($collection, 'timestamp');
        $this->keyInUse($collection, 'server_name');
        $this->keyInUse($collection, 'server_type');
        $this->keyInUse($collection, 'cpu_load_percent');
        $this->keyInUse($collection, 'cpu_idle_percent');
        try{
            $output = [];
            $json = "";

            if (env('FAKE_DATA', 'false') == 'true') {
                $json = $this->fake();
            } else {
                exec('mpstat 1 5 -o JSON', $output);
                foreach ($output as $item) {
                    $json .= $item;
                }
            }

            $stat = json_decode(str_replace('-', '_', $json));
            $collection->put('timestamp', (string)Carbon::now()->toDateTimeString());
            $collection->put('server_name', (string)str_replace('_', '-', $stat->sysstat->hosts[0]->nodename));
            $collection->put('server_type', (string)str_replace('_', '-', "{$stat->sysstat->hosts[0]->sysname} {$stat->sysstat->hosts[0]->release} {$stat->sysstat->hosts[0]->machine}"));
            $collection->put("cpus", (int) $stat->sysstat->hosts[0]->number_of_cpus);


            $usage = [];
            foreach ($stat->sysstat->hosts[0]->statistics as $statistic) {
                $usage[] = ['idle'=>$statistic->cpu_load[0]->idle];
            }

            $total_usage = collect($usage);
            $idle = number_format($total_usage->median('idle'));
            $load = number_format(100 - $idle);

            $collection->put('cpu_load_percent', (float) $load);
            $collection->put('cpu_idle_percent', (float) $idle);
        }catch(\Exception $e){
            $collection->put('timestamp', (string)Carbon::now()->toDateTimeString());
            $collection->put("server_name", (string) "X");
            $collection->put("server_type", (string) "X");
            $collection->put("cpus", (int) 0);
            $collection->put('cpu_load_percent', (float)0.00);
            $collection->put('cpu_idle_percent', (float)0.00);
        }
        $this->next($collection);
    }

    protected function fake()
    {
        $node_name = 'fake-system-1';
        $sys_name = 'MacOS';
        $cpus = 8;
        $date = Carbon::now()->format('d/m/Y');
        $time = Carbon::now()->format('H:i:s');

        $cpu_idle_percent_0 = 91.00;
        $cpu_other_percent_0 = 9.00;

        $cpu_idle_percent_1 = 88.00;
        $cpu_other_percent_1 = 12.00;

        $cpu_idle_percent_2 = 67.00;
        $cpu_other_percent_2 = 33.00;

        $cpu_idle_percent_3 = 78.00;
        $cpu_other_percent_3 = 22.00;

        $cpu_idle_percent_4 = 97.00;
        $cpu_other_percent_4 = 3.00;

        return '{"sysstat": {"hosts": [{"nodename": "' . $node_name . '","sysname": "' . $sys_name . '","release": "5.4.0-200-generic","machine": "x86_64","number-of-cpus": ' . $cpus . ',"date": "' . $date . '","statistics": [{"timestamp": "' . $time . '","cpu-load": [{"cpu": "all", "usr": '.$cpu_other_percent_0.', "nice": 0.00, "sys": 0.00, "iowait": 0.00, "irq": 0.00, "soft": 0.00, "steal": 0.00, "guest": 0.00, "gnice": 0.00, "idle": '.$cpu_idle_percent_0.'}]},{"timestamp": "' . $time . '","cpu-load": [{"cpu": "all", "usr": '.$cpu_other_percent_1.', "nice": 0.00, "sys": 0.00, "iowait": 0.00, "irq": 0.00, "soft": 0.00, "steal": 0.00, "guest": 0.00, "gnice": 0.00, "idle": '.$cpu_idle_percent_1.'}]},{"timestamp": "' . $time . '","cpu-load": [{"cpu": "all", "usr": '.$cpu_other_percent_2.', "nice": 0.00, "sys": 0.00, "iowait": 0.00, "irq": 0.00, "soft": 0.00, "steal": 0.00, "guest": 0.00, "gnice": 0.00, "idle": '.$cpu_idle_percent_2.'}]},{"timestamp": "' . $time . '","cpu-load": [{"cpu": "all", "usr": '.$cpu_other_percent_3.', "nice": 0.00, "sys": 0.00, "iowait": 0.00, "irq": 0.00, "soft": 0.00, "steal": 0.00, "guest": 0.00, "gnice": 0.00, "idle": '.$cpu_idle_percent_3.'}]},{"timestamp": "' . $time . '","cpu-load": [{"cpu": "all", "usr": '.$cpu_other_percent_4.', "nice": 0.00, "sys": 0.00, "iowait": 0.00, "irq": 0.00, "soft": 0.00, "steal": 0.00, "guest": 0.00, "gnice": 0.00, "idle": '.$cpu_idle_percent_4.'}]}]}]}}';
    }
}