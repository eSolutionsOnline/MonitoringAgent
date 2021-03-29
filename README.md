![Eso Logo](public/logo.png)

# MonitoringAgent
This server monitoring agent while primarily designed for ubuntu servers it will work on any server that can support PHP.

---

## Installation

---

1. Clone the repo `git clone git@github.com:eSolutionsOnline/MonitoringAgent.git MonitoringAgent`
1. Move into the cloned dir `cd MonitoringAgent`
1. Install the dependencies `composer install`
1. Copy the env settings `cp .env.example .env`
1. Set the endpoint
1. Set the API key

You should be good to go, let go ahead and run the application.


## Running the monitor command

---

MonitoringAgent uses the `Symfony\Component\Console\Application` to do the heavy lifting calling the base command `artisan` is just lazy as I am just used to typing it!

Basic usage `php artisan monitor`:

### Server Monitor vx.x.x
| Stat | Value |
| ----------- | ----------- |
| server_name | homestead |
| server_type | Linux 5.4.0_65_generic |
| cpus | 1 |
| avg_cpu_load | 0.50 |
| avg_cpu_idle | 99.50 |

Adding the `-q` switch do it all quietly.  Adding the `-s` switch will send the data to the given endpoint


## Extending

---

Adding more stats is easy as we go through each metric in the chain we add to the collection yes `Illuminate\Support\Collection` at the end we send to the endpoint or to a table.

Create a new metric in `/MonitoringAgent/Commands/Classes/Statistics/Metric` using the current metrics as a template and ensure that `$this->next($collection);` is at the end of the method.

Register the new metric in `/MonitoringAgent/Commands/Traits/StatisticalTrait.php`  


```php
    //Original
    protected function Statistics(): Collection
    {
        $collection = new Collection();
        $server_info = new ServerInfo();
        $load_average = new LoadAverage();

        $server_info->succeedWith($load_average);

        $server_info->handle($collection);

        return $collection;
    }
```


```php
    //Modified
    protected function Statistics(): Collection
    {
        $collection = new Collection();
        $server_info = new ServerInfo();
        $load_average = new LoadAverage();
        $new_metric = new NewMetric(); //NEW

        $server_info->succeedWith($load_average);
        $load_average->succeedWith($new_metric); //NEW

        $server_info->handle($collection);

        return $collection;
    }
```

