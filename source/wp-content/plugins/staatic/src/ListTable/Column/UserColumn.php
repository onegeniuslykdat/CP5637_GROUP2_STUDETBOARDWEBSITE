<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable\Column;

final class UserColumn extends AbstractColumn
{
    public function render($item): void
    {
        $userId = $this->itemValue($item);
        if (!$userId) {
            echo '<em>' . esc_html__('system', 'staatic') . '</em>';

            return;
        }
        $user = get_userdata($userId);
        if ($user) {
            $result = esc_html($user->data->display_name);
            echo $this->applyDecorators($result, $item);
        } else {
            echo esc_html__('unknown', 'staatic');
        }
    }
}
