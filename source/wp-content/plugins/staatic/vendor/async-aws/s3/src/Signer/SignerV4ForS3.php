<?php

namespace Staatic\Vendor\AsyncAws\S3\Signer;

use DateTimeImmutable;
use Staatic\Vendor\AsyncAws\Core\Configuration;
use Staatic\Vendor\AsyncAws\Core\Credentials\Credentials;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\RequestContext;
use Staatic\Vendor\AsyncAws\Core\Signer\SignerV4;
use Staatic\Vendor\AsyncAws\Core\Signer\SigningContext;
use Staatic\Vendor\AsyncAws\Core\Stream\FixedSizeStream;
use Staatic\Vendor\AsyncAws\Core\Stream\IterableStream;
use Staatic\Vendor\AsyncAws\Core\Stream\ReadOnceResultStream;
use Staatic\Vendor\AsyncAws\Core\Stream\RequestStream;
use Staatic\Vendor\AsyncAws\Core\Stream\RewindableStream;
class SignerV4ForS3 extends SignerV4
{
    private const ALGORITHM_CHUNK = 'AWS4-HMAC-SHA256-PAYLOAD';
    private const CHUNK_SIZE = 64 * 1024;
    private const MD5_OPERATIONS = ['DeleteObjects' => \true, 'PutBucketCors' => \true, 'PutBucketLifecycle' => \true, 'PutBucketLifecycleConfiguration' => \true, 'PutBucketPolicy' => \true, 'PutBucketTagging' => \true, 'PutBucketReplication' => \true, 'PutObjectLegalHold' => \true, 'PutObjectRetention' => \true, 'PutObjectLockConfiguration' => \true];
    private $sendChunkedBody;
    public function __construct(string $scopeName, string $region, array $s3SignerOptions = [])
    {
        parent::__construct($scopeName, $region);
        $this->sendChunkedBody = $s3SignerOptions[Configuration::OPTION_SEND_CHUNKED_BODY] ?? \false;
        unset($s3SignerOptions[Configuration::OPTION_SEND_CHUNKED_BODY]);
        if (!empty($s3SignerOptions)) {
            throw new InvalidArgument(sprintf('Invalid option(s) "%s" passed to "%s::%s". ', implode('", "', array_keys($s3SignerOptions)), __CLASS__, __METHOD__));
        }
    }
    /**
     * @param Request $request
     * @param Credentials $credentials
     * @param RequestContext $context
     */
    public function sign($request, $credentials, $context): void
    {
        if ((null === ($operation = $context->getOperation()) || isset(self::MD5_OPERATIONS[$operation])) && !$request->hasHeader('content-md5')) {
            $request->setHeader('content-md5', base64_encode($request->getBody()->hash('md5', \true)));
        }
        if (!$request->hasHeader('x-amz-content-sha256')) {
            $request->setHeader('x-amz-content-sha256', $request->getBody()->hash());
        }
        parent::sign($request, $credentials, $context);
    }
    /**
     * @param Request $request
     * @param bool $isPresign
     */
    protected function buildBodyDigest($request, $isPresign): string
    {
        if ($isPresign) {
            $request->setHeader('x-amz-content-sha256', 'UNSIGNED-PAYLOAD');
            return 'UNSIGNED-PAYLOAD';
        }
        return parent::buildBodyDigest($request, $isPresign);
    }
    /**
     * @param Request $request
     */
    protected function buildCanonicalPath($request): string
    {
        return '/' . ltrim($request->getUri(), '/');
    }
    /**
     * @param SigningContext $context
     */
    protected function convertBodyToStream($context): void
    {
        $request = $context->getRequest();
        $body = $request->getBody();
        if ($request->hasHeader('content-length')) {
            $contentLength = (int) $request->getHeader('content-length');
        } else {
            $contentLength = $body->length();
        }
        if (null === $contentLength) {
            $request->setBody($body = RewindableStream::create($body));
            $body->read();
            $contentLength = $body->length();
        }
        if ($contentLength < self::CHUNK_SIZE || !$this->sendChunkedBody) {
            if ($body instanceof ReadOnceResultStream) {
                $request->setBody(RewindableStream::create($body));
            }
            return;
        }
        $customEncoding = $request->getHeader('content-encoding');
        // $request->setHeader('content-encoding', $customEncoding ? "aws-chunked, {$customEncoding}" : 'aws-chunked');
        $request->setHeader('x-amz-decoded-content-length', (string) $contentLength);
        $request->setHeader('x-amz-content-sha256', 'STREAMING-' . self::ALGORITHM_CHUNK);
        $chunkCount = (int) ceil($contentLength / self::CHUNK_SIZE);
        $fullChunkCount = ($chunkCount * self::CHUNK_SIZE === $contentLength) ? $chunkCount : ($chunkCount - 1);
        $metaLength = \strlen(";chunk-signature=\r\n\r\n") + 64;
        $request->setHeader('content-length', (string) ($contentLength + $fullChunkCount * ($metaLength + \strlen(dechex(self::CHUNK_SIZE))) + ($chunkCount - $fullChunkCount) * ($metaLength + \strlen(dechex($contentLength % self::CHUNK_SIZE))) + $metaLength + 1));
        $body = RewindableStream::create(IterableStream::create((function (RequestStream $body) use ($context): iterable {
            $now = $context->getNow();
            $credentialString = $context->getCredentialString();
            $signingKey = $context->getSigningKey();
            $signature = $context->getSignature();
            foreach (FixedSizeStream::create($body, self::CHUNK_SIZE) as $chunk) {
                $stringToSign = $this->buildChunkStringToSign($now, $credentialString, $signature, $chunk);
                $context->setSignature($signature = $this->buildSignature($stringToSign, $signingKey));
                yield sprintf("%s;chunk-signature=%s\r\n", dechex(\strlen($chunk)), $signature) . "{$chunk}\r\n";
            }
            $stringToSign = $this->buildChunkStringToSign($now, $credentialString, $signature, '');
            $context->setSignature($signature = $this->buildSignature($stringToSign, $signingKey));
            yield sprintf("%s;chunk-signature=%s\r\n\r\n", dechex(0), $signature);
        })($body)));
        $request->setBody($body);
    }
    private function buildChunkStringToSign(DateTimeImmutable $now, string $credentialString, string $signature, string $chunk): string
    {
        static $emptyHash;
        $emptyHash = $emptyHash ?? hash('sha256', '');
        return implode("\n", [self::ALGORITHM_CHUNK, $now->format('Ymd\\THis\\Z'), $credentialString, $signature, $emptyHash, hash('sha256', $chunk)]);
    }
    private function buildSignature(string $stringToSign, string $signingKey): string
    {
        return hash_hmac('sha256', $stringToSign, $signingKey);
    }
}
