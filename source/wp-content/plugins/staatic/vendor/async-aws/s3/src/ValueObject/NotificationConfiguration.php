<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
final class NotificationConfiguration
{
    private $topicConfigurations;
    private $queueConfigurations;
    private $lambdaFunctionConfigurations;
    private $eventBridgeConfiguration;
    public function __construct(array $input)
    {
        $this->topicConfigurations = isset($input['TopicConfigurations']) ? array_map([TopicConfiguration::class, 'create'], $input['TopicConfigurations']) : null;
        $this->queueConfigurations = isset($input['QueueConfigurations']) ? array_map([QueueConfiguration::class, 'create'], $input['QueueConfigurations']) : null;
        $this->lambdaFunctionConfigurations = isset($input['LambdaFunctionConfigurations']) ? array_map([LambdaFunctionConfiguration::class, 'create'], $input['LambdaFunctionConfigurations']) : null;
        $this->eventBridgeConfiguration = isset($input['EventBridgeConfiguration']) ? EventBridgeConfiguration::create($input['EventBridgeConfiguration']) : null;
    }
    public static function create($input): self
    {
        return ($input instanceof self) ? $input : new self($input);
    }
    public function getEventBridgeConfiguration(): ?EventBridgeConfiguration
    {
        return $this->eventBridgeConfiguration;
    }
    public function getLambdaFunctionConfigurations(): array
    {
        return $this->lambdaFunctionConfigurations ?? [];
    }
    public function getQueueConfigurations(): array
    {
        return $this->queueConfigurations ?? [];
    }
    public function getTopicConfigurations(): array
    {
        return $this->topicConfigurations ?? [];
    }
    public function requestBody(DOMElement $node, DOMDocument $document): void
    {
        if (null !== $v = $this->topicConfigurations) {
            foreach ($v as $item) {
                $node->appendChild($child = $document->createElement('TopicConfiguration'));
                $item->requestBody($child, $document);
            }
        }
        if (null !== $v = $this->queueConfigurations) {
            foreach ($v as $item) {
                $node->appendChild($child = $document->createElement('QueueConfiguration'));
                $item->requestBody($child, $document);
            }
        }
        if (null !== $v = $this->lambdaFunctionConfigurations) {
            foreach ($v as $item) {
                $node->appendChild($child = $document->createElement('CloudFunctionConfiguration'));
                $item->requestBody($child, $document);
            }
        }
        if (null !== $v = $this->eventBridgeConfiguration) {
            $node->appendChild($child = $document->createElement('EventBridgeConfiguration'));
            $v->requestBody($child, $document);
        }
    }
}
