<?php

declare(strict_types=1);

namespace Lit\Runner\React;

use Lit\Air\Factory;
use Lit\Bolt\BoltContainerConfiguration;
use Psr\Container\ContainerInterface;
use React\EventLoop\LoopInterface;
use React\Http\Server;
use React\Http\StreamingServer;
use React\Socket\Server as SocketServer;

class ReactRunner
{
    /**
     * @var LoopInterface
     */
    protected $loop;
    /**
     * @var SocketServer
     */
    protected $socketServer;
    /**
     * @var Server|StreamingServer
     */
    protected $server;

    /**
     * ReactRunner constructor.
     * @param Server|StreamingServer $server
     * @param LoopInterface $loop
     * @param SocketServer $socketServer
     */
    public function __construct($server, LoopInterface $loop, SocketServer $socketServer)
    {
        assert($server instanceof Server || $server instanceof StreamingServer);

        $this->loop = $loop;
        $this->socketServer = $socketServer;
        $this->server = $server;
    }

    public static function run($config = [])
    {
        $container = $config instanceof ContainerInterface
            ? $config
            : BoltContainerConfiguration::createContainer($config + ReactConfiguration::default());

        Factory::of($container)->getOrProduce(static::class)->work();
    }

    public function work()
    {
        $this->server->listen($this->socketServer);
        $this->loop->run();
    }
}
