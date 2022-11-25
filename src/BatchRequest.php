<?php

namespace Rmk\JsonRpc;

use Rmk\Collections\BaseClassCollection;

/**
 * Collection of multiple requests for batch execution
 */
class BatchRequest extends BaseClassCollection
{
    /**
     * Create new batch request object
     *
     * @param array $data Dataset with Request objects
     */
    public function __construct(array $data = [])
    {
        parent::__construct(JsonRpcMessageInterface::class, $data);
    }

    /**
     * Create batch collection from PSR7 parsed request
     *
     * @param array $body Parsed body of PSR7 request
     *
     * @return static Collection with requests and/or error responses
     */
    public static function fromParsedRequestBody(array $body): self
    {
        $batch = new static();
        foreach ($body as $request) {
            $batch->append(Request::fromParsedRequestBody($request));
        }

        return $batch;
    }
}
