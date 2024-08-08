<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable;

use Staatic\WordPress\ListTable\BulkAction\BulkActionInterface;
use Staatic\WordPress\ListTable\RowAction\RowActionInterface;
use WP_List_Table;

if (defined('ABSPATH') && !class_exists('WP_List_Table')) {
    require_once \ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
final class WpListTable extends WP_List_Table
{
    /**
     * @var \Staatic\WordPress\ListTable\AbstractListTable
     */
    private $listTable;

    public function __construct(\Staatic\WordPress\ListTable\AbstractListTable $listTable)
    {
        $this->listTable = $listTable;
        parent::__construct([
            'singular' => 'item',
            'plural' => 'items',
            'ajax' => \false
        ]);
    }

    public function get_columns()
    {
        $columnHeaders = [];
        if (count($this->listTable->bulkActions())) {
            $columnHeaders['cb'] = '<input type="checkbox">';
        }
        foreach ($this->listTable->columns() as $name => $column) {
            $columnHeaders[$name] = $column->label();
        }

        return $columnHeaders;
    }

    protected function get_sortable_columns()
    {
        $sortableColumns = array_filter($this->listTable->columns(), function ($column) {
            return $column->isSortable();
        });
        $sortDefinitions = [];
        foreach ($sortableColumns as $column) {
            $sortDefinitions[$column->name()] = $column->sortDefinition();
        }

        return $sortDefinitions;
    }

    protected function get_default_primary_column_name()
    {
        return $this->listTable->primaryColumn();
    }

    public function get_view(): ?string
    {
        return isset($_REQUEST['curview']) ? sanitize_key($_REQUEST['curview']) : null;
    }

    public function no_items()
    {
        /* translators: %s: Plural value for list item. */
        _e(sprintf('No %s found.', $this->_args['plural']), 'staatic');
    }

    public function prepare_items()
    {
        global $curview, $searchquery;
        $curview = $this->get_view();
        $searchquery = empty($_REQUEST['s']) ? null : sanitize_text_field($_REQUEST['s']);
        $itemsPerPage = $this->get_items_per_page(sprintf('staatic_%s_per_page', $this->listTable->name()));
        $pageNumber = $this->get_pagenum();
        [$orderBy, $direction] = $this->getOrder();
        $this->items = $this->listTable->items(
            $curview,
            $searchquery,
            $itemsPerPage,
            ($pageNumber - 1) * $itemsPerPage,
            $orderBy,
            $direction
        );
        $itemsTotal = $this->listTable->numItems($curview, $searchquery);
        $this->set_pagination_args([
            'total_items' => $itemsTotal,
            'per_page' => $itemsPerPage
        ]);
    }

    protected function getOrder(): array
    {
        $orderBy = null;
        $direction = null;
        $sortableColumns = $this->get_sortable_columns();
        if (isset($_REQUEST['orderby'])) {
            $inputOrderBy = sanitize_key($_REQUEST['orderby']);
            if (array_key_exists($inputOrderBy, $sortableColumns)) {
                [$orderBy, $direction] = $sortableColumns[$inputOrderBy];
            }
        }
        if (isset($_REQUEST['order'])) {
            $inputOrder = strtoupper(sanitize_key($_REQUEST['order']));
            if (in_array($inputOrder, ['ASC', 'DESC'])) {
                $direction = $inputOrder;
            }
        }
        if ($orderBy === null) {
            $defaultSortDefinition = $this->listTable->defaultSortDefinition();
            if (is_array($defaultSortDefinition)) {
                $orderBy = $defaultSortDefinition[0];
                $direction = ($direction === null) ? $defaultSortDefinition[1] : $direction;
            }
        }

        return [$orderBy, $direction];
    }

    /*protected function extra_tablenav($which)
      {
          ?>
          <p class="export-box" style="float: left;">
              <a class="button" href="<?php echo esc_url($this->listTable->); ?>">
                  <?php esc_html_e('Export to JSON', 'staatic'); ?>
              </a>
          </p>
          <?php
      }*/
    public function column_cb($item)
    {
        $itemId = \Staatic\WordPress\ListTable\ValueAccessor::getValueByKey($item, 'id');
        ?>
        <label class="screen-reader-text" for="cb-select-<?php 
        echo esc_attr($itemId);
        ?>">
            <?php 
        /* translators: %s: List table item identifier. */
        printf(__('Select %s', 'staatic'), esc_html($itemId));
        ?>
        </label>
        <input id="cb-select-<?php 
        echo esc_attr($itemId);
        ?>" type="checkbox" name="item[]" value="<?php 
        echo esc_attr($itemId);
        ?>">
        <?php 
    }

    protected function column_default($item, $columnName)
    {
        $column = $this->listTable->column($columnName);
        $column->render($item);
    }

    protected function get_views()
    {
        global $curview;
        $views = $this->listTable->views();
        if (empty($views)) {
            return;
        }
        $numItemsPerView = $this->listTable->numItemsPerView();
        $baseUrl = $this->listTable->baseUrl();
        if ($numItemsPerView === null) {
            $allLabel = __('All', 'staatic');
        } else {
            $numItemsTotal = array_sum($numItemsPerView);
            $allLabel = sprintf(
                /* translators: %s: Number of items. */
                _nx('All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $numItemsTotal, 'items', 'staatic'),
                number_format_i18n($numItemsTotal)
            );
        }
        $result = [];
        $result['all'] = sprintf(
            '<a href="%s"%s>%s</a>',
            esc_url($baseUrl),
            empty($curview) ? ' class="current" aria-current="page"' : '',
            $allLabel
        );
        foreach ($views as $name => $view) {
            if ($numItemsPerView === null) {
                $viewLabel = $view->label();
            } else {
                $numItems = $numItemsPerView[$name] ?? 0;
                $viewLabel = sprintf(
                    /* translators: 1: View label, 2: Number of items. */
                    _nx('%1$s <span class="count">(%2$s)</span>', '%1$s <span class="count">(%2$s)</span>', $numItems, 'items', 'staatic'),
                    $view->label(),
                    number_format_i18n($numItems)
                );
            }
            $result['staatic-' . $this->listTable->name() . '-view-' . $name] = sprintf(
                '<a href="%s"%s>%s</a>',
                esc_url(add_query_arg('curview', $name, $baseUrl)),
                ($curview == $name) ? ' class="current" aria-current="page"' : '',
                $viewLabel
            );
        }

        return $result;
    }

    protected function get_bulk_actions()
    {
        return array_map(function (BulkActionInterface $bulkAction) {
            return $bulkAction->label();
        }, $this->listTable->bulkActions());
    }

    protected function handle_row_actions($item, $column_name, $primary)
    {
        if ($primary !== $column_name) {
            return '';
        }
        $actions = $this->listTable->rowActions();
        $actions = array_filter($actions, function (RowActionInterface $rowAction) use ($item) {
            $callback = $rowAction->visibleCallback();

            return !$callback || $callback($item);
        });
        $actions = array_map(function (RowActionInterface $rowAction) use ($item) {
            return $rowAction->render($item);
        }, $actions);

        return $this->row_actions($actions);
    }
}
