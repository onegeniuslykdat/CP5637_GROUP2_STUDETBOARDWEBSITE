<?php

namespace Staatic\Vendor;

/**
 * @var Staatic\WordPress\Service\Formatter $_formatter
 * @var Staatic\WordPress\Publication\Publication $publication
 * @var Staatic\WordPress\Logging\LogEntry[]|array $logEntries
 * @var string $breakLogEntryId;
 * @var array $resultsPerStatusCategory
 */
use Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsPage;
use Staatic\WordPress\Module\Admin\Page\PublicationResults\PublicationResultsPage;

$numFailedResults = $resultsPerStatusCategory[4] + $resultsPerStatusCategory[5];
$build = $publication->build();
$deployment = $publication->deployment();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php 
echo \esc_html(\sprintf(
    /* translators: %s: Publication creation date. */
    \__('Publication "%s"', 'staatic'),
    $_formatter->date($publication->dateCreated())
));
?>
        <?php 
if ($publication->isPreview()) {
    ?>
            <?php 
    \esc_html_e('(Preview)', 'staatic');
    ?>
        <?php 
}
?>
    </h1>
    <hr class="wp-header-end">

    <?php 
$currentTab = 'summary';
$this->render('admin/publication/_header.php', \compact('publication', 'currentTab'));
?>

    <h2><?php 
\esc_html_e('Publication Summary', 'staatic');
?></h2>

    <table class="form-table staatic-dense" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><?php 
\esc_html_e('Published By', 'staatic');
?></th>
                <td><?php 
echo $publication->userId() ? \esc_html($publication->publisher()->data->display_name) : ('<em>' . \esc_html__(
    'system',
    'staatic'
) . '</em>');
?></td>
            </tr>
            <tr>
                <th scope="row"><?php 
\esc_html_e('Status', 'staatic');
?></th>
                <td><?php 
echo \esc_html($publication->status()->label());
?></td>
            </tr>
            <tr>
                <th scope="row"><?php 
\esc_html_e('Time Taken', 'staatic');
?></th>
                <td>
                    <?php 
echo \esc_html($_formatter->difference($publication->dateCreated(), $publication->dateFinished()));
?>
                    <?php 
if ($build->dateCrawlFinished()) {
    ?>
                        <small class="text-muted">
                            <?php 
    echo \esc_html(\sprintf(
        /* translators: 1: Date interval for crawling, 2: Date interval for deployment. */
        \__('(%1$s for crawling, %2$s for deployment)', 'staatic'),
        $_formatter->difference($build->dateCrawlStarted(), $build->dateCrawlFinished()),
        $_formatter->difference($deployment->dateStarted(), $deployment->dateFinished())
    ));
    ?>
                        </small>
                    <?php 
}
?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php 
\esc_html_e('Resources', 'staatic');
?></th>
                <td>
                    <a href="<?php 
echo \admin_url(\sprintf('admin.php?page=%s&id=%s', PublicationResultsPage::PAGE_SLUG, $publication->id()));
?>">
                        <?php 
echo \esc_html(\sprintf(
    /* translators: %s: Number of resources. */
    \__('%s resources', 'staatic'),
    $_formatter->number(\array_sum($resultsPerStatusCategory))
));
?></a>
                    <small class="text-muted">
                        <?php 
echo \wp_kses_post(\sprintf(
    /* translators: 1: Number of resources, 2: Redirect/redirects, 3: Number of failed resources, 4: Failure/failures. */
    \__('(%1$s %2$s, %3$s %4$s)', 'staatic'),
    $_formatter->number($resultsPerStatusCategory[3]),
    ($resultsPerStatusCategory[3] === 1) ? \__('redirect', 'staatic') : \__('redirects', 'staatic'),
    \sprintf(($numFailedResults > 0) ? '<b>%s</b>' : '%s', $_formatter->number($numFailedResults)),
    ($numFailedResults === 1) ? \__('failure', 'staatic') : \__('failures', 'staatic')
));
?>
                    </small>
                </td>
            </tr>
            <?php 
if ((string) $publication->build()->destinationUrl() !== '') {
    ?>
                <tr>
                    <th scope="row"><?php 
    \esc_html_e('Destination URL', 'staatic');
    ?></th>
                    <td><a href="<?php 
    echo \esc_attr($publication->build()->destinationUrl());
    ?>" target="_blank" rel="noopener"><?php 
    echo \esc_html($publication->build()->destinationUrl());
    ?></a></td>
                </tr>
            <?php 
}
?>
        </tbody>
    </table>

    <h2><?php 
\esc_html_e('Log Summary', 'staatic');
?></h2>

    <?php 
if (\count($logEntries) > 0) {
    ?>
        <ul class="staatic-log-summary">
            <?php 
    foreach ($logEntries as $logEntry) {
        ?>
                <?php 
        if ($logEntry->id() === $breakLogEntryId) {
            ?>
                    <li class="staatic-log-break">[…]</li>
                <?php 
        }
        ?>

                <?php 
        $source = ($logEntry->context() && isset($logEntry->context()['source'])) ? $logEntry->context()['source'] : null;
        ?>
                <?php 
        $failure = ($logEntry->context() && isset($logEntry->context()['failure'])) ? $logEntry->context()['failure'] : null;
        ?>

                <li class="staatic-log-level-<?php 
        echo \esc_attr($logEntry->level());
        ?>">
                    <?php 
        echo \esc_html($_formatter->date($logEntry->date()));
        ?> -
                    <?php 
        echo $source ? \esc_html($source) . ': ' : '';
        ?>

                    <span class="message"><?php 
        echo $_formatter->logMessage($logEntry->message());
        ?></span>

                    <?php 
        if ($failure) {
            ?>
                        <?php 
            $failureSummary = (\preg_match('~\w+:\s+(.+?) in .+?:\d+\n~s', $failure, $match) === 1) ? $match[1] : null;
            ?>
                        <?php 
            $showSensitiveData = \current_user_can('staatic_manage_settings');
            ?>

                        <?php 
            if ($showSensitiveData) {
                ?>
                            <?php 
                if ($failureSummary) {
                    ?>
                                <details class="staatic-publication-error">
                                    <summary><?php 
                    echo \esc_html($failureSummary);
                    ?></summary>
                                    <p><?php 
                    echo \nl2br(\esc_html($failure));
                    ?></p>
                                </details >
                            <?php 
                } else {
                    ?>
                                <div class="staatic-publication-error"><?php 
                    echo \nl2br(\esc_html($failure));
                    ?></div>
                            <?php 
                }
                ?>
                        <?php 
            } elseif ($failureSummary) {
                ?>
                            <div class="staatic-publication-error"><?php 
                echo \esc_html($failureSummary);
                ?></div>
                        <?php 
            }
            ?>
                    <?php 
        }
        ?>
                </li>
            <?php 
    }
    ?>
        </ul>
    <?php 
} else {
    ?>
        <p><?php 
    \esc_html_e('No logs for this publication (yet)', 'staatic');
    ?></p>
    <?php 
}
?>

    <p>
        <a href="<?php 
echo \admin_url(\sprintf('admin.php?page=%s&id=%s', PublicationLogsPage::PAGE_SLUG, $publication->id()));
?>">
            <?php 
\esc_html_e('View all logs', 'staatic');
?>
        </a>
    </p>

    <br class="clear">
</div>
<?php 
