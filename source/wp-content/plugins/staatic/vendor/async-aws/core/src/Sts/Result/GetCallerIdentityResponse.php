<?php

namespace Staatic\Vendor\AsyncAws\Core\Sts\Result;

use SimpleXMLElement;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
class GetCallerIdentityResponse extends Result
{
    private $userId;
    private $account;
    private $arn;
    public function getAccount(): ?string
    {
        $this->initialize();
        return $this->account;
    }
    public function getArn(): ?string
    {
        $this->initialize();
        return $this->arn;
    }
    public function getUserId(): ?string
    {
        $this->initialize();
        return $this->userId;
    }
    /**
     * @param Response $response
     */
    protected function populateResult($response): void
    {
        $data = new SimpleXMLElement($response->getContent());
        $data = $data->GetCallerIdentityResult;
        $this->userId = ($v = $data->UserId) ? (string) $v : null;
        $this->account = ($v = $data->Account) ? (string) $v : null;
        $this->arn = ($v = $data->Arn) ? (string) $v : null;
    }
}
