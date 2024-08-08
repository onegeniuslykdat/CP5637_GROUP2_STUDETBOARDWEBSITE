<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Chunk;

use Staatic\Vendor\Symfony\Contracts\HttpClient\ChunkInterface;
final class ServerSentEvent extends DataChunk implements ChunkInterface
{
    /**
     * @var string
     */
    private $data = '';
    /**
     * @var string
     */
    private $id = '';
    /**
     * @var string
     */
    private $type = 'message';
    /**
     * @var float
     */
    private $retry = 0;
    public function __construct(string $content)
    {
        parent::__construct(-1, $content);
        if (strncmp($content, "﻿", strlen("﻿")) === 0) {
            $content = substr($content, 3);
        }
        foreach (preg_split("/(?:\r\n|[\r\n])/", $content) as $line) {
            if (0 === $i = strpos($line, ':')) {
                continue;
            }
            $i = (\false === $i) ? \strlen($line) : $i;
            $field = substr($line, 0, $i);
            $i += 1 + (' ' === ($line[1 + $i] ?? ''));
            switch ($field) {
                case 'id':
                    $this->id = substr($line, $i);
                    break;
                case 'event':
                    $this->type = substr($line, $i);
                    break;
                case 'data':
                    $this->data .= (('' === $this->data) ? '' : "\n") . substr($line, $i);
                    break;
                case 'retry':
                    $retry = substr($line, $i);
                    if ('' !== $retry && \strlen($retry) === strspn($retry, '0123456789')) {
                        $this->retry = $retry / 1000.0;
                    }
                    break;
            }
        }
    }
    public function getId(): string
    {
        return $this->id;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function getData(): string
    {
        return $this->data;
    }
    public function getRetry(): float
    {
        return $this->retry;
    }
}
