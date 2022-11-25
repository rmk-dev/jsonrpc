<?php

namespace Rmk\JsonRpc;

/**
 * Interface for JSON-RPC request or response
 */
interface JsonRpcMessageInterface
{

    /**
     * The message identification if any
     *
     * @return mixed
     */
    public function getId(): mixed;
}
