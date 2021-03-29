<?php


namespace Commands\Traits;

use GuzzleHttp\Client as Guzzle;
use Illuminate\Support\Collection;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait SendDataTrait {

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Collection $collection
     */
    protected function sendData(InputInterface $input, OutputInterface $output, Collection $collection): void
    {
        //Only send if the send option is used
        if($input->getOption('send')){
            $output->writeln("<info>Sending data...</info>");
            $endpoint = env('ENDPOINT');
            $api_key = env('API_KEY');


            if(!$endpoint || !$api_key){
                throw new \Exception("There must be an endpoint and api key set");
            }
            try{
                $collection->prepend($api_key, "api_key");
                $client = new Guzzle();
                $response = $client->request('POST', $endpoint, ['json'=>$collection->toJson()]);
                if($response->getStatusCode() != 200){
                    throw new \Exception("Request not accepted");
                }
            }catch (\Exception $e){
                die($e->getMessage()."\n");
            } catch (GuzzleException $e) {
                die($e->getMessage()."\n");
            }
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