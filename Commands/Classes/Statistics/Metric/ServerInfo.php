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
                exec('mpstat -o JSON', $output);
                foreach ($output as $item) {
                    $json .= $item;
                }
            }

            $stat = json_decode(str_replace('-', '_', $json));
            $collection->put('timestamp', (string)Carbon::now()->toDateTimeString());
            $collection->put('server_name', (string)str_replace('_', '-', $stat->sysstat->hosts[0]->nodename));
            $collection->put('server_type', (string)str_replace('_', '-', "{$stat->sysstat->hosts[0]->sysname} {$stat->sysstat->hosts[0]->release} {$stat->sysstat->hosts[0]->machine}"));
            $collection->put("cpus", (int) $stat->sysstat->hosts[0]->number_of_cpus);

            $idle = number_format($stat->sysstat->hosts[0]->statistics[0]->cpu_load[0]->idle, 2);
            $load = number_format(100 - number_format($stat->sysstat->hosts[0]->statistics[0]->cpu_load[0]->idle, 2), 2);

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

        $cpu_usr_percent = 1.00;
        $cpu_nice_percent = 1.00;
        $cpu_sys_percent = 1.00;
        $cpu_iowait_percent = 1.00;
        $cpu_irq_percent = 1.00;
        $cpu_soft_percent = 1.00;
        $cpu_steal_percent = 1.00;
        $cpu_guest_percent = 1.00;
        $cpu_gnice_percent = 1.00;
        $cpu_idle_percent = 91.00;

        return '{"sysstat": {"hosts": [{"nodename": "' . $node_name . '","sysname": "' . $sys_name . '","release": "5.4.0-200-generic","machine": "x86_64","number-of-cpus": ' . $cpus . ',"date": "' . $date . '","statistics": [{"timestamp": "' . $time . '","cpu-load": [{"cpu": "all", "usr": ' . $cpu_usr_percent . ', "nice": ' . $cpu_nice_percent . ', "sys": ' . $cpu_sys_percent . ', "iowait": ' . $cpu_iowait_percent . ', "irq": ' . $cpu_irq_percent . ', "soft": ' . $cpu_soft_percent . ', "steal": ' . $cpu_steal_percent . ', "guest": ' . $cpu_guest_percent . ', "gnice": ' . $cpu_gnice_percent . ', "idle": ' . $cpu_idle_percent . '}]}]}]}}';
    }
}