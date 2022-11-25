<?php

namespace Rmk\JsonRpc;

use stdClass;

/**
 * Request object for JSON-RPC call
 *
 * It contains the request details like the procedure's name, parameters,
 * the request id, etc.
 */
class Request implements JsonRpcMessageInterface
{
    /**
     * JSON-RPC version
     *
     * @var string
     */
    protected string $jsonRpc;

    /**
     * The request id
     *
     * @var mixed
     */
    protected mixed $id;

    /**
     * The procedure name
     *
     * @var string
     */
    protected string $method;

    /**
     * Parameters for calling the procedure
     *
     * @var array
     */
    protected array $params;

    /**
     * Create new JSON-RPC request object
     *
     * @param string $jsonRpc The JSON-RPC version
     * @param mixed  $id      The request id
     * @param string $method  The procedure's name
     * @param array  $params  The procedure's parameters
     */
    public function __construct(string $jsonRpc, mixed $id, string $method, array $params)
    {
        $this->jsonRpc = $jsonRpc;
        $this->id = $id;
        $this->method = $method;
        $this->params = $params;
    }

    /**
     * The JSON-RPC version
     *
     * @return string
     */
    public function getJsonRpc(): string
    {
        return $this->jsonRpc;
    }

    /**
     * The request id
     *
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * The procedure's name
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * The procedure's parameters
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Create new request object from parsed body of PSR-7 request
     *
     * @param array $body
     *
     * @return JsonRpcMessageInterface
     */
    public static function fromParsedRequestBody(array $body): JsonRpcMessageInterface
    {
        $id = $body['id'] ?? null;
        if (!isset($body['jsonrpc']) || $body['jsonrpc'] !== JsonRpc::VERSION) {
            $return = new ErrorResponse($id, JsonRpcException::INVALID_REQUEST, 'Invalid JSON-RPC request');
        } elseif (!isset($body['method'])) {
            $return = new ErrorResponse($id, JsonRpcException::INVALID_REQUEST, 'No RPC method');
        } elseif (str_contains($body['method'], 'rpc')) {
            $return = new ErrorResponse($id, JsonRpcException::INVALID_REQUEST, 'Invalid request method');
        } else {
            $return = new Request(JsonRpc::VERSION, $id, $body['method'], (array) ($body['params'] ?? []));
        }

        return $return;
    }
}
