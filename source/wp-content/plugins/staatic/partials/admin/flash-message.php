<?php

namespace Staatic\Vendor;

/**
 * @var \Staatic\WordPress\Service\Formatter $_formatter
 *
 * @var string $title
 * @var string $message
 * @var string $redirectUrl
 * @var int $redirectTimeout
 */
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php 
echo \esc_html($title);
?></h1>
    <hr class="wp-header-end">
    <p><?php 
echo \esc_html($message);
?></p>
</div>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        window.setTimeout(function() {
            window.location.href = '<?php 
echo \esc_url_raw($redirectUrl);
?>';
        }, <?php 
echo \intval($redirectTimeout);
?>);
    });
</script>
<?php 
