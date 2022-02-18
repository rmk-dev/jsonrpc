<?php

namespace RmkTests\JsonRpc;

use PHPUnit\Framework\TestCase;
use Rmk\JsonRpc\JsonRpcException;

class JsonRpcExceptionTest extends TestCase
{

    public function testGetters(): void
    {
        $exception = new JsonRpcException('Internal Error', JsonRpcException::INTERNAL_ERROR, 1, 200);
        $this->assertEquals(200, $exception->getHttpStatus());
        $this->assertEquals(1, $exception->getId());
    }
}