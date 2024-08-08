<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator;

use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Setting\SettingInterface;
use Staatic\WordPress\Module\ModuleCollection;
use Staatic\WordPress\Activator;
use Staatic\WordPress\Deactivator;
use wpdb;
use Staatic\WordPress\DependencyInjection\WpdbWrapper;
use Staatic\WordPress\Service\PartialRenderer;
use Staatic\WordPress\Factory\PartialRendererFactory;
use Staatic\Vendor\Psr\SimpleCache\CacheInterface;
use Staatic\WordPress\Cache\TransientCache;
use Staatic\Framework\Logger\ConsoleLogger;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\WordPress\Logging\DatabaseLogger;
use Staatic\WordPress\Logging\Logger;
use Staatic\WordPress\Factory\LoggerFactory;
use Staatic\Crawler\UrlTransformer\UrlTransformerInterface;
use Staatic\WordPress\Factory\UrlTransformerFactory;
use Staatic\Framework\BuildRepository\BuildRepositoryInterface;
use Staatic\WordPress\Bridge\BuildRepository;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\WordPress\Factory\ResourceRepositoryFactory;
use Staatic\Crawler\CrawlQueue\CrawlQueueInterface;
use Staatic\WordPress\Bridge\CrawlQueue;
use Staatic\Framework\DeploymentRepository\DeploymentRepositoryInterface;
use Staatic\WordPress\Bridge\DeploymentRepository;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
use Staatic\WordPress\Bridge\ResultRepository;
use Staatic\Crawler\UrlExtractor\Mapping\HtmlUrlExtractorMapping;
use Staatic\WordPress\Service\AdminNavigation;
use Staatic\WordPress\Factory\HttpClientFactory;
use Staatic\WordPress\Logging\LogEntryRepository;
use Staatic\WordPress\Publication\PublicationManager;
use Staatic\WordPress\Publication\PublicationRepository;
use Staatic\WordPress\Publication\PublicationTaskProvider;
use Staatic\WordPress\Service\Scheduler;
use Staatic\WordPress\Service\Settings;
use Staatic\WordPress\Publication\Task\SetupTask;
use Staatic\WordPress\Publication\Task\InitializeCrawlerTask;
use Staatic\WordPress\Publication\Task\CrawlTask;
use Staatic\WordPress\Publication\Task\FinishCrawlerTask;
use Staatic\WordPress\Publication\Task\PostProcessTask;
use Staatic\WordPress\Publication\Task\InitiateDeploymentTask;
use Staatic\WordPress\Publication\Task\DeployTask;
use Staatic\WordPress\Publication\Task\FinishDeploymentTask;
use Staatic\WordPress\Publication\Task\FinishTask;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services()->defaults()->autowire()->bind('$pluginVersion', '%staatic.version%')->bind(
        '$settingLocator',
        tagged_locator('app.setting')
    )->bind(
        '$publicationTasks',
        tagged_iterator('app.publicationTask')
    );
    $services->instanceof(ModuleInterface::class)->tag('app.module');
    $services->instanceof(SettingInterface::class)->tag('app.setting');
    $services->load('Staatic\WordPress\\', '../src/*');
    $services->set(ModuleCollection::class)->args([tagged_iterator('app.module')])->public();
    $services->set(Activator::class)->public();
    $services->set(Deactivator::class)->public();
    $services->set(wpdb::class, wpdb::class)->factory([service(WpdbWrapper::class), 'get']);
    $services->set(PartialRenderer::class)->factory(service(PartialRendererFactory::class));
    // Allow these components to be lazy loaded
    $services->set(CacheInterface::class, TransientCache::class)->lazy(CacheInterface::class);
    $services->set('console_logger', ConsoleLogger::class);
    $services->alias(LoggerInterface::class . ' $consoleLogger', 'console_logger');
    $services->set('database_logger', DatabaseLogger::class);
    $services->alias(LoggerInterface::class . ' $databaseLogger', 'database_logger');
    $services->set(LoggerInterface::class, Logger::class)->factory(service(LoggerFactory::class))->lazy(
        \Staatic\WordPress\Logging\LoggerInterface::class
    );
    $services->set(UrlTransformerInterface::class)->factory(service(UrlTransformerFactory::class))->lazy();
    $services->set(BuildRepositoryInterface::class, BuildRepository::class)->lazy(BuildRepositoryInterface::class);
    $services->set(ResourceRepositoryInterface::class)->factory(service(ResourceRepositoryFactory::class))->lazy();
    $services->set(CrawlQueueInterface::class, CrawlQueue::class)->lazy(CrawlQueueInterface::class);
    $services->set(DeploymentRepositoryInterface::class, DeploymentRepository::class)->lazy(
        DeploymentRepositoryInterface::class
    );
    $services->set(ResultRepositoryInterface::class, ResultRepository::class)->lazy(ResultRepositoryInterface::class);
    $services->set(HtmlUrlExtractorMapping::class, \Staatic\WordPress\Bridge\HtmlUrlExtractorMapping::class);
    $servicesMap = [
        'staatic.admin_navigation' => AdminNavigation::class,
        'staatic.build_repository' => BuildRepositoryInterface::class,
        'staatic.cache' => CacheInterface::class,
        'staatic.crawl_queue' => CrawlQueueInterface::class,
        'staatic.deployment_repository' => DeploymentRepositoryInterface::class,
        'staatic.http_client_factory' => HttpClientFactory::class,
        'staatic.log_entry_repository' => LogEntryRepository::class,
        'staatic.logger' => LoggerInterface::class,
        'staatic.publication_manager' => PublicationManager::class,
        'staatic.publication_repository' => PublicationRepository::class,
        'staatic.publication_task_provider' => PublicationTaskProvider::class,
        'staatic.resource_repository' => ResourceRepositoryInterface::class,
        'staatic.result_repository' => ResultRepositoryInterface::class,
        'staatic.scheduler' => Scheduler::class,
        'staatic.settings' => Settings::class,
        'staatic.url_transformer' => UrlTransformerInterface::class
    ];
    foreach ($servicesMap as $alias => $service) {
        $services->alias($alias, $service)->public();
    }
    foreach ([
        SetupTask::class => 100,
        InitializeCrawlerTask::class => 80,
        CrawlTask::class => 60,
        FinishCrawlerTask::class => 40,
        PostProcessTask::class => 20,
        InitiateDeploymentTask::class => 0,
        DeployTask::class => -40,
        FinishDeploymentTask::class => -80,
        FinishTask::class => -120
    ] as $task => $priority) {
        $services->set($task)->tag('app.publicationTask', [
            'priority' => $priority
        ]);
    }
};
