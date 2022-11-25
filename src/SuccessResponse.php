<?php

namespace Rmk\JsonRpc;

/**
 * Success response class
 *
 * Create such response when the request is not a notification and the execution
 * passed without errors.
 */
class SuccessResponse extends Response
{
    /**
     * The procedure's result
     *
     * @var mixed
     */
    protected mixed $result;

    /**
     * Create new success response object
     *
     * @param mixed $id     The response id.
     * @param mixed $result The execution result.
     */
    public function __construct(mixed $id, mixed $result)
    {
        parent::__construct($id);
        $this->result = $result;
    }

    /**
     * The execution result.
     *
     * @return mixed
     */
    public function getResult(): mixed
    {
        return $this->result;
    }

    /**
     * Return the result
     *
     * @return array
     */
    public function respond(): array
    {
        return [
            'result' => $this->getResult()
        ];
    }
}
