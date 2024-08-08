<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleRetry;

use Staatic\Vendor\GuzzleHttp\Promise\Create;
use Closure;
use DateTime;
use Staatic\Vendor\GuzzleHttp\Exception\BadResponseException;
use Staatic\Vendor\GuzzleHttp\Exception\ConnectException;
use Staatic\Vendor\GuzzleHttp\Promise\Promise;
use Staatic\Vendor\GuzzleHttp\Promise\PromiseInterface;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use Throwable;
use function call_user_func;
use function call_user_func_array;
use function Staatic\Vendor\GuzzleHttp\Promise\rejection_for;
use function in_array;
use function is_callable;
class GuzzleRetryMiddleware
{
    public const DATE_FORMAT = 'D, d M Y H:i:s T';
    public const RETRY_HEADER = 'X-Retry-Counter';
    public const RETRY_AFTER_HEADER = 'Retry-After';
    public const REQUEST_METHODS_ALL = '*';
    private $defaultOptions = ['retry_enabled' => \true, 'default_retry_multiplier' => 1.5, 'max_retry_attempts' => 10, 'max_allowable_timeout_secs' => null, 'give_up_after_secs' => null, 'retry_only_if_retry_after_header' => \false, 'retry_on_status' => ['429', '503'], 'on_retry_callback' => null, 'retry_on_timeout' => \false, 'expose_retry_header' => \false, 'retry_header' => self::RETRY_HEADER, 'retry_after_header' => self::RETRY_AFTER_HEADER, 'retry_after_date_format' => self::DATE_FORMAT, 'retry_on_methods' => self::REQUEST_METHODS_ALL, 'should_retry_callback' => null];
    private $nextHandler;
    /**
     * @param mixed[] $defaultOptions
     */
    public static function factory($defaultOptions = []): Closure
    {
        return function (callable $handler) use ($defaultOptions): self {
            return new static($handler, $defaultOptions);
        };
    }
    final public function __construct(callable $nextHandler, array $defaultOptions = [])
    {
        $this->nextHandler = $nextHandler;
        $this->defaultOptions = array_replace($this->defaultOptions, $defaultOptions);
    }
    public function __invoke(RequestInterface $request, array $options): Promise
    {
        $options = array_replace($this->defaultOptions, $options);
        $options['request_timestamp'] = time();
        if (!isset($options['retry_count'])) {
            $options['retry_count'] = 0;
        }
        if ($options['retry_count'] === 0) {
            $options['first_request_timestamp'] = time();
        }
        $next = $this->nextHandler;
        return $next($request, $options)->then($this->onFulfilled($request, $options), $this->onRejected($request, $options));
    }
    /**
     * @param RequestInterface $request
     * @param mixed[] $options
     */
    protected function onFulfilled($request, $options): callable
    {
        return function (ResponseInterface $response) use ($request, $options) {
            return $this->shouldRetryHttpResponse($options, $request, $response) ? $this->doRetry($request, $options, $response) : $this->returnResponse($options, $response);
        };
    }
    /**
     * @param RequestInterface $request
     * @param mixed[] $options
     */
    protected function onRejected($request, $options): callable
    {
        return function (Throwable $reason) use ($request, $options): PromiseInterface {
            if ($reason instanceof BadResponseException) {
                if ($this->shouldRetryHttpResponse($options, $request, $reason->getResponse())) {
                    return $this->doRetry($request, $options, $reason->getResponse(), $reason);
                }
            } elseif ($reason instanceof ConnectException) {
                if ($this->shouldRetryConnectException($options, $request)) {
                    return $this->doRetry($request, $options, null, $reason);
                }
            }
            if (class_exists('Staatic\Vendor\GuzzleHttp\Promise\Create')) {
                return Create::rejectionFor($reason);
            } else {
                return rejection_for($reason);
            }
        };
    }
    /**
     * @param mixed[] $options
     * @param RequestInterface $request
     */
    protected function shouldRetryConnectException($options, $request): bool
    {
        return $options['retry_enabled'] && ($options['retry_on_timeout'] ?? \false) && $this->hasTimeAvailable($options) !== \false && $this->countRemainingRetries($options) > 0 && $this->ensureMethod($options, $request);
    }
    /**
     * @param mixed[] $options
     * @param RequestInterface $request
     */
    protected function ensureMethod($options, $request): bool
    {
        if ($options['retry_on_methods'] === self::REQUEST_METHODS_ALL) {
            return \true;
        } else {
            $reqMethods = array_map('strtoupper', (array) $options['retry_on_methods']);
            return in_array(strtoupper($request->getMethod()), $reqMethods);
        }
    }
    /**
     * @param mixed[] $options
     * @param RequestInterface $request
     * @param ResponseInterface|null $response
     */
    protected function shouldRetryHttpResponse($options, $request, $response = null): bool
    {
        $statuses = array_map('\intval', (array) $options['retry_on_status']);
        $hasRetryAfterHeader = $response && $response->hasHeader('Retry-After');
        if ((int) $options['give_up_after_secs'] > 0) {
            $delayTimeout = $this->determineDelayTimeout($options, $response);
            $nextRequestTime = (float) $options['first_request_timestamp'] + $delayTimeout;
            $giveUpAfterTime = (float) $options['first_request_timestamp'] + $options['give_up_after_secs'];
            if ($nextRequestTime > $giveUpAfterTime) {
                return \false;
            }
        }
        switch (\true) {
            case $options['retry_enabled'] === \false:
            case $this->hasTimeAvailable($options) === \false:
            case $this->countRemainingRetries($options) === 0:
            case !$this->ensureMethod($options, $request):
                return \false;
            case $options['should_retry_callback']:
                return (bool) call_user_func($options['should_retry_callback'], $options, $response);
            case !$hasRetryAfterHeader && $options['retry_only_if_retry_after_header']:
                return \false;
            default:
                $statusCode = $response ? $response->getStatusCode() : 0;
                return in_array($statusCode, $statuses, \true);
        }
    }
    /**
     * @param mixed[] $options
     */
    protected function countRemainingRetries($options): int
    {
        $retryCount = isset($options['retry_count']) ? (int) $options['retry_count'] : 0;
        $numAllowed = isset($options['max_retry_attempts']) ? (int) $options['max_retry_attempts'] : $this->defaultOptions['max_retry_attempts'];
        return (int) max([$numAllowed - $retryCount, 0]);
    }
    /**
     * @param RequestInterface $request
     * @param mixed[] $options
     * @param ResponseInterface|null $response
     * @param Throwable|null $exception
     */
    protected function doRetry($request, $options, $response = null, $exception = null): Promise
    {
        ++$options['retry_count'];
        $delayTimeout = $this->determineDelayTimeout($options, $response);
        if ($options['on_retry_callback']) {
            call_user_func_array($options['on_retry_callback'], [(int) $options['retry_count'], $delayTimeout, &$request, &$options, $response, $exception]);
        }
        usleep((int) ($delayTimeout * 1000000.0));
        return $this($request, $options);
    }
    /**
     * @param mixed[] $options
     * @param ResponseInterface $response
     */
    protected function returnResponse($options, $response): ResponseInterface
    {
        if ($options['expose_retry_header'] === \false || $options['retry_count'] === 0) {
            return $response;
        }
        return $response->withHeader($options['retry_header'], $options['retry_count']);
    }
    /**
     * @param mixed[] $options
     * @param ResponseInterface|null $response
     */
    protected function determineDelayTimeout($options, $response = null): float
    {
        $timeoutDerivedFromServer = \false;
        if (is_callable($options['default_retry_multiplier'])) {
            $defaultDelayTimeout = (float) call_user_func($options['default_retry_multiplier'], $options['retry_count'], $response);
        } else {
            $defaultDelayTimeout = (float) $options['default_retry_multiplier'] * $options['retry_count'];
        }
        if ($response && $response->hasHeader($options['retry_after_header'])) {
            $timeout = $this->deriveTimeoutFromHeader($response->getHeader($options['retry_after_header'])[0], $options['retry_after_date_format']);
            if ($timeout !== null) {
                $timeoutDerivedFromServer = \true;
            } else {
                $timeout = $defaultDelayTimeout;
            }
        } else {
            $timeout = abs($defaultDelayTimeout);
        }
        if (!is_null($options['max_allowable_timeout_secs']) && abs($options['max_allowable_timeout_secs']) > 0) {
            $timeout = min(abs($timeout), (float) abs($options['max_allowable_timeout_secs']));
        } else {
            $timeout = abs($timeout);
        }
        if ($options['give_up_after_secs'] && !$timeoutDerivedFromServer) {
            $giveUpAfterSecs = abs((float) $options['give_up_after_secs']);
            $timeSinceFirstReq = $options['request_timestamp'] - $options['first_request_timestamp'];
            $timeout = min($timeout, $giveUpAfterSecs - $timeSinceFirstReq);
        }
        return $timeout;
    }
    /**
     * @param mixed[] $options
     */
    protected function hasTimeAvailable($options): bool
    {
        if (!$options['give_up_after_secs']) {
            return \true;
        }
        $giveUpAfterTimestamp = $options['first_request_timestamp'] + abs(intval($options['give_up_after_secs']));
        return $options['request_timestamp'] < $giveUpAfterTimestamp;
    }
    /**
     * @param string $headerValue
     * @param string $dateFormat
     */
    protected function deriveTimeoutFromHeader($headerValue, $dateFormat = self::DATE_FORMAT): ?float
    {
        if (is_numeric($headerValue)) {
            return (float) trim($headerValue);
        } elseif ($date = DateTime::createFromFormat($dateFormat ?: self::DATE_FORMAT, trim($headerValue))) {
            return (float) $date->format('U') - time();
        }
        return null;
    }
}
