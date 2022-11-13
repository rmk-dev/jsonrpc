<?php

namespace Rmk\JsonRpc;

use Closure;
use ReflectionException;
use ReflectionFunction;
use ReflectionParameter;
use Throwable;
use Rmk\CallbackResolver\CallbackResolver;
use Rmk\Collections\Collection;

class JsonRpc
{
    /**
     * The current supported version
     */
    public const VERSION = '2.0';

    /**
     * Collection of callbacks that can be remotely executed
     *
     * @var Collection
     */
    protected Collection $callbacks;

    /**
     * Resolver for the procedure
     *
     * @var CallbackResolver
     */
    protected CallbackResolver $resolver;

    /**
     * Create new JSON-RPC service object
     *
     * @param Collection       $callbacks Collection with callbacks
     * @param CallbackResolver $resolver  Callback resolver
     */
    public function __construct(Collection $callbacks, CallbackResolver $resolver)
    {
        $this->callbacks = $callbacks;
        $this->resolver = $resolver;
    }

    /**
     * Executes the remote-called procedure with all the request parameters
     *
     * @param Request $request The JSON-RPC request
     *
     * @return Response Response with the procedure result.
     */
    public function execute(Request $request): Response
    {
        $method = $request->getMethod();
        $args = $request->getParams();
        $id = $request->getId();
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

            if ($id !== null) {
                return new SuccessResponse($id, $result);
            }

            return new NotificationResponse();
        } catch (Throwable $exception) {
            $code = $exception instanceof JsonRpcException ? $exception->getCode() : JsonRpcException::INTERNAL_ERROR;

            return new ErrorResponse($request->getId(), $code, $exception->getMessage(), $exception->getTrace());
        }
    }

    /**
     * Define parameters for the called procedure
     *
     * @param callable $callback The called procedure
     * @param array    $args     The parameters, send with the request
     *
     * @return array Parameters for the callback, ordered with their names and values
     *
     * @throws JsonRpcException    If no value is sent and no default value present in the callback definition.
     * @throws ReflectionException If the procedure is not a valid callback
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
     * Define value for a single parameter
     *
     * @param array                $args     Parameters sent with the request
     * @param ReflectionParameter $parameter Reflection object for the current parameter
     *
     * @return mixed The parameter's value, extracted from the request parameters
     *
     * @throws JsonRpcException If no value is sent and no default value present in the callback definition.
     */
    protected function getParam(array $args, ReflectionParameter $parameter): mixed
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
