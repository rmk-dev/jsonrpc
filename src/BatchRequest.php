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
        parent::__construct(Request::class, $data);
    }
}
