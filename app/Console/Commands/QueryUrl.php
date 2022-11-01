<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Services\ApiRequest;
use App\Http\Services\ApiResponse;

class QueryUrl extends Command
{
    protected $signature = 'query:url {url?}';

    protected $description = 'Makes a request to the given URI via an open proxy and outputs the HTTP headers';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $url = $this->argument('url');
        if ( $this->argument('url') !== null ) {

            try {
                $this->info("Starting...");
                $ApiRequest = new ApiRequest('https://api.proxyscrape.com/v2/?request=displayproxies&protocol=http&timeout=10000&country=GB&ssl=no&anonymity=all');
                $ApiResponse = new ApiResponse($ApiRequest);
                
                $this->info("Getting headers using " . count($ApiRequest->proxies) . " proxies...");
                foreach ( $ApiRequest->proxies as $proxy ) {
                    $this->warn('Trying: ' . $proxy);
                    $response = $ApiRequest->getURIHeader($url,$proxy);
                    if ( $response instanceof \Exception ) {
                        continue;
                    }
                    $this->info(json_encode($ApiResponse::format($response)));
                    $this->newLine();
                } 

                $now = date('d/m/Y H:i:s');
                file_put_contents(storage_path() . '/logs/results.log', "{$now}: {$url}\r\n", FILE_APPEND);

                $this->newLine();
                $this->info("All done!");
            }
            catch( \Exception $e ) {
                $this->error($e->getMessage());
            }

        } else {
            $this->newLine();
            $this->error(' Please include a URL as an argument ');
        }
    }
    
}
