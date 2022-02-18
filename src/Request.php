<?php

namespace Rmk\JsonRpc;

use stdClass;

class Request implements JsonRpcMessageInterface
{

    /**
     * @var string
     */
    protected string $jsonrpc;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected string $method;

    /**
     * @var array
     */
    protected array $params;

    /**
     * @param string $jsonrpc
     * @param mixed $id
     * @param string $method
     * @param stdClass|array $params
     */
    public function __construct(string $jsonrpc, $id, string $method, $params)
    {
        $this->jsonrpc = $jsonrpc;
        $this->id = $id;
        $this->method = $method;
        $this->params = (array) $params;
    }

    /**
     * @return mixed
     */
    public function getJsonrpc()
    {
        return $this->jsonrpc;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param stdClass $body
     *
     * @return JsonRpcMessageInterface
     */
    public static function fromParsedRequestBody(stdClass $body): JsonRpcMessageInterface
    {
        $id = $body->id ?? null;
        if (!isset($body->jsonrpc) || $body->jsonrpc !== JsonRpc::VERSION) {
            $return = new ErrorResponse($id, JsonRpcException::INVALID_REQUEST, 'Invalid JSON-RPC request');
        } else if (!isset($body->method)) {
            $return = new ErrorResponse($id, JsonRpcException::INVALID_REQUEST, 'No RPC method');
        } else if (strstr($body->method, 'rpc')) {
            $return = new ErrorResponse($id, JsonRpcException::INVALID_REQUEST, 'Invalid request method');
        } else {
            $return = new Request(JsonRpc::VERSION, $id, $body->method, $body->params ?? []);
        }

        return $return;
    }
}