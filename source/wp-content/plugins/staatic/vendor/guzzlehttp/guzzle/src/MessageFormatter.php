<?php

namespace Staatic\Vendor\GuzzleHttp;

use Staatic\Vendor\GuzzleHttp\Psr7\Message;
use Throwable;
use Staatic\Vendor\Psr\Http\Message\MessageInterface;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
class MessageFormatter implements MessageFormatterInterface
{
    public const CLF = '{hostname} {req_header_User-Agent} - [{date_common_log}] "{method} {target} HTTP/{version}" {code} {res_header_Content-Length}';
    public const DEBUG = ">>>>>>>>\n{request}\n<<<<<<<<\n{response}\n--------\n{error}";
    public const SHORT = '[{ts}] "{method} {target} HTTP/{version}" {code}';
    private $template;
    public function __construct(?string $template = self::CLF)
    {
        $this->template = $template ?: self::CLF;
    }
    /**
     * @param RequestInterface $request
     * @param ResponseInterface|null $response
     * @param Throwable|null $error
     */
    public function format($request, $response = null, $error = null): string
    {
        $cache = [];
        return \preg_replace_callback('/{\s*([A-Za-z_\-\.0-9]+)\s*}/', function (array $matches) use ($request, $response, $error, &$cache) {
            if (isset($cache[$matches[1]])) {
                return $cache[$matches[1]];
            }
            $result = '';
            switch ($matches[1]) {
                case 'request':
                    $result = Message::toString($request);
                    break;
                case 'response':
                    $result = $response ? Message::toString($response) : '';
                    break;
                case 'req_headers':
                    $result = \trim($request->getMethod() . ' ' . $request->getRequestTarget()) . ' HTTP/' . $request->getProtocolVersion() . "\r\n" . $this->headers($request);
                    break;
                case 'res_headers':
                    $result = $response ? \sprintf('HTTP/%s %d %s', $response->getProtocolVersion(), $response->getStatusCode(), $response->getReasonPhrase()) . "\r\n" . $this->headers($response) : 'NULL';
                    break;
                case 'req_body':
                    $result = $request->getBody()->__toString();
                    break;
                case 'res_body':
                    if (!$response instanceof ResponseInterface) {
                        $result = 'NULL';
                        break;
                    }
                    $body = $response->getBody();
                    if (!$body->isSeekable()) {
                        $result = 'RESPONSE_NOT_LOGGEABLE';
                        break;
                    }
                    $result = $response->getBody()->__toString();
                    break;
                case 'ts':
                case 'date_iso_8601':
                    $result = \gmdate('c');
                    break;
                case 'date_common_log':
                    $result = \date('d/M/Y:H:i:s O');
                    break;
                case 'method':
                    $result = $request->getMethod();
                    break;
                case 'version':
                    $result = $request->getProtocolVersion();
                    break;
                case 'uri':
                case 'url':
                    $result = $request->getUri()->__toString();
                    break;
                case 'target':
                    $result = $request->getRequestTarget();
                    break;
                case 'req_version':
                    $result = $request->getProtocolVersion();
                    break;
                case 'res_version':
                    $result = $response ? $response->getProtocolVersion() : 'NULL';
                    break;
                case 'host':
                    $result = $request->getHeaderLine('Host');
                    break;
                case 'hostname':
                    $result = \gethostname();
                    break;
                case 'code':
                    $result = $response ? $response->getStatusCode() : 'NULL';
                    break;
                case 'phrase':
                    $result = $response ? $response->getReasonPhrase() : 'NULL';
                    break;
                case 'error':
                    $result = $error ? $error->getMessage() : 'NULL';
                    break;
                default:
                    if (\strpos($matches[1], 'req_header_') === 0) {
                        $result = $request->getHeaderLine(\substr($matches[1], 11));
                    } elseif (\strpos($matches[1], 'res_header_') === 0) {
                        $result = $response ? $response->getHeaderLine(\substr($matches[1], 11)) : 'NULL';
                    }
            }
            $cache[$matches[1]] = $result;
            return $result;
        }, $this->template);
    }
    private function headers(MessageInterface $message): string
    {
        $result = '';
        foreach ($message->getHeaders() as $name => $values) {
            $result .= $name . ': ' . \implode(', ', $values) . "\r\n";
        }
        return \trim($result);
    }
}
