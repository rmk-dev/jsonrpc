<?php

namespace RmkTests\JsonRpc;

use PHPUnit\Framework\TestCase;
use Rmk\JsonRpc\SuccessResponse;

class SuccessResponseTest extends TestCase
{

    public function testRespond(): void
    {
        $result = 123;
        $response = new SuccessResponse(1, $result);
        $this->assertEquals(['result' => $result], $response->respond());
    }
}
