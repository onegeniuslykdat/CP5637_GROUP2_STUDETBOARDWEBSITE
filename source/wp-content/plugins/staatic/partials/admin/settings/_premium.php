<?php

namespace Staatic\Vendor;

/**
 * @var \Staatic\WordPress\Service\Formatter $_formatter
 */
?>

<div class="staatic-settings-premium">
    <h2 class="wp-heading-inline">
        <span class="dashicons dashicons-superhero"></span>
        <?php 
\esc_html_e('Advance Your WordPress Site', 'staatic');
?>
    </h2>

    <p><?php 
\_e('Thank you for choosing Staatic to enhance the speed and security of your WordPress site. To further improve your site’s performance and support the continuous development of Staatic, consider upgrading to Staatic for WordPress Premium.', 'staatic');
?></p>

    <h2 class="wp-heading-inline">
        <span class="dashicons dashicons-heart"></span>
        <?php 
\esc_html_e('Support Development with Premium', 'staatic');
?>
    </h2>

    <p><?php 
\_e('By choosing Premium, you not only gain access to exclusive features but also contribute to the sustainability and advancement of Staatic. This support allows us to continually refine and expand the capabilities of Staatic.', 'staatic');
?></p>

    <h2 class="wp-heading-inline">
        <span class="dashicons dashicons-flag"></span>
        <?php 
\esc_html_e('Premium Features', 'staatic');
?>
    </h2>

    <p><?php 
\_e('Staatic for WordPress Premium introduces a suite of advanced features designed to optimize your site management and user experience:', 'staatic');
?></p>

    <ul>
        <li><span class="dashicons dashicons-yes"></span> <?php 
\_e('<strong>Quick Publications</strong>: Efficiently publish changes by updating only the modified content.', 'staatic');
?></li>
        <li><span class="dashicons dashicons-yes"></span> <?php 
\_e('<strong>Selective Publications</strong>: Choose specific content to update, merging it seamlessly with your site’s latest publication for targeted updates.', 'staatic');
?></li>
        <li><span class="dashicons dashicons-yes"></span> <?php 
\_e('<strong>Automated Publications</strong>: Automate your site updates based on content revisions or schedule, keeping your site up-to-date effortlessly.', 'staatic');
?></li>
        <li><span class="dashicons dashicons-yes"></span> <?php 
\_e('<strong>Form Submission Handling</strong>: Maintain interactive elements like forms on your static site, ensuring functionality without compromising on speed.', 'staatic');
?></li>
        <li><span class="dashicons dashicons-yes"></span> <?php 
\_e('<strong>Search Integration</strong>: Improve your site’s search functionality, allowing visitors to find information faster and more accurately.', 'staatic');
?></li>
    </ul>

    <h2 class="wp-heading-inline">
        <?php 
\esc_html_e('Learn More', 'staatic');
?>
    </h2>

    <p><?php 
echo \sprintf(
    /* translators: 1: Link to Staatic Premium. */
    \__('Interested in enhancing your site with Staatic for WordPress Premium? <a href="%1$s" target="_blank" rel="noopener">Explore the benefits and features of upgrading to Premium</a> and see how this can further elevate your website’s efficiency and power.', 'staatic'),
    'https://staatic.com/wordpress/activation/'
);
?></p>
</div>
<?php 
