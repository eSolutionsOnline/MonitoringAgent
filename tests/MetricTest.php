<?php
namespace Tests;

use Commands\Classes\Statistics\Metric\Disk;
use Commands\Classes\Statistics\Metric\LoadAverage;
use Commands\Classes\Statistics\Metric\LoadCurrent;
use Commands\Classes\Statistics\Metric\Memory;
use Commands\Classes\Statistics\Metric\ServerInfo;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class MetricTest extends TestCase
{
    public function test_that_the_disk_metric_return_the_correct_data()
    {
        $collection = new Collection();
        $disk = new Disk;
        $disk->handle($collection);

        $total = $collection->get('total_disk_space_mb');
        $this->assertIsInt($total,"total_disk_space_mb should be an int");
        $this->assertGreaterThan(0, $total,"total_disk_space_mb should be greater than 0");

        $free = $collection->get('free_disk_space_mb');
        $this->assertIsInt($free,"free_disk_space_mb should be an int");
        $this->assertGreaterThan(0, $free,"free_disk_space_mb should be greater than 0");
        $this->assertLessThan($total, $free,"free_disk_space_mb should be less than total_disk_space_mb");

        $used = $collection->get('used_disk_space_mb');
        $this->assertIsInt($used,"used_disk_space_mb should be an int");
        $this->assertGreaterThan(0, $used,"used_disk_space_mb should be greater than 0");
        $this->assertLessThan($total, $used,"used_disk_space_mb should be less than total_disk_space_mb");
    }

    public function test_that_the_memory_metric_return_the_correct_data()
    {
        $collection = new Collection();
        $memory = new Memory();
        $memory->handle($collection);

        $total = $collection->get('total_memory_mb');
        $this->assertIsInt($total,"total_memory_mb should be an int");
        $this->assertGreaterThan(0, $total,"total_memory_mb should be greater than 0");

        $free = $collection->get('free_memory_mb');
        $this->assertIsInt($free,"free_memory_mb should be an int");
        $this->assertGreaterThan(0, $free,"free_memory_mb should be greater than 0");
        $this->assertLessThan($total, $free,"free_memory_mb should be less than total_disk_space_mb");

        $used = $collection->get('used_memory_mb');
        $this->assertIsInt($used,"used_memory_mb should be an int");
        $this->assertGreaterThan(0, $used,"used_memory_mb should be greater than 0");
        $this->assertLessThan($total, $used,"used_memory_mb should be less than total_disk_space_mb");

        $buffer = $collection->get('buffer_memory_mb');
        $this->assertIsInt($buffer,"buffer_memory_mb should be an int");
        $this->assertGreaterThan(0, $buffer,"buffer_memory_mb should be greater than 0");
        $this->assertLessThan($total, $buffer,"buffer_memory_mb should be less than total_disk_space_mb");
    }

    public function test_that_the_server_info_metric_return_the_correct_data()
    {
        $collection = new Collection();
        $info = new ServerInfo();
        $info->handle($collection);

        $name = $collection->get('server_name');
        $this->assertIsString($name);
        $this->assertNotEquals($name, "X");

        $type = $collection->get('server_type');
        $this->assertIsString($type);
        $this->assertNotEquals($type, "X");

        $cpu = $collection->get('cpus');
        $this->assertIsInt($cpu,"cpus should be an int");
        $this->assertGreaterThan(0, $cpu,"cpus should be greater than 0");
    }

    public function test_that_the_load_current_metric_return_the_correct_data()
    {
        $collection = new Collection();
        $load = new LoadCurrent();
        $load->handle($collection);

        $cpu_load = $collection->get('cpu_load');
        $this->assertIsFloat($cpu_load);

        $cpu_idle = $collection->get('cpu_idle');
        $this->assertIsFloat($cpu_idle);

        $this->assertEquals(100.0, (float) $cpu_idle+$cpu_load, "cpu_load + cpu_idle should equal 100.0");

    }

    public function test_that_the_load_average_metric_return_the_correct_data()
    {
        $collection = new Collection();
        $load = new LoadAverage();
        $load->handle($collection);

        $cpu_load = $collection->get('avg_cpu_load');
        $this->assertIsFloat($cpu_load);

        $cpu_idle = $collection->get('avg_cpu_idle');
        $this->assertIsFloat($cpu_idle);

        $this->assertEquals(100.0, (float) $cpu_idle+$cpu_load, "cpu_load + cpu_idle should equal 100.0");

    }

}
