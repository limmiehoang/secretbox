<?php

namespace SecretBox\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Http\Router;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use SecretBox\Http\Controllers\WebSocketController;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class FileUploadServer extends WsServer
{
    public function __construct()
    {
        parent::__construct(new WebSocketController());
    }
}

class WebSocketServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializing Websocket server to receive and manage connections';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $collection = new RouteCollection();

        $collection->add('file-upload', new Route('/upload', array(
            '_controller' => FileUploadServer::class
        )));

       $urlMatcher = new UrlMatcher($collection, new RequestContext());
       $router = new Router($urlMatcher);

        $server = IoServer::factory(
            new HttpServer(
                $router
            ),
            8090
        );
        $server->run();
    }
}
