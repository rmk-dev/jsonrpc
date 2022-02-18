<?php

namespace RmkTests\JsonRpc;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Rmk\CallbackResolver\CallbackResolver;
use Rmk\Collections\Collection;
use Rmk\JsonRpc\ErrorResponse;
use Rmk\JsonRpc\JsonRpc;
use Rmk\JsonRpc\JsonRpcException;
use Rmk\JsonRpc\Request;
use Rmk\JsonRpc\SuccessResponse;

class JsonRpcTest extends TestCase
{

    private JsonRpc $jsonRpc;

    protected function setUp(): void
    {
        $callables = [
            'without_params' => static function() { return 1; },
            'single_param' => static function($a) { return $a; },
            'two_typed_params' => static function(string $a, string $b) { return $a.', '.$b; },
            'with_default_param' => static function(string $a, $b = 'C') { return $a.', '.$b; },
            'with_exception' => static function() { throw new \ErrorException('Throw exception'); }
        ];
        $callbacks = new Collection($callables);
        $container = $this->createStub(ContainerInterface::class);
        $container->method('has')->willReturnCallback(function ($name) use ($callables) {
            return array_key_exists($name, $callables);
        });
        $container->method('get')->willReturnCallback(function ($name) use ($callables) {
            return $callables[$name];
        });
        $resolver = new CallbackResolver($container);
        $this->jsonRpc = new JsonRpc($callbacks, $resolver);
    }

    public function testExecuteWithoutMethod(): void
    {
        $request = new Request(JsonRpc::VERSION, 1, 'unknown_method', []);
        $response = $this->jsonRpc->execute($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals('Method not found', $response->getMessage());
        $this->assertEquals(JsonRpcException::METHOD_NOT_FOUND, $response->getCode());
    }

    public function testExecuteWithInvalidParameters(): void
    {
        $request = new Request(JsonRpc::VERSION, 1, 'single_param', []);
        $response = $this->jsonRpc->execute($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals(JsonRpcException::INVALID_PARAMS, $response->getCode());
    }

    public function testExecute(): void
    {
        $request = new Request(JsonRpc::VERSION, 1, 'without_params', []);
        $response = $this->jsonRpc->execute($request);
        $this->assertInstanceOf(SuccessResponse::class, $response);
        $this->assertEquals(1, $response->getResult());

        $request = new Request(JsonRpc::VERSION, 2, 'single_param', [123]);
        $response = $this->jsonRpc->execute($request);
        $this->assertInstanceOf(SuccessResponse::class, $response);
        $this->assertEquals(123, $response->getResult());

        $request = new Request(JsonRpc::VERSION, 3, 'two_typed_params', ['b' => 'bb', 'a' => 'aa']);
        $response = $this->jsonRpc->execute($request);
        $this->assertInstanceOf(SuccessResponse::class, $response);
        $this->assertEquals('aa, bb', $response->getResult());

        $request = new Request(JsonRpc::VERSION, 4, 'with_default_param', ['a' => 'aa']);
        $response = $this->jsonRpc->execute($request);
        $this->assertInstanceOf(SuccessResponse::class, $response);
        $this->assertEquals('aa, C', $response->getResult());

        $request = new Request(JsonRpc::VERSION, 5, 'with_exception', []);
        $response = $this->jsonRpc->execute($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals('Throw exception', $response->getMessage());
    }
}
