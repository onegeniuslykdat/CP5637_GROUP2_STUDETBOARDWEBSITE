<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page\PublicationResults;

use Staatic\Vendor\GuzzleHttp\Psr7\Exception\MalformedUriException;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Framework\Result;
use Staatic\WordPress\Bridge\ResultRepository;
use Staatic\WordPress\ListTable\AbstractListTable;
use Staatic\WordPress\ListTable\Column\BytesColumn;
use Staatic\WordPress\ListTable\Column\DateColumn;
use Staatic\WordPress\ListTable\Column\NumberColumn;
use Staatic\WordPress\ListTable\Column\TextColumn;
use Staatic\WordPress\ListTable\Decorator\CallbackDecorator;
use Staatic\WordPress\ListTable\Decorator\LinkDecorator;
use Staatic\WordPress\ListTable\Decorator\TitleDecorator;
use Staatic\WordPress\ListTable\View\View;
use Staatic\WordPress\Service\Formatter;

class PublicationResultsTable extends AbstractListTable
{
    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @var ResultRepository
     */
    private $repository;

    /** @var string */
    protected const NAME = 'result_list_table';

    public function __construct(Formatter $formatter, ResultRepository $repository)
    {
        $this->formatter = $formatter;
        $this->repository = $repository;
        parent::__construct('id', ['date_created', 'DESC']);
    }

    /**
     * @param string $baseUrl
     * @param mixed[] $arguments
     */
    public function initialize($baseUrl, $arguments = []): void
    {
        parent::initialize($baseUrl, $arguments);
        $this->setupColumns();
        $this->setupViews();
    }

    private function setupColumns(): void
    {
        $this->addColumns([new NumberColumn(
            $this->formatter,
            'status_code',
            __('HTTP Status Code', 'staatic')
        ), new TextColumn('url', __('URL', 'staatic'), [
            'decorators' => [new CallbackDecorator(function (string $input, Result $item) {
                        return (string) $this->shortUrl($item->url());
                    }), new LinkDecorator(function (Result $item) {
                        return $item->url()->getAuthority() ? (string) $item->url() : null;
                    }, \true), new TitleDecorator(function (Result $item) {
                        return $item->originalUrl() ? sprintf(
                            /* translators: %s: original value. */
                            __('Originally: %s', 'staatic'),
                            $item->originalUrl()
                        ) : null;
                    })]
        ]), new TextColumn('redirect_url', __('Redirect URL', 'staatic'), [
            'decorators' => [new LinkDecorator(function (Result $item) {
                        return (($nullsafeVariable1 = $item->redirectUrl()) ? $nullsafeVariable1->getAuthority() : null) ? (string) $item->redirectUrl() : null;
                    }, \true)]
        ]), new TextColumn(
            'mime_type',
            __('Mime Type', 'staatic')
        ), new BytesColumn($this->formatter, 'size', __('Size', 'staatic'), [
            'decorators' => [new LinkDecorator(function (Result $item) {
                        return admin_url("admin.php?staatic=result-download&resultId={$item->id()}");
                    })]
        ]), new TextColumn('original_found_on_url', __('Found On URL', 'staatic'), [
            'decorators' => [new CallbackDecorator(function (string $input, Result $item) {
                        return $item->originalFoundOnUrl() ? (string) $this->shortUrl(
                            $item->originalFoundOnUrl()
                        ) : null;
                    }), new LinkDecorator(function (Result $item) {
                        return $item->originalFoundOnUrl() ? (string) $item->originalFoundOnUrl() : null;
                    }, \true)]
        ]), new DateColumn($this->formatter, 'date_created', __('Found On Date', 'staatic'))]);
    }

    private function shortUrl(UriInterface $url): UriInterface
    {
        try {
            return $url->withScheme('')->withUserInfo('')->withHost('')->withPort(null);
        } catch (MalformedUriException $exception) {
            // That's fine; just return the original URL.
        }

        return $url;
    }

    public function setupViews(): void
    {
        $statusCategories = [
            1 => __('1xx Informational', 'staatic'),
            2 => __('2xx Success', 'staatic'),
            3 => __('3xx Redirection', 'staatic'),
            4 => __('4xx Client Errors', 'staatic'),
            5 => __('5xx Server Errors', 'staatic')
        ];
        foreach ($statusCategories as $name => $label) {
            $this->addView(new View((string) $name, $label));
        }
    }

    /**
     * @param string|null $view
     * @param string|null $query
     * @param int $limit
     * @param int $offset
     * @param string|null $orderBy
     * @param string|null $direction
     */
    public function items($view, $query, $limit, $offset, $orderBy, $direction): array
    {
        $buildId = $this->arguments['buildId'];
        $view = $view ? (int) $view : null;

        return $this->repository->findWhereMatching($buildId, $view, $query, $limit, $offset, $orderBy, $direction);
    }

    /**
     * @param string|null $view
     * @param string|null $query
     */
    public function numItems($view, $query): int
    {
        $buildId = $this->arguments['buildId'];
        $view = $view ? (int) $view : null;

        return $this->repository->countWhereMatching($buildId, $view, $query);
    }

    public function numItemsPerView(): ?array
    {
        return $this->repository->getResultsPerStatusCategory($this->arguments['buildId']);
    }
}
