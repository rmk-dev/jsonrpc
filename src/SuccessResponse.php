<?php

namespace Rmk\JsonRpc;

class SuccessResponse extends Response
{

    protected $result;

    /**
     * @param mixed $id
     * @param mixed $result
     */
    public function __construct(mixed $id, mixed $result)
    {
        parent::__construct($id);
        $this->result = $result;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    public function respond(): array
    {
        return [
            'result' => $this->getResult()
        ];
    }
}
