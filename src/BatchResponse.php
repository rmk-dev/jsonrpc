<?php

namespace Rmk\JsonRpc;

use Rmk\Collections\BaseClassCollection;

/**
 * Collection with responses of batch request
 */
class BatchResponse extends BaseClassCollection
{

    /**
     * Create new batch response object
     *
     * @param iterable $data
     */
    public function __construct(iterable $data = [])
    {
        parent::__construct(Response::class, $data);
    }

    /**
     * @inheritDoc
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->filter(static function (Response $response) {
            return !($response instanceof NotificationResponse);
        })->getArrayCopy();
    }
}
