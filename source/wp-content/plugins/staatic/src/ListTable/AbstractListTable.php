<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable;

use RuntimeException;
use Staatic\WordPress\ListTable\BulkAction\BulkActionInterface;
use Staatic\WordPress\ListTable\Column\ColumnInterface;
use Staatic\WordPress\ListTable\RowAction\RowActionInterface;
use Staatic\WordPress\ListTable\View\ViewInterface;
use WP_List_Table;

abstract class AbstractListTable
{
    /**
     * @var string
     */
    private $primaryColumn;

    /**
     * @var mixed[]|null
     */
    private $defaultSortDefinition;

    /** @var int */
    protected const DEFAULT_ITEMS_PER_PAGE = 20;

    /** @var string */
    protected const NAME = 'list_table';

    /** @var ColumnInterface[] */
    private $columns = [];

    /** @var ViewInterface[] */
    private $views = [];

    /** @var RowActionInterface[] */
    private $rowActions = [];

    /** @var BulkActionInterface[] */
    private $bulkActions = [];

    // Runtime stuff
    /**
     * @var string|null
     */
    private $baseUrl;

    /**
     * @var mixed[]
     */
    protected $arguments = [];

    /**
     * @var string|null
     */
    private $wpScreenId;

    /**
     * @var WP_List_Table|null
     */
    private $wpListTable;

    public function __construct(string $primaryColumn, ?array $defaultSortDefinition = null)
    {
        $this->primaryColumn = $primaryColumn;
        $this->defaultSortDefinition = $defaultSortDefinition;
    }

    public function name(): string
    {
        return static::NAME;
    }

    public function baseUrl(): string
    {
        if ($this->baseUrl === null) {
            throw new RuntimeException('List table has not been initialized.');
        }

        return $this->baseUrl;
    }

    public function primaryColumn(): string
    {
        return $this->primaryColumn;
    }

    public function defaultSortDefinition(): ?array
    {
        return $this->defaultSortDefinition;
    }

    /**
     * @return ColumnInterface[]
     */
    public function columns(): array
    {
        return $this->columns;
    }

    /**
     * @param string $name
     */
    public function column($name): ColumnInterface
    {
        return $this->columns[$name];
    }

    /**
     * @param ColumnInterface $column
     */
    public function addColumn($column): void
    {
        $this->columns[$column->name()] = $column;
    }

    /**
     * @param ColumnInterface[] $columns
     */
    public function addColumns($columns): void
    {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }
    }

    /**
     * @return ViewInterface[]
     */
    public function views(): array
    {
        return $this->views;
    }

    /**
     * @param ViewInterface $view
     */
    public function addView($view): void
    {
        $this->views[$view->name()] = $view;
    }

    /**
     * @return RowActionInterface[]
     */
    public function rowActions(): array
    {
        return $this->rowActions;
    }

    /**
     * @param RowActionInterface $rowAction
     */
    public function addRowAction($rowAction): void
    {
        $this->rowActions[$rowAction->name()] = $rowAction;
    }

    /**
     * @param RowActionInterface[] $rowActions
     */
    public function addRowActions($rowActions): void
    {
        foreach ($rowActions as $rowAction) {
            $this->addRowAction($rowAction);
        }
    }

    /**
     * @return BulkActionInterface[]
     */
    public function bulkActions(): array
    {
        return $this->bulkActions;
    }

    /**
     * @param BulkActionInterface $bulkAction
     */
    public function addBulkAction($bulkAction): void
    {
        $this->bulkActions[$bulkAction->name()] = $bulkAction;
    }

    /**
     * @param BulkActionInterface[] $bulkActions
     */
    public function addBulkActions($bulkActions): void
    {
        foreach ($bulkActions as $bulkAction) {
            $this->addBulkAction($bulkAction);
        }
    }

    public function arguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param string|null $view
     * @param string|null $query
     * @param int $limit
     * @param int $offset
     * @param string|null $orderBy
     * @param string|null $direction
     */
    abstract public function items($view, $query, $limit, $offset, $orderBy, $direction): array;

    /**
     * @param string|null $view
     * @param string|null $query
     */
    abstract public function numItems($view, $query): int;

    public function numItemsPerView(): ?array
    {
        return null;
    }

    /**
     * @param string $wpScreenId
     */
    public function registerHooks($wpScreenId): void
    {
        // Initialize wp list table when enough is loaded, but not all...
        // add_action('admin_menu', [$this, 'initialize']);
        // Hidden columns
        add_filter('default_hidden_columns', [$this, 'defaultHiddenColumns'], 10, 2);
        // Add screen options
        add_action('load-' . $wpScreenId, [$this, 'addScreenOptions']);
        // Save screen options (WP < 5.4.2)
        add_filter('set-screen-option', [$this, 'saveScreenOptions'], 10, 3);
        // Save screen options (WP >= 5.4.2)
        // See: https://core.trac.wordpress.org/ticket/50392
        $optionName = sprintf('staatic_%s_per_page', static::NAME);
        add_filter(sprintf('set_screen_option_%s', $optionName), [$this, 'saveScreenOption'], 10, 3);
        $this->wpScreenId = $wpScreenId;
    }

    /**
     * @param mixed[] $hidden
     * @param object $wpScreen
     */
    public function defaultHiddenColumns($hidden, $wpScreen): array
    {
        if (isset($wpScreen->id) && $wpScreen->id === $this->wpScreenId) {
            foreach ($this->columns() as $column) {
                if ($column->isHiddenByDefault() && !in_array($column->name(), $hidden)) {
                    $hidden[] = $column->name();
                }
            }
        }

        return $hidden;
    }

    public static function addScreenOptions(): void
    {
        $optionName = sprintf('staatic_%s_per_page', static::NAME);
        add_screen_option('per_page', [
            'default' => static::DEFAULT_ITEMS_PER_PAGE,
            'option' => $optionName
        ]);
    }

    public static function saveScreenOptions($status, $option, $value)
    {
        $optionName = sprintf('staatic_%s_per_page', static::NAME);

        return ($option === $optionName) ? $value : $status;
    }

    public static function saveScreenOption($status, $option, $value)
    {
        return $value;
    }

    /**
     * @param string $baseUrl
     * @param mixed[] $arguments
     */
    public function initialize($baseUrl, $arguments = []): void
    {
        $this->wpListTable = new WpListTable($this);
        $this->baseUrl = $baseUrl;
        $this->arguments = $arguments;
    }

    public function processBulkActions(): void
    {
        $actionName = $this->wpListTable->current_action();
        if (!$actionName) {
            return;
        }
        check_admin_referer('bulk-items');
        if (!isset($this->bulkActions[$actionName])) {
            return;
        }
        $action = $this->bulkActions[$actionName];
        if (!isset($_REQUEST['item'])) {
            return;
        }
        $itemIds = (array) $_REQUEST['item'];
        if (empty($itemIds)) {
            return;
        }
        $action->callback()($itemIds);
    }

    /**
     * @param mixed[] $arguments
     */
    public function setArguments($arguments): void
    {
        $this->arguments = $arguments;
    }

    public function wpListTable(): WP_List_Table
    {
        return $this->wpListTable;
    }
}
