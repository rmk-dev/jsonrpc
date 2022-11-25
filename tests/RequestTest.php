<?php

namespace RmkTests\JsonRpc;

use Rmk\JsonRpc\ErrorResponse;
use Rmk\JsonRpc\JsonRpcException;
use Rmk\JsonRpc\Request;
use PHPUnit\Framework\TestCase;
use Rmk\JsonRpc\JsonRpc;

class RequestTest extends TestCase
{

    public function testGetters(): void
    {
        $request = new Request(JsonRpc::VERSION, 1, 'test_method', [1, 2]);
        $this->assertEquals(JsonRpc::VERSION, $request->getJsonRpc());
        $this->assertEquals(1, $request->getId());
        $this->assertEquals('test_method', $request->getMethod());
        $this->assertEquals([1, 2], $request->getParams());
    }

    public function testCreateFromParsedRequestBody(): void
    {
        $body = [];
        $request = Request::fromParsedRequestBody($body);
        $this->assertInstanceOf(ErrorResponse::class, $request);
        $this->assertEquals(JsonRpcException::INVALID_REQUEST, $request->getCode());

        $body['jsonrpc'] = JsonRpc::VERSION;
        $request = Request::fromParsedRequestBody($body);
        $this->assertInstanceOf(ErrorResponse::class, $request);
        $this->assertEquals(JsonRpcException::INVALID_REQUEST, $request->getCode());
        $this->assertEquals('No RPC method', $request->getMessage());

        $body['method'] = 'rpcTestMethod';
        $request = Request::fromParsedRequestBody($body);
        $this->assertInstanceOf(ErrorResponse::class, $request);
        $this->assertEquals(JsonRpcException::INVALID_REQUEST, $request->getCode());
        $this->assertEquals('Invalid request method', $request->getMessage());

        $body['method'] = 'test_method';
        $body['id'] = 1;
        $request = Request::fromParsedRequestBody($body);
        $this->assertInstanceOf(Request::class, $request);
        $this->assertEquals(1, $request->getId());
        $this->assertEquals('test_method', $request->getMethod());
    }
}