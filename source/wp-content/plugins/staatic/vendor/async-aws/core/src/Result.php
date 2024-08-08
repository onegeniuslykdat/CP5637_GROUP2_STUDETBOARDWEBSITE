<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core;

use Staatic\Vendor\AsyncAws\Core\Exception\Http\HttpException;
use Staatic\Vendor\AsyncAws\Core\Exception\Http\NetworkException;
class Result
{
    protected $awsClient;
    protected $input;
    private $initialized = \false;
    private $response;
    private $prefetchResults = [];
    public function __construct(Response $response, ?AbstractApi $awsClient = null, $request = null)
    {
        $this->response = $response;
        $this->awsClient = $awsClient;
        $this->input = $request;
    }
    public function __destruct()
    {
        while (!empty($this->prefetchResults)) {
            array_shift($this->prefetchResults)->cancel();
        }
    }
    /**
     * @param float|null $timeout
     */
    final public function resolve($timeout = null): bool
    {
        return $this->response->resolve($timeout);
    }
    /**
     * @param iterable $results
     * @param float|null $timeout
     * @param bool $downloadBody
     */
    final public static function wait($results, $timeout = null, $downloadBody = \false): iterable
    {
        $resultMap = [];
        $responses = [];
        foreach ($results as $index => $result) {
            $responses[$index] = $result->response;
            $resultMap[$index] = $result;
        }
        foreach (Response::wait($responses, $timeout, $downloadBody) as $index => $response) {
            yield $index => $resultMap[$index];
        }
    }
    final public function info(): array
    {
        return $this->response->info();
    }
    final public function cancel(): void
    {
        $this->response->cancel();
    }
    /**
     * @param $this $result
     */
    final protected function registerPrefetch($result): void
    {
        $this->prefetchResults[spl_object_id($result)] = $result;
    }
    /**
     * @param $this $result
     */
    final protected function unregisterPrefetch($result): void
    {
        unset($this->prefetchResults[spl_object_id($result)]);
    }
    final protected function initialize(): void
    {
        if ($this->initialized) {
            return;
        }
        $this->resolve();
        $this->initialized = \true;
        $this->populateResult($this->response);
    }
    /**
     * @param Response $response
     */
    protected function populateResult($response): void
    {
    }
}
