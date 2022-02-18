<?php

namespace RmkTests\JsonRpc;

use PHPUnit\Framework\TestCase;
use Rmk\JsonRpc\ErrorResponse;
use Rmk\JsonRpc\JsonRpcException;

class ErrorResponseTest extends TestCase
{

    public function testGetters(): void
    {
        $response = new ErrorResponse(1);
        $this->assertEquals(1, $response->getId());
        $this->assertEquals(JsonRpcException::INTERNAL_ERROR, $response->getCode());
        $this->assertEquals('Internal Error', $response->getMessage());
        $this->assertNull($response->getData());
        $response = new ErrorResponse(2, JsonRpcException::INVALID_PARAMS, 'Invalid Params', [1, 2]);
        $this->assertEquals(2, $response->getId());
        $this->assertEquals(JsonRpcException::INVALID_PARAMS, $response->getCode());
        $this->assertEquals('Invalid Params', $response->getMessage());
        $this->assertEquals([1, 2], $response->getData());
    }

    public function testRespondAndSerializing(): void
    {
        $errorData = [
            'code' => JsonRpcException::INTERNAL_ERROR,
            'message' => 'Internal Error',
            'data' => [1, 2]
        ];
        $response = new ErrorResponse(3, $errorData['code'], $errorData['message'], $errorData['data']);
        $this->assertEquals(['error' => $errorData], $response->respond());
        $serialized = ['error' => $errorData, 'id' => 3, 'jsonrpc' => '2.0'];
        $this->assertEquals(json_encode($serialized), json_encode($response));
    }
}
