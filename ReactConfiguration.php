<?php

declare(strict_types=1);

namespace Lit\Runner\React;

use Lit\Air\Configurator as C;
use Lit\Bolt\BoltApp;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Http\Response;
use React\Http\Server;
use React\Http\StreamingServer;
use React\Socket\Server as SocketServer;

class ReactConfiguration
{
    public static function default()
    {
        $serverConfiguration = C::provideParameter([
            function (BoltApp $app) {
                return function (ServerRequestInterface $request) use ($app) {
                    return $app->handle($request);
                };
            }
        ]);

        return [
            LoopInterface::class => [Factory::class, 'create'],
            SocketServer::class => C::provideParameter([
                $_ENV['LISTEN'] ?? 8080
            ]),

            Server::class => $serverConfiguration,
            StreamingServer::class => $serverConfiguration,

            ReactRunner::class => C::provideParameter([
                'server' => C::alias(ReactRunner::class, 'server'),
            ]),
            C::join(ReactRunner::class, 'server') => C::alias(StreamingServer::class),

            ResponseFactoryInterface::class => new class implements ResponseFactoryInterface
            {
                public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
                {
                    return new Response($code, [], null, '1,1', $reasonPhrase);
                }
            }
        ];
    }
}
