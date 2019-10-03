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

/**
 * react runner
 */
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
     *
     * @param Server|StreamingServer $server       React server.
     * @param LoopInterface          $loop         React eventloop.
     * @param SocketServer           $socketServer React socket server.
     */
    public function __construct($server, LoopInterface $loop, SocketServer $socketServer)
    {
        assert($server instanceof Server || $server instanceof StreamingServer);

        $this->loop = $loop;
        $this->socketServer = $socketServer;
        $this->server = $server;
    }

    /**
     * run a bolt app with react
     *
     * @param array $config The application configuration.
     */
    public static function run($config = [])
    {
        $container = $config instanceof ContainerInterface
            ? $config
            : BoltContainerConfiguration::createContainer($config + ReactConfiguration::default());

        /**
         * @var static
         */
        $instance = Factory::of($container)->getOrProduce(static::class);
        $instance->work();
    }

    protected function work()
    {
        $this->server->listen($this->socketServer);
        $this->loop->run();
    }
}
