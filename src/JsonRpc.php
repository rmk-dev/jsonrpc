<?php

namespace Rmk\JsonRpc;

use Closure;
use ReflectionException;
use ReflectionFunction;
use Throwable;
use Rmk\CallbackResolver\CallbackResolver;
use Rmk\Collections\Collection;

class JsonRpc
{
    public const VERSION = '2.0';

    /**
     * @var Collection
     */
    protected Collection $callbacks;

    /**
     * @var CallbackResolver
     */
    protected CallbackResolver $resolver;

    /**
     * @param Collection $callbacks
     * @param CallbackResolver $resolver
     */
    public function __construct(Collection $callbacks, CallbackResolver $resolver)
    {
        $this->callbacks = $callbacks;
        $this->resolver = $resolver;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function execute(Request $request): Response
    {
        $method = $request->getMethod();
        $args = $request->getParams();
        try {
            if (!$this->callbacks->has($method)) {
                throw new JsonRpcException(
                    'Method not found',
                    JsonRpcException::METHOD_NOT_FOUND,
                    $request->getId()
                );
            }
            $callback = $this->resolver->resolve($this->callbacks->get($method));
            $result = call_user_func_array($callback, $this->defineParams($callback, $args));

            return new SuccessResponse($request->getId(), $result);
        } catch (Throwable $exception) {
            $code = $exception instanceof JsonRpcException ? $exception->getCode() : JsonRpcException::INTERNAL_ERROR;

            return new ErrorResponse($request->getId(), $code, $exception->getMessage(), $exception->getTrace());
        }
    }

    /**
     * @param callable $callback
     * @param array $args
     *
     * @return array
     *
     * @throws JsonRpcException
     * @throws ReflectionException
     */
    protected function defineParams(callable $callback, array $args): array
    {
        $ref = new ReflectionFunction(Closure::fromCallable($callback));
        $params = [];
        foreach ($ref->getParameters() as $parameter) {
            $params[$parameter->getName()] = $this->getParam($args, $parameter);
        }

        return $params;
    }

    /**
     * @param array $args
     * @param \ReflectionParameter $parameter
     *
     * @return mixed
     *
     * @throws JsonRpcException
     */
    protected function getParam(array $args, \ReflectionParameter $parameter)
    {
        if (array_key_exists($parameter->getName(), $args)) {
            return $args[$parameter->getName()];
        }

        if (array_key_exists($parameter->getPosition(), $args)) {
            return $args[$parameter->getPosition()];
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new JsonRpcException(
            'No value for parameter ' . $parameter->getName(),
            JsonRpcException::INVALID_PARAMS
        );
    }
}
