<?php

namespace Rmk\JsonRpc;

use JsonSerializable;

abstract class Response implements JsonSerializable, JsonRpcMessageInterface
{

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @param mixed $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    abstract public function respond(): array;

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $response = $this->respond();
        $response['id'] = $this->getId();
        $response['jsonrpc'] = '2.0';

        return $response;
    }
}
