<?php

namespace Staatic\WordPress\DependencyInjection;

use Closure;
use Staatic\WordPress\Activator;
use Staatic\WordPress\Deactivator;
use Staatic\WordPress\Service\Scheduler;
use Staatic\WordPress\Module\ModuleCollection;
use Staatic\WordPress\Module\LoadTextDomain;
use Staatic\WordPress\Module\RegisterOptions;
use Staatic\WordPress\Module\RegisterSchedules;
use Staatic\WordPress\Module\Admin\RegisterAssets;
use Staatic\WordPress\Module\Admin\RegisterPluginActionLinks;
use Staatic\WordPress\Module\Compatibility;
use Staatic\WordPress\Module\Deployer\SftpDeployer\SftpStatusEndpoint;
use Staatic\WordPress\Module\Integration\AvadaTheme;
use Staatic\WordPress\Module\Integration\FlyingPressPlugin;
use Staatic\WordPress\Module\Integration\RankMathPlugin;
use Staatic\WordPress\Module\Integration\RedirectionPlugin;
use Staatic\WordPress\Module\Integration\SafeRedirectManagerPlugin;
use Staatic\WordPress\Module\Integration\Simple301RedirectsPlugin;
use Staatic\WordPress\Module\Integration\Wordpress;
use Staatic\WordPress\Module\Integration\WpFastestCachePlugin;
use Staatic\WordPress\Module\Integration\YoastPremiumPlugin;
use Staatic\WordPress\Service\AdminNavigation;
use Staatic\WordPress\Bridge\BuildRepository;
use Staatic\WordPress\Cache\TransientCache;
use Staatic\WordPress\Bridge\CrawlQueue;
use Staatic\WordPress\Bridge\DeploymentRepository;
use Staatic\WordPress\Factory\HttpClientFactory;
use Staatic\WordPress\Setting\Advanced\HttpConcurrencySetting;
use Staatic\WordPress\Setting\Advanced\HttpTimeoutSetting;
use Staatic\WordPress\Setting\Advanced\HttpDelaySetting;
use Staatic\WordPress\Setting\Advanced\SslVerifyBehaviorSetting;
use Staatic\WordPress\Setting\Advanced\SslVerifyPathSetting;
use Staatic\WordPress\Setting\Advanced\HttpAuthenticationUsernameSetting;
use Staatic\WordPress\Setting\Advanced\HttpAuthenticationPasswordSetting;
use Staatic\WordPress\Setting\Advanced\HttpToHttpsSetting;
use Staatic\WordPress\Service\SiteUrlProvider;
use Staatic\WordPress\Logging\LogEntryRepository;
use Staatic\WordPress\Factory\LoggerFactory;
use Staatic\WordPress\Setting\Advanced\LoggingLevelSetting;
use Staatic\WordPress\Logging\DatabaseLogger;
use Staatic\Framework\Logger\ConsoleLogger;
use Staatic\WordPress\Publication\PublicationManager;
use Staatic\WordPress\Publication\BackgroundPublisher;
use Staatic\WordPress\Factory\DeploymentFactory;
use Staatic\WordPress\Setting\Build\DestinationUrlSetting;
use Staatic\WordPress\Setting\Build\PreviewUrlSetting;
use Staatic\WordPress\Publication\PublicationRepository;
use Staatic\WordPress\Setting\Advanced\WorkDirectorySetting;
use Staatic\WordPress\Publication\PublicationTaskProvider;
use Staatic\WordPress\Factory\ResourceRepositoryFactory;
use Staatic\WordPress\Bridge\ResultRepository;
use Staatic\WordPress\Service\Settings;
use Staatic\WordPress\Factory\EncrypterFactory;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\ServiceLocator;
use Staatic\WordPress\Factory\StaticDeployerFactory;
use Staatic\WordPress\Factory\StaticGeneratorFactory;
use Staatic\WordPress\Factory\CrawlProfileFactory;
use Staatic\WordPress\Factory\KnownUrlsContainerFactory;
use Staatic\WordPress\Bridge\HtmlUrlExtractorMapping;
use Staatic\WordPress\Factory\UrlTransformerFactory;
use Staatic\WordPress\Migrations\MigrationCoordinatorFactory;
use Staatic\WordPress\Module\Admin\ExtendSiteHealth;
use Staatic\WordPress\Service\Formatter;
use Staatic\WordPress\Module\Admin\Page\BuildResultPage;
use Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsExportPage;
use Staatic\WordPress\Service\PublicationLogsExporter;
use Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsPage;
use Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsTable;
use Staatic\WordPress\Module\Admin\Page\PublicationResults\PublicationResultsPage;
use Staatic\WordPress\Module\Admin\Page\PublicationResults\PublicationResultsTable;
use Staatic\WordPress\Module\Admin\Page\Publications\PublicationDeletePage;
use Staatic\WordPress\Module\Admin\Page\Publications\PublicationDownloadPage;
use Staatic\WordPress\Service\PublicationArchiver;
use Staatic\WordPress\Module\Admin\Page\Publications\PublicationSummaryPage;
use Staatic\WordPress\Module\Admin\Page\Publications\PublicationsPage;
use Staatic\WordPress\Module\Admin\Page\Publications\PublicationsTable;
use Staatic\WordPress\ListTable\Column\ColumnFactory;
use Staatic\WordPress\Module\Admin\Page\PublishPage;
use Staatic\WordPress\Module\Admin\Page\PublishSubsetPage;
use Staatic\WordPress\Module\Admin\Page\SettingsPage;
use Staatic\WordPress\Module\Admin\Page\TestRequestPage;
use Staatic\WordPress\Module\Admin\RegisterAdminBar;
use Staatic\WordPress\Module\Admin\RegisterNavigation;
use Staatic\WordPress\Module\Admin\Widget\PublicationLogsWidget;
use Staatic\WordPress\Module\Admin\Widget\PublicationStatusWidget;
use Staatic\WordPress\Module\Cleanup;
use Staatic\WordPress\Logging\LogEntryCleanup;
use Staatic\WordPress\Publication\PublicationCleanup;
use Staatic\WordPress\Service\ResourceCleanup;
use Staatic\WordPress\Module\Cli\RegisterCommands;
use Staatic\WordPress\Cli\MigrateCommand;
use Staatic\WordPress\Cli\PublishCommand;
use Staatic\WordPress\Cli\RedeployCommand;
use Staatic\WordPress\Module\Deployer\FilesystemDeployer\ApacheConfigurationFileSetting;
use Staatic\WordPress\Module\Deployer\FilesystemDeployer\ConfigurationFilesSetting;
use Staatic\WordPress\Module\Deployer\FilesystemDeployer\FilesystemDeployerModule;
use Staatic\WordPress\Module\Deployer\FilesystemDeployer\FilesystemDeployStrategyFactory;
use Staatic\WordPress\Module\Deployer\FilesystemDeployer\NginxConfigurationFileSetting;
use Staatic\WordPress\Module\Deployer\FilesystemDeployer\RetainPathsSetting;
use Staatic\WordPress\Module\Deployer\FilesystemDeployer\TargetDirectorySetting;
use Staatic\WordPress\Module\Deployer\FilesystemDeployer\SymlinkUploadsDirectorySetting;
use Staatic\WordPress\Module\Deployer\GithubDeployer\AuthSetting;
use Staatic\WordPress\Module\Deployer\GithubDeployer\TokenSetting;
use Staatic\WordPress\Module\Deployer\GithubDeployer\BranchSetting;
use Staatic\WordPress\Module\Deployer\GithubDeployer\CommitMessageSetting;
use Staatic\WordPress\Module\Deployer\GithubDeployer\GitSetting;
use Staatic\WordPress\Module\Deployer\GithubDeployer\GithubDeployerModule;
use Staatic\WordPress\Module\Deployer\GithubDeployer\GithubDeployStrategyFactory;
use Staatic\WordPress\Module\Deployer\GithubDeployer\GithubStatusEndpoint;
use Staatic\WordPress\Module\Deployer\GithubDeployer\PrefixSetting;
use Staatic\WordPress\Module\Deployer\GithubDeployer\RepositorySetting;
use Staatic\WordPress\Module\Deployer\NetlifyDeployer\AccessTokenSetting;
use Staatic\WordPress\Module\Deployer\NetlifyDeployer\SiteIdSetting;
use Staatic\WordPress\Module\Deployer\NetlifyDeployer\NetlifyDeployerModule;
use Staatic\WordPress\Module\Deployer\NetlifyDeployer\NetlifyDeployStrategyFactory;
use Staatic\WordPress\Module\Deployer\NetlifyDeployer\NetlifyStatusEndpoint;
use Staatic\WordPress\Module\Deployer\S3Deployer\AuthAccessKeyIdSetting;
use Staatic\WordPress\Module\Deployer\S3Deployer\AuthProfileSetting;
use Staatic\WordPress\Module\Deployer\S3Deployer\AuthSecretAccessKeySetting;
use Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontDistributionIdSetting;
use Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontInvalidateEverythingPath;
use Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontMaxInvalidationPathsSetting;
use Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontSetting;
use Staatic\WordPress\Module\Deployer\S3Deployer\EndpointSetting;
use Staatic\WordPress\Module\Deployer\S3Deployer\S3PrefixSetting;
use Staatic\WordPress\Module\Deployer\S3Deployer\S3BucketSetting;
use Staatic\WordPress\Module\Deployer\S3Deployer\S3DeployerModule;
use Staatic\WordPress\Module\Deployer\S3Deployer\AwsDeployStrategyFactory;
use Staatic\WordPress\Module\Deployer\S3Deployer\S3ObjectAcl;
use Staatic\WordPress\Module\Deployer\S3Deployer\S3RegionSetting;
use Staatic\WordPress\Module\Deployer\S3Deployer\S3Setting;
use Staatic\WordPress\Module\Deployer\SftpDeployer\HostSetting;
use Staatic\WordPress\Module\Deployer\SftpDeployer\PortSetting;
use Staatic\WordPress\Module\Deployer\SftpDeployer\UsernameSetting;
use Staatic\WordPress\Module\Deployer\SftpDeployer\PasswordSetting;
use Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeySetting;
use Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeyPasswordSetting;
use Staatic\WordPress\Module\Deployer\SftpDeployer\NetworkSetting;
use Staatic\WordPress\Module\Deployer\SftpDeployer\SftpDeployerModule;
use Staatic\WordPress\Module\Deployer\SftpDeployer\SftpDeployStrategyFactory;
use Staatic\WordPress\Module\Deployer\SftpDeployer\SftpSetting;
use Staatic\WordPress\Module\Deployer\SftpDeployer\TimeoutSetting;
use Staatic\WordPress\Module\Deployer\ZipfileDeployer\ZipfileDeployerModule;
use Staatic\WordPress\Module\Deployer\ZipfileDeployer\ZipfileSetting;
use Staatic\WordPress\Module\EnsureMigrated;
use Staatic\WordPress\Module\HttpAuthHeaders;
use Staatic\WordPress\Module\HttpsToHttpDowngrade;
use Staatic\WordPress\Module\Integration\ElementorPlugin;
use Staatic\WordPress\Module\RegisterFieldTypes;
use Staatic\WordPress\Module\RegisterPublishHook;
use Staatic\WordPress\Module\RegisterSettings;
use Staatic\WordPress\Module\Rest\PublicationLogsEndpoint;
use Staatic\WordPress\Module\Rest\PublicationStatusEndpoint;
use Staatic\WordPress\Module\Rest\SiteHealthTestsEndpoint;
use Staatic\WordPress\Module\ScheduleTestRequest;
use Staatic\WordPress\Publication\Task\CrawlTask;
use Staatic\WordPress\Publication\Task\DeployTask;
use Staatic\WordPress\Publication\Task\FinishCrawlerTask;
use Staatic\WordPress\Publication\Task\FinishDeploymentTask;
use Staatic\WordPress\Publication\Task\FinishTask;
use Staatic\WordPress\Publication\Task\InitializeCrawlerTask;
use Staatic\WordPress\Publication\Task\InitiateDeploymentTask;
use Staatic\WordPress\Publication\Task\PostProcessTask;
use Staatic\WordPress\Publication\Task\SetupTask;
use Staatic\WordPress\Service\HealthChecks;
use Staatic\WordPress\Factory\PartialRendererFactory;
use Staatic\WordPress\Setting\Advanced\BackgroundProcessTimeout;
use Staatic\WordPress\Setting\Advanced\CrawlerDomParserSetting;
use Staatic\WordPress\Setting\Advanced\CrawlerLowercaseUrlsSetting;
use Staatic\WordPress\Setting\Advanced\CrawlerProcessNotFoundSetting;
use Staatic\WordPress\Setting\Advanced\CrawlerSetting;
use Staatic\WordPress\Setting\Advanced\HttpAuthenticationSetting;
use Staatic\WordPress\Setting\Advanced\HttpNetworkSetting;
use Staatic\WordPress\Setting\Advanced\OverrideSiteUrlSetting;
use Staatic\WordPress\Setting\Advanced\PageNotFoundPathSetting;
use Staatic\WordPress\Setting\Advanced\SslVerifySetting;
use Staatic\WordPress\Setting\Advanced\UninstallDataSetting;
use Staatic\WordPress\Setting\Advanced\UninstallSetting;
use Staatic\WordPress\Setting\Advanced\UninstallSettingsSetting;
use Staatic\WordPress\Setting\Build\AdditionalPathsSetting;
use Staatic\WordPress\Setting\Build\AdditionalRedirectsSetting;
use Staatic\WordPress\Setting\Build\AdditionalUrlsSetting;
use Staatic\WordPress\Setting\Build\ExcludeUrlsSetting;
use Staatic\WordPress\Setting\Deployment\DeploymentMethodSetting;
use Staatic\Framework\BuildRepository\BuildRepositoryInterface;
use Staatic\Vendor\Symfony\Component\VarExporter\LazyObjectInterface;
use Staatic\Vendor\Symfony\Component\VarExporter\LazyProxyTrait;
use BadMethodCallException;
use Staatic\Framework\Build;
use Staatic\Vendor\Symfony\Component\VarExporter\Internal\Hydrator;
use Staatic\Vendor\Symfony\Component\VarExporter\Internal\LazyObjectRegistry;
use Staatic\Vendor\Symfony\Component\VarExporter\Internal\LazyObjectState;
use Staatic\Vendor\Psr\SimpleCache\CacheInterface;
use Staatic\Crawler\CrawlQueue\CrawlQueueInterface;
use Staatic\Crawler\CrawlUrl;
use ReturnTypeWillChange;
use Staatic\Framework\DeploymentRepository\DeploymentRepositoryInterface;
use Staatic\Framework\Deployment;
use Staatic\WordPress\Logging\LoggerInterface;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\Framework\Resource;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
use Staatic\Framework\Result;
use Generator;
use Staatic\Crawler\UrlTransformer\UrlTransformerInterface;
use Staatic\Crawler\UrlTransformer\UrlTransformation;
use Staatic\WordPress\Logging\Logger;
use Staatic\WordPress\Service\PartialRenderer;
use wpdb;
use UnitEnum;
use Stringable;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Container;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\LogicException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class CachedContainer extends Container
{
    protected $parameters = [];

    /**
     * @var Closure
     */
    protected $getService;

    public function __construct()
    {
        $this->getService = Closure::fromCallable([$this, 'getService']);
        $this->parameters = $this->getDefaultParameters();
        $this->services = $this->privates = [];
        $this->methodMap = [
            'Staatic\WordPress\Activator' => 'getActivatorService',
            'Staatic\WordPress\Deactivator' => 'getDeactivatorService',
            'Staatic\WordPress\Module\ModuleCollection' => 'getModuleCollectionService',
            'staatic.admin_navigation' => 'getStaatic_AdminNavigationService',
            'staatic.build_repository' => 'getStaatic_BuildRepositoryService',
            'staatic.cache' => 'getStaatic_CacheService',
            'staatic.crawl_queue' => 'getStaatic_CrawlQueueService',
            'staatic.deployment_repository' => 'getStaatic_DeploymentRepositoryService',
            'staatic.http_client_factory' => 'getStaatic_HttpClientFactoryService',
            'staatic.log_entry_repository' => 'getStaatic_LogEntryRepositoryService',
            'staatic.logger' => 'getStaatic_LoggerService',
            'staatic.publication_manager' => 'getStaatic_PublicationManagerService',
            'staatic.publication_repository' => 'getStaatic_PublicationRepositoryService',
            'staatic.publication_task_provider' => 'getStaatic_PublicationTaskProviderService',
            'staatic.resource_repository' => 'getStaatic_ResourceRepositoryService',
            'staatic.result_repository' => 'getStaatic_ResultRepositoryService',
            'staatic.scheduler' => 'getStaatic_SchedulerService',
            'staatic.settings' => 'getStaatic_SettingsService',
            'staatic.url_transformer' => 'getStaatic_UrlTransformerService'
        ];
        $this->aliases = [];
    }

    public function compile(): void
    {
        throw new LogicException('You cannot compile a dumped container that was already compiled.');
    }

    public function isCompiled(): bool
    {
        return \true;
    }

    public function getRemovedIds(): array
    {
        return [
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\ExtendSiteHealth' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\Page\BuildResultPage' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsExportPage' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsPage' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\Page\PublicationResults\PublicationResultsPage' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\Page\Publications\PublicationDeletePage' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\Page\Publications\PublicationDownloadPage' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\Page\Publications\PublicationSummaryPage' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\Page\Publications\PublicationsPage' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\Page\PublishPage' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\Page\PublishSubsetPage' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\Page\SettingsPage' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\Page\TestRequestPage' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\RegisterAdminBar' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\RegisterAssets' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\RegisterNavigation' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\RegisterPluginActionLinks' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\Widget\PublicationLogsWidget' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Admin\Widget\PublicationStatusWidget' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Cleanup' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Cli\RegisterCommands' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Compatibility' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\FilesystemDeployer\ApacheConfigurationFileSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\FilesystemDeployer\ConfigurationFilesSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\FilesystemDeployer\FilesystemDeployerModule' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\FilesystemDeployer\NginxConfigurationFileSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\FilesystemDeployer\RetainPathsSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\FilesystemDeployer\SymlinkUploadsDirectorySetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\FilesystemDeployer\TargetDirectorySetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\GithubDeployer\AuthSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\GithubDeployer\BranchSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\GithubDeployer\CommitMessageSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\GithubDeployer\GitSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\GithubDeployer\GithubDeployerModule' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\GithubDeployer\GithubStatusEndpoint' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\GithubDeployer\PrefixSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\GithubDeployer\RepositorySetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\GithubDeployer\RetainPathsSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\GithubDeployer\TokenSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\NetlifyDeployer\AccessTokenSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\NetlifyDeployer\AuthSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\NetlifyDeployer\NetlifyDeployerModule' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\NetlifyDeployer\NetlifyStatusEndpoint' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\NetlifyDeployer\SiteIdSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\S3Deployer\AuthAccessKeyIdSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\S3Deployer\AuthProfileSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\S3Deployer\AuthSecretAccessKeySetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\S3Deployer\AuthSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontDistributionIdSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontInvalidateEverythingPath' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontMaxInvalidationPathsSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\S3Deployer\EndpointSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\S3Deployer\RetainPathsSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\S3Deployer\S3BucketSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\S3Deployer\S3DeployerModule' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\S3Deployer\S3ObjectAcl' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\S3Deployer\S3PrefixSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\S3Deployer\S3RegionSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\S3Deployer\S3Setting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\SftpDeployer\AuthSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\SftpDeployer\HostSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\SftpDeployer\NetworkSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\SftpDeployer\PasswordSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\SftpDeployer\PortSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\SftpDeployer\SftpDeployerModule' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\SftpDeployer\SftpSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\SftpDeployer\SftpStatusEndpoint' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeyPasswordSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeySetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\SftpDeployer\TargetDirectorySetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\SftpDeployer\TimeoutSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\SftpDeployer\UsernameSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\ZipfileDeployer\ZipfileDeployerModule' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Deployer\ZipfileDeployer\ZipfileSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\EnsureMigrated' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\HttpAuthHeaders' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\HttpsToHttpDowngrade' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Integration\AvadaTheme' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Integration\ElementorPlugin' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Integration\FlyingPressPlugin' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Integration\RankMathPlugin' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Integration\RedirectionPlugin' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Integration\SafeRedirectManagerPlugin' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Integration\Simple301RedirectsPlugin' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Integration\Wordpress' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Integration\WpFastestCachePlugin' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Integration\YoastPremiumPlugin' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\LoadTextDomain' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\RegisterFieldTypes' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\RegisterOptions' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\RegisterPublishHook' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\RegisterSchedules' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\RegisterSettings' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Rest\PublicationLogsEndpoint' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Rest\PublicationStatusEndpoint' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\Rest\SiteHealthTestsEndpoint' => \true,
            '.abstract.instanceof.Staatic\WordPress\Module\ScheduleTestRequest' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\BackgroundProcessTimeout' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\CrawlerDomParserSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\CrawlerLowercaseUrlsSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\CrawlerProcessNotFoundSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\CrawlerSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\HttpAuthenticationPasswordSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\HttpAuthenticationSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\HttpAuthenticationUsernameSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\HttpConcurrencySetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\HttpDelaySetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\HttpNetworkSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\HttpTimeoutSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\HttpToHttpsSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\LoggingLevelSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\OverrideSiteUrlSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\PageNotFoundPathSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\SslVerifyBehaviorSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\SslVerifyPathSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\SslVerifySetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\UninstallDataSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\UninstallSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\UninstallSettingsSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Advanced\WorkDirectorySetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Build\AdditionalPathsSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Build\AdditionalRedirectsSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Build\AdditionalUrlsSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Build\DestinationUrlSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Build\ExcludeUrlsSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Build\PreviewUrlSetting' => \true,
            '.abstract.instanceof.Staatic\WordPress\Setting\Deployment\DeploymentMethodSetting' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\ExtendSiteHealth' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\Page\BuildResultPage' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsExportPage' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsPage' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\Page\PublicationResults\PublicationResultsPage' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\Page\Publications\PublicationDeletePage' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\Page\Publications\PublicationDownloadPage' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\Page\Publications\PublicationSummaryPage' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\Page\Publications\PublicationsPage' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\Page\PublishPage' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\Page\PublishSubsetPage' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\Page\SettingsPage' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\Page\TestRequestPage' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\RegisterAdminBar' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\RegisterAssets' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\RegisterNavigation' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\RegisterPluginActionLinks' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\Widget\PublicationLogsWidget' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Admin\Widget\PublicationStatusWidget' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Cleanup' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Cli\RegisterCommands' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Compatibility' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Deployer\FilesystemDeployer\FilesystemDeployerModule' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Deployer\GithubDeployer\GithubDeployerModule' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Deployer\GithubDeployer\GithubStatusEndpoint' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Deployer\NetlifyDeployer\NetlifyDeployerModule' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Deployer\NetlifyDeployer\NetlifyStatusEndpoint' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Deployer\S3Deployer\S3DeployerModule' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Deployer\SftpDeployer\SftpDeployerModule' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Deployer\SftpDeployer\SftpStatusEndpoint' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Deployer\ZipfileDeployer\ZipfileDeployerModule' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\EnsureMigrated' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\HttpAuthHeaders' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\HttpsToHttpDowngrade' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Integration\AvadaTheme' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Integration\ElementorPlugin' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Integration\FlyingPressPlugin' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Integration\RankMathPlugin' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Integration\RedirectionPlugin' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Integration\SafeRedirectManagerPlugin' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Integration\Simple301RedirectsPlugin' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Integration\Wordpress' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Integration\WpFastestCachePlugin' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Integration\YoastPremiumPlugin' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\LoadTextDomain' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\RegisterFieldTypes' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\RegisterOptions' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\RegisterPublishHook' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\RegisterSchedules' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\RegisterSettings' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Rest\PublicationLogsEndpoint' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Rest\PublicationStatusEndpoint' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\Rest\SiteHealthTestsEndpoint' => \true,
            '.instanceof.Staatic\WordPress\Module\ModuleInterface.0.Staatic\WordPress\Module\ScheduleTestRequest' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\FilesystemDeployer\ApacheConfigurationFileSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\FilesystemDeployer\ConfigurationFilesSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\FilesystemDeployer\NginxConfigurationFileSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\FilesystemDeployer\RetainPathsSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\FilesystemDeployer\SymlinkUploadsDirectorySetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\FilesystemDeployer\TargetDirectorySetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\GithubDeployer\AuthSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\GithubDeployer\BranchSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\GithubDeployer\CommitMessageSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\GithubDeployer\GitSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\GithubDeployer\PrefixSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\GithubDeployer\RepositorySetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\GithubDeployer\RetainPathsSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\GithubDeployer\TokenSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\NetlifyDeployer\AccessTokenSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\NetlifyDeployer\AuthSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\NetlifyDeployer\SiteIdSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\S3Deployer\AuthAccessKeyIdSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\S3Deployer\AuthProfileSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\S3Deployer\AuthSecretAccessKeySetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\S3Deployer\AuthSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontDistributionIdSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontInvalidateEverythingPath' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontMaxInvalidationPathsSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\S3Deployer\EndpointSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\S3Deployer\RetainPathsSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\S3Deployer\S3BucketSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\S3Deployer\S3ObjectAcl' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\S3Deployer\S3PrefixSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\S3Deployer\S3RegionSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\S3Deployer\S3Setting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\SftpDeployer\AuthSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\SftpDeployer\HostSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\SftpDeployer\NetworkSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\SftpDeployer\PasswordSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\SftpDeployer\PortSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\SftpDeployer\SftpSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeyPasswordSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeySetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\SftpDeployer\TargetDirectorySetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\SftpDeployer\TimeoutSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\SftpDeployer\UsernameSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Module\Deployer\ZipfileDeployer\ZipfileSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\BackgroundProcessTimeout' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\CrawlerDomParserSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\CrawlerLowercaseUrlsSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\CrawlerProcessNotFoundSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\CrawlerSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\HttpAuthenticationPasswordSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\HttpAuthenticationSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\HttpAuthenticationUsernameSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\HttpConcurrencySetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\HttpDelaySetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\HttpNetworkSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\HttpTimeoutSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\HttpToHttpsSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\LoggingLevelSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\OverrideSiteUrlSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\PageNotFoundPathSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\SslVerifyBehaviorSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\SslVerifyPathSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\SslVerifySetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\UninstallDataSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\UninstallSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\UninstallSettingsSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Advanced\WorkDirectorySetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Build\AdditionalPathsSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Build\AdditionalRedirectsSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Build\AdditionalUrlsSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Build\DestinationUrlSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Build\ExcludeUrlsSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Build\PreviewUrlSetting' => \true,
            '.instanceof.Staatic\WordPress\Setting\SettingInterface.0.Staatic\WordPress\Setting\Deployment\DeploymentMethodSetting' => \true,
            '.service_locator.LQWyeDB' => \true,
            'Staatic\Vendor\Psr\Log\LoggerInterface' => \true,
            'Psr\Log\LoggerInterface $consoleLogger' => \true,
            'Psr\Log\LoggerInterface $databaseLogger' => \true,
            'Staatic\Vendor\Psr\SimpleCache\CacheInterface' => \true,
            'Staatic\Crawler\CrawlQueue\CrawlQueueInterface' => \true,
            'Staatic\Crawler\UrlExtractor\Mapping\HtmlUrlExtractorMapping' => \true,
            'Staatic\Crawler\UrlTransformer\UrlTransformerInterface' => \true,
            'Staatic\Framework\BuildRepository\BuildRepositoryInterface' => \true,
            'Staatic\Framework\DeploymentRepository\DeploymentRepositoryInterface' => \true,
            'Staatic\Framework\ResourceRepository\ResourceRepositoryInterface' => \true,
            'Staatic\Framework\ResultRepository\ResultRepositoryInterface' => \true,
            'Staatic\WordPress\Bridge\BuildRepository' => \true,
            'Staatic\WordPress\Bridge\CrawlProfile' => \true,
            'Staatic\WordPress\Bridge\CrawlQueue' => \true,
            'Staatic\WordPress\Bridge\DeploymentRepository' => \true,
            'Staatic\WordPress\Bridge\HtmlUrlExtractorMapping' => \true,
            'Staatic\WordPress\Bridge\HttpsToHttpMiddleware' => \true,
            'Staatic\WordPress\Bridge\KnownUrlsContainer' => \true,
            'Staatic\WordPress\Bridge\ResultRepository' => \true,
            'Staatic\WordPress\Bridge\RewriteResponseBodyMiddleware' => \true,
            'Staatic\WordPress\Cache\InvalidArgumentException' => \true,
            'Staatic\WordPress\Cache\TransientCache' => \true,
            'Staatic\WordPress\Cli\MigrateCommand' => \true,
            'Staatic\WordPress\Cli\PublishCommand' => \true,
            'Staatic\WordPress\Cli\RedeployCommand' => \true,
            'Staatic\WordPress\Composer\Scripts' => \true,
            'Staatic\WordPress\DependencyInjection\DetectPluginVersion' => \true,
            'Staatic\WordPress\DependencyInjection\WpdbWrapper' => \true,
            'Staatic\WordPress\Factory\CrawlProfileFactory' => \true,
            'Staatic\WordPress\Factory\DeploymentFactory' => \true,
            'Staatic\WordPress\Factory\EncrypterFactory' => \true,
            'Staatic\WordPress\Factory\HttpClientFactory' => \true,
            'Staatic\WordPress\Factory\KnownUrlsContainerFactory' => \true,
            'Staatic\WordPress\Factory\LoggerFactory' => \true,
            'Staatic\WordPress\Factory\PartialRendererFactory' => \true,
            'Staatic\WordPress\Factory\ResourceRepositoryFactory' => \true,
            'Staatic\WordPress\Factory\StaticDeployerFactory' => \true,
            'Staatic\WordPress\Factory\StaticGeneratorFactory' => \true,
            'Staatic\WordPress\Factory\UrlTransformerFactory' => \true,
            'Staatic\WordPress\ListTable\BulkAction\BulkAction' => \true,
            'Staatic\WordPress\ListTable\BulkAction\BulkActionInterface' => \true,
            'Staatic\WordPress\ListTable\Column\BytesColumn' => \true,
            'Staatic\WordPress\ListTable\Column\ColumnFactory' => \true,
            'Staatic\WordPress\ListTable\Column\DateColumn' => \true,
            'Staatic\WordPress\ListTable\Column\IdentifierColumn' => \true,
            'Staatic\WordPress\ListTable\Column\LogMessageColumn' => \true,
            'Staatic\WordPress\ListTable\Column\NumberColumn' => \true,
            'Staatic\WordPress\ListTable\Column\TextColumn' => \true,
            'Staatic\WordPress\ListTable\Column\TypeColumn' => \true,
            'Staatic\WordPress\ListTable\Column\UserColumn' => \true,
            'Staatic\WordPress\ListTable\Decorator\CallbackDecorator' => \true,
            'Staatic\WordPress\ListTable\Decorator\LinkDecorator' => \true,
            'Staatic\WordPress\ListTable\Decorator\TitleDecorator' => \true,
            'Staatic\WordPress\ListTable\RowAction\RowAction' => \true,
            'Staatic\WordPress\ListTable\RowAction\RowActionInterface' => \true,
            'Staatic\WordPress\ListTable\ValueAccessor' => \true,
            'Staatic\WordPress\ListTable\View\View' => \true,
            'Staatic\WordPress\ListTable\View\ViewInterface' => \true,
            'Staatic\WordPress\ListTable\WpListTable' => \true,
            'Staatic\WordPress\Logging\Contextable' => \true,
            'Staatic\WordPress\Logging\DatabaseLogger' => \true,
            'Staatic\WordPress\Logging\LogEntry' => \true,
            'Staatic\WordPress\Logging\LogEntryCleanup' => \true,
            'Staatic\WordPress\Logging\LogEntryRepository' => \true,
            'Staatic\WordPress\Logging\Logger' => \true,
            'Staatic\WordPress\Logging\LoggerInterface' => \true,
            'Staatic\WordPress\Migrations\MigrationCoordinator' => \true,
            'Staatic\WordPress\Migrations\MigrationCoordinatorFactory' => \true,
            'Staatic\WordPress\Migrations\Migrator' => \true,
            'Staatic\WordPress\Module\Admin\ExtendSiteHealth' => \true,
            'Staatic\WordPress\Module\Admin\Page\BuildResultPage' => \true,
            'Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsExportPage' => \true,
            'Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsPage' => \true,
            'Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsTable' => \true,
            'Staatic\WordPress\Module\Admin\Page\PublicationResults\PublicationResultsPage' => \true,
            'Staatic\WordPress\Module\Admin\Page\PublicationResults\PublicationResultsTable' => \true,
            'Staatic\WordPress\Module\Admin\Page\Publications\PublicationDeletePage' => \true,
            'Staatic\WordPress\Module\Admin\Page\Publications\PublicationDownloadPage' => \true,
            'Staatic\WordPress\Module\Admin\Page\Publications\PublicationStatusColumn' => \true,
            'Staatic\WordPress\Module\Admin\Page\Publications\PublicationSummaryPage' => \true,
            'Staatic\WordPress\Module\Admin\Page\Publications\PublicationTitleColumn' => \true,
            'Staatic\WordPress\Module\Admin\Page\Publications\PublicationsPage' => \true,
            'Staatic\WordPress\Module\Admin\Page\Publications\PublicationsTable' => \true,
            'Staatic\WordPress\Module\Admin\Page\PublishPage' => \true,
            'Staatic\WordPress\Module\Admin\Page\PublishSubsetPage' => \true,
            'Staatic\WordPress\Module\Admin\Page\SettingsPage' => \true,
            'Staatic\WordPress\Module\Admin\Page\TestRequestPage' => \true,
            'Staatic\WordPress\Module\Admin\RegisterAdminBar' => \true,
            'Staatic\WordPress\Module\Admin\RegisterAssets' => \true,
            'Staatic\WordPress\Module\Admin\RegisterNavigation' => \true,
            'Staatic\WordPress\Module\Admin\RegisterPluginActionLinks' => \true,
            'Staatic\WordPress\Module\Admin\Widget\PublicationLogsWidget' => \true,
            'Staatic\WordPress\Module\Admin\Widget\PublicationStatusWidget' => \true,
            'Staatic\WordPress\Module\Cleanup' => \true,
            'Staatic\WordPress\Module\Cli\RegisterCommands' => \true,
            'Staatic\WordPress\Module\Compatibility' => \true,
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\ApacheConfigurationFileSetting' => \true,
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\ConfigurationFilesSetting' => \true,
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\FilesystemDeployStrategyFactory' => \true,
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\FilesystemDeployerModule' => \true,
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\NginxConfigurationFileSetting' => \true,
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\RetainPaths' => \true,
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\RetainPathsSetting' => \true,
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\SymlinkUploadsDirectorySetting' => \true,
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\TargetDirectorySetting' => \true,
            'Staatic\WordPress\Module\Deployer\GithubDeployer\AuthSetting' => \true,
            'Staatic\WordPress\Module\Deployer\GithubDeployer\BranchSetting' => \true,
            'Staatic\WordPress\Module\Deployer\GithubDeployer\CommitMessageSetting' => \true,
            'Staatic\WordPress\Module\Deployer\GithubDeployer\GitSetting' => \true,
            'Staatic\WordPress\Module\Deployer\GithubDeployer\GithubDeployStrategyFactory' => \true,
            'Staatic\WordPress\Module\Deployer\GithubDeployer\GithubDeployerModule' => \true,
            'Staatic\WordPress\Module\Deployer\GithubDeployer\GithubStatusEndpoint' => \true,
            'Staatic\WordPress\Module\Deployer\GithubDeployer\PrefixSetting' => \true,
            'Staatic\WordPress\Module\Deployer\GithubDeployer\RepositorySetting' => \true,
            'Staatic\WordPress\Module\Deployer\GithubDeployer\RetainPaths' => \true,
            'Staatic\WordPress\Module\Deployer\GithubDeployer\RetainPathsSetting' => \true,
            'Staatic\WordPress\Module\Deployer\GithubDeployer\TokenSetting' => \true,
            'Staatic\WordPress\Module\Deployer\NetlifyDeployer\AccessTokenSetting' => \true,
            'Staatic\WordPress\Module\Deployer\NetlifyDeployer\AuthSetting' => \true,
            'Staatic\WordPress\Module\Deployer\NetlifyDeployer\NetlifyDeployStrategyFactory' => \true,
            'Staatic\WordPress\Module\Deployer\NetlifyDeployer\NetlifyDeployerModule' => \true,
            'Staatic\WordPress\Module\Deployer\NetlifyDeployer\NetlifyStatusEndpoint' => \true,
            'Staatic\WordPress\Module\Deployer\NetlifyDeployer\SiteIdSetting' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\AuthAccessKeyIdSetting' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\AuthProfileSetting' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\AuthSecretAccessKeySetting' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\AuthSetting' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\AwsDeployStrategyFactory' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontDistributionIdSetting' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontInvalidateEverythingPath' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontMaxInvalidationPathsSetting' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontSetting' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\EndpointSetting' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\RetainPaths' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\RetainPathsSetting' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\S3BucketSetting' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\S3DeployerModule' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\S3ObjectAcl' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\S3PrefixSetting' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\S3RegionSetting' => \true,
            'Staatic\WordPress\Module\Deployer\S3Deployer\S3Setting' => \true,
            'Staatic\WordPress\Module\Deployer\SftpDeployer\AuthSetting' => \true,
            'Staatic\WordPress\Module\Deployer\SftpDeployer\HostSetting' => \true,
            'Staatic\WordPress\Module\Deployer\SftpDeployer\NetworkSetting' => \true,
            'Staatic\WordPress\Module\Deployer\SftpDeployer\PasswordSetting' => \true,
            'Staatic\WordPress\Module\Deployer\SftpDeployer\PortSetting' => \true,
            'Staatic\WordPress\Module\Deployer\SftpDeployer\SftpDeployStrategyFactory' => \true,
            'Staatic\WordPress\Module\Deployer\SftpDeployer\SftpDeployerModule' => \true,
            'Staatic\WordPress\Module\Deployer\SftpDeployer\SftpLoginException' => \true,
            'Staatic\WordPress\Module\Deployer\SftpDeployer\SftpSetting' => \true,
            'Staatic\WordPress\Module\Deployer\SftpDeployer\SftpStatusEndpoint' => \true,
            'Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeyPasswordSetting' => \true,
            'Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeySetting' => \true,
            'Staatic\WordPress\Module\Deployer\SftpDeployer\TargetDirectorySetting' => \true,
            'Staatic\WordPress\Module\Deployer\SftpDeployer\TimeoutSetting' => \true,
            'Staatic\WordPress\Module\Deployer\SftpDeployer\UsernameSetting' => \true,
            'Staatic\WordPress\Module\Deployer\ZipfileDeployer\ZipfileDeployerModule' => \true,
            'Staatic\WordPress\Module\Deployer\ZipfileDeployer\ZipfileSetting' => \true,
            'Staatic\WordPress\Module\EnsureMigrated' => \true,
            'Staatic\WordPress\Module\HttpAuthHeaders' => \true,
            'Staatic\WordPress\Module\HttpsToHttpDowngrade' => \true,
            'Staatic\WordPress\Module\Integration\AvadaTheme' => \true,
            'Staatic\WordPress\Module\Integration\ElementorPlugin' => \true,
            'Staatic\WordPress\Module\Integration\FlyingPressPlugin' => \true,
            'Staatic\WordPress\Module\Integration\RankMathPlugin' => \true,
            'Staatic\WordPress\Module\Integration\RedirectionPlugin' => \true,
            'Staatic\WordPress\Module\Integration\SafeRedirectManagerPlugin' => \true,
            'Staatic\WordPress\Module\Integration\Simple301RedirectsPlugin' => \true,
            'Staatic\WordPress\Module\Integration\Wordpress' => \true,
            'Staatic\WordPress\Module\Integration\WpFastestCachePlugin' => \true,
            'Staatic\WordPress\Module\Integration\YoastPremiumPlugin' => \true,
            'Staatic\WordPress\Module\LoadTextDomain' => \true,
            'Staatic\WordPress\Module\RegisterFieldTypes' => \true,
            'Staatic\WordPress\Module\RegisterOptions' => \true,
            'Staatic\WordPress\Module\RegisterPublishHook' => \true,
            'Staatic\WordPress\Module\RegisterSchedules' => \true,
            'Staatic\WordPress\Module\RegisterSettings' => \true,
            'Staatic\WordPress\Module\Rest\PublicationLogsEndpoint' => \true,
            'Staatic\WordPress\Module\Rest\PublicationStatusEndpoint' => \true,
            'Staatic\WordPress\Module\Rest\SiteHealthTestsEndpoint' => \true,
            'Staatic\WordPress\Module\ScheduleTestRequest' => \true,
            'Staatic\WordPress\Publication\BackgroundPublisher' => \true,
            'Staatic\WordPress\Publication\Publication' => \true,
            'Staatic\WordPress\Publication\PublicationCleanup' => \true,
            'Staatic\WordPress\Publication\PublicationManager' => \true,
            'Staatic\WordPress\Publication\PublicationManagerInterface' => \true,
            'Staatic\WordPress\Publication\PublicationRepository' => \true,
            'Staatic\WordPress\Publication\PublicationTaskProvider' => \true,
            'Staatic\WordPress\Publication\Task\CrawlTask' => \true,
            'Staatic\WordPress\Publication\Task\DeployTask' => \true,
            'Staatic\WordPress\Publication\Task\FinishCrawlerTask' => \true,
            'Staatic\WordPress\Publication\Task\FinishDeploymentTask' => \true,
            'Staatic\WordPress\Publication\Task\FinishTask' => \true,
            'Staatic\WordPress\Publication\Task\InitializeCrawlerTask' => \true,
            'Staatic\WordPress\Publication\Task\InitiateDeploymentTask' => \true,
            'Staatic\WordPress\Publication\Task\PostProcessTask' => \true,
            'Staatic\WordPress\Publication\Task\SetupTask' => \true,
            'Staatic\WordPress\Publication\Task\TaskCollection' => \true,
            'Staatic\WordPress\Request\TestRequest' => \true,
            'Staatic\WordPress\Service\AdditionalPaths' => \true,
            'Staatic\WordPress\Service\AdditionalRedirects' => \true,
            'Staatic\WordPress\Service\AdditionalUrls' => \true,
            'Staatic\WordPress\Service\AdminNavigation' => \true,
            'Staatic\WordPress\Service\Encrypter' => \true,
            'Staatic\WordPress\Service\Encrypter\InvalidValueException' => \true,
            'Staatic\WordPress\Service\Encrypter\PossiblyUnencryptedValueException' => \true,
            'Staatic\WordPress\Service\ExcludeUrls' => \true,
            'Staatic\WordPress\Service\Formatter' => \true,
            'Staatic\WordPress\Service\HealthChecks' => \true,
            'Staatic\WordPress\Service\PartialRenderer' => \true,
            'Staatic\WordPress\Service\Polyfill' => \true,
            'Staatic\WordPress\Service\PublicationArchiver' => \true,
            'Staatic\WordPress\Service\PublicationLogsExporter' => \true,
            'Staatic\WordPress\Service\ResourceCleanup' => \true,
            'Staatic\WordPress\Service\Scheduler' => \true,
            'Staatic\WordPress\Service\Settings' => \true,
            'Staatic\WordPress\Service\SiteUrlProvider' => \true,
            'Staatic\WordPress\SettingGroup\SettingGroup' => \true,
            'Staatic\WordPress\SettingGroup\SettingGroupInterface' => \true,
            'Staatic\WordPress\Setting\Advanced\BackgroundProcessTimeout' => \true,
            'Staatic\WordPress\Setting\Advanced\CrawlerDomParserSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\CrawlerLowercaseUrlsSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\CrawlerProcessNotFoundSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\CrawlerSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\HttpAuthenticationPasswordSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\HttpAuthenticationSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\HttpAuthenticationUsernameSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\HttpConcurrencySetting' => \true,
            'Staatic\WordPress\Setting\Advanced\HttpDelaySetting' => \true,
            'Staatic\WordPress\Setting\Advanced\HttpNetworkSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\HttpTimeoutSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\HttpToHttpsSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\LoggingLevelSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\OverrideSiteUrlSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\PageNotFoundPathSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\SslVerifyBehaviorSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\SslVerifyPathSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\SslVerifySetting' => \true,
            'Staatic\WordPress\Setting\Advanced\UninstallDataSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\UninstallSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\UninstallSettingsSetting' => \true,
            'Staatic\WordPress\Setting\Advanced\WorkDirectorySetting' => \true,
            'Staatic\WordPress\Setting\Build\AdditionalPathsSetting' => \true,
            'Staatic\WordPress\Setting\Build\AdditionalRedirectsSetting' => \true,
            'Staatic\WordPress\Setting\Build\AdditionalUrlsSetting' => \true,
            'Staatic\WordPress\Setting\Build\DestinationUrlSetting' => \true,
            'Staatic\WordPress\Setting\Build\ExcludeUrlsSetting' => \true,
            'Staatic\WordPress\Setting\Build\PreviewUrlSetting' => \true,
            'Staatic\WordPress\Setting\Deployment\DeploymentMethodSetting' => \true,
            'Staatic\WordPress\Uninstaller' => \true,
            'Staatic\WordPress\Util\CsvUtil' => \true,
            'Staatic\WordPress\Util\DateUtil' => \true,
            'Staatic\WordPress\Util\HttpUtil' => \true,
            'Staatic\WordPress\Util\TimeLimit' => \true,
            'Staatic\WordPress\Util\WordpressEnv' => \true,
            'console_logger' => \true,
            'database_logger' => \true,
            'wpdb' => \true
        ];
    }

    /**
     * @param Closure $factory
     */
    protected function createProxy($class, $factory)
    {
        return $factory();
    }

    /**
     * Gets the public 'Staatic\WordPress\Activator' shared autowired service.
     *
     * @return Activator
     */
    protected function getActivatorService()
    {
        return $this->services['Staatic\WordPress\Activator'] = new Activator(
            $this->privates['Staatic\WordPress\Migrations\MigrationCoordinatorFactory'] ?? $this->getMigrationCoordinatorFactoryService()
        );
    }

    /**
     * Gets the public 'Staatic\WordPress\Deactivator' shared autowired service.
     *
     * @return Deactivator
     */
    protected function getDeactivatorService()
    {
        return $this->services['Staatic\WordPress\Deactivator'] = new Deactivator(
            $this->services['staatic.scheduler'] = $this->services['staatic.scheduler'] ?? new Scheduler()
        );
    }

    /**
     * Gets the public 'Staatic\WordPress\Module\ModuleCollection' shared autowired service.
     *
     * @return ModuleCollection
     */
    protected function getModuleCollectionService()
    {
        return $this->services['Staatic\WordPress\Module\ModuleCollection'] = new ModuleCollection(new RewindableGenerator(function () {
            yield 0 => $this->privates['Staatic\WordPress\Module\EnsureMigrated'] ?? $this->getEnsureMigratedService();
            yield 1 => $this->privates['Staatic\WordPress\Module\RegisterFieldTypes'] ?? $this->getRegisterFieldTypesService();
            yield 2 => $this->privates['Staatic\WordPress\Module\LoadTextDomain'] = $this->privates['Staatic\WordPress\Module\LoadTextDomain'] ?? new LoadTextDomain();
            yield 3 => $this->privates['Staatic\WordPress\Module\RegisterOptions'] = $this->privates['Staatic\WordPress\Module\RegisterOptions'] ?? new RegisterOptions();
            yield 4 => $this->privates['Staatic\WordPress\Module\RegisterSchedules'] = $this->privates['Staatic\WordPress\Module\RegisterSchedules'] ?? new RegisterSchedules();
            yield 5 => $this->privates['Staatic\WordPress\Module\RegisterSettings'] ?? $this->getRegisterSettingsService();
            yield 6 => $this->privates['Staatic\WordPress\Module\Admin\RegisterAssets'] = $this->privates['Staatic\WordPress\Module\Admin\RegisterAssets'] ?? new RegisterAssets(
                '1.10.4'
            );
            yield 7 => $this->privates['Staatic\WordPress\Module\Admin\RegisterNavigation'] ?? $this->getRegisterNavigationService();
            yield 8 => $this->privates['Staatic\WordPress\Module\Admin\ExtendSiteHealth'] ?? $this->getExtendSiteHealthService();
            yield 9 => $this->privates['Staatic\WordPress\Module\Admin\Page\BuildResultPage'] ?? $this->getBuildResultPageService();
            yield 10 => $this->privates['Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsExportPage'] ?? $this->getPublicationLogsExportPageService();
            yield 11 => $this->privates['Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsPage'] ?? $this->getPublicationLogsPageService();
            yield 12 => $this->privates['Staatic\WordPress\Module\Admin\Page\PublicationResults\PublicationResultsPage'] ?? $this->getPublicationResultsPageService();
            yield 13 => $this->privates['Staatic\WordPress\Module\Admin\Page\Publications\PublicationDeletePage'] ?? $this->getPublicationDeletePageService();
            yield 14 => $this->privates['Staatic\WordPress\Module\Admin\Page\Publications\PublicationDownloadPage'] ?? $this->getPublicationDownloadPageService();
            yield 15 => $this->privates['Staatic\WordPress\Module\Admin\Page\Publications\PublicationSummaryPage'] ?? $this->getPublicationSummaryPageService();
            yield 16 => $this->privates['Staatic\WordPress\Module\Admin\Page\Publications\PublicationsPage'] ?? $this->getPublicationsPageService();
            yield 17 => $this->privates['Staatic\WordPress\Module\Admin\Page\PublishPage'] ?? $this->getPublishPageService();
            yield 18 => $this->privates['Staatic\WordPress\Module\Admin\Page\PublishSubsetPage'] ?? $this->getPublishSubsetPageService();
            yield 19 => $this->privates['Staatic\WordPress\Module\Admin\Page\SettingsPage'] ?? $this->getSettingsPageService();
            yield 20 => $this->privates['Staatic\WordPress\Module\Admin\Page\TestRequestPage'] ?? $this->getTestRequestPageService();
            yield 21 => $this->privates['Staatic\WordPress\Module\Admin\RegisterAdminBar'] ?? $this->getRegisterAdminBarService();
            yield 22 => $this->privates['Staatic\WordPress\Module\Admin\RegisterPluginActionLinks'] = $this->privates['Staatic\WordPress\Module\Admin\RegisterPluginActionLinks'] ?? new RegisterPluginActionLinks();
            yield 23 => $this->privates['Staatic\WordPress\Module\Admin\Widget\PublicationLogsWidget'] ?? $this->getPublicationLogsWidgetService();
            yield 24 => $this->privates['Staatic\WordPress\Module\Admin\Widget\PublicationStatusWidget'] ?? $this->getPublicationStatusWidgetService();
            yield 25 => $this->privates['Staatic\WordPress\Module\Cleanup'] ?? $this->getCleanupService();
            yield 26 => $this->privates['Staatic\WordPress\Module\Cli\RegisterCommands'] ?? $this->getRegisterCommandsService();
            yield 27 => $this->privates['Staatic\WordPress\Module\Compatibility'] = $this->privates['Staatic\WordPress\Module\Compatibility'] ?? new Compatibility();
            yield 28 => $this->privates['Staatic\WordPress\Module\Deployer\FilesystemDeployer\FilesystemDeployerModule'] ?? $this->getFilesystemDeployerModuleService();
            yield 29 => $this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\GithubDeployerModule'] ?? $this->getGithubDeployerModuleService();
            yield 30 => $this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\GithubStatusEndpoint'] ?? $this->getGithubStatusEndpointService();
            yield 31 => $this->privates['Staatic\WordPress\Module\Deployer\NetlifyDeployer\NetlifyDeployerModule'] ?? $this->getNetlifyDeployerModuleService();
            yield 32 => $this->privates['Staatic\WordPress\Module\Deployer\NetlifyDeployer\NetlifyStatusEndpoint'] ?? $this->getNetlifyStatusEndpointService();
            yield 33 => $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\S3DeployerModule'] ?? $this->getS3DeployerModuleService();
            yield 34 => $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\SftpDeployerModule'] ?? $this->getSftpDeployerModuleService();
            yield 35 => $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\SftpStatusEndpoint'] = $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\SftpStatusEndpoint'] ?? new SftpStatusEndpoint();
            yield 36 => $this->privates['Staatic\WordPress\Module\Deployer\ZipfileDeployer\ZipfileDeployerModule'] ?? $this->getZipfileDeployerModuleService();
            yield 37 => $this->privates['Staatic\WordPress\Module\HttpAuthHeaders'] ?? $this->getHttpAuthHeadersService();
            yield 38 => $this->privates['Staatic\WordPress\Module\HttpsToHttpDowngrade'] ?? $this->getHttpsToHttpDowngradeService();
            yield 39 => $this->privates['Staatic\WordPress\Module\Integration\AvadaTheme'] = $this->privates['Staatic\WordPress\Module\Integration\AvadaTheme'] ?? new AvadaTheme();
            yield 40 => $this->privates['Staatic\WordPress\Module\Integration\ElementorPlugin'] ?? $this->getElementorPluginService();
            yield 41 => $this->privates['Staatic\WordPress\Module\Integration\FlyingPressPlugin'] = $this->privates['Staatic\WordPress\Module\Integration\FlyingPressPlugin'] ?? new FlyingPressPlugin();
            yield 42 => $this->privates['Staatic\WordPress\Module\Integration\RankMathPlugin'] = $this->privates['Staatic\WordPress\Module\Integration\RankMathPlugin'] ?? new RankMathPlugin();
            yield 43 => $this->privates['Staatic\WordPress\Module\Integration\RedirectionPlugin'] = $this->privates['Staatic\WordPress\Module\Integration\RedirectionPlugin'] ?? new RedirectionPlugin();
            yield 44 => $this->privates['Staatic\WordPress\Module\Integration\SafeRedirectManagerPlugin'] = $this->privates['Staatic\WordPress\Module\Integration\SafeRedirectManagerPlugin'] ?? new SafeRedirectManagerPlugin();
            yield 45 => $this->privates['Staatic\WordPress\Module\Integration\Simple301RedirectsPlugin'] = $this->privates['Staatic\WordPress\Module\Integration\Simple301RedirectsPlugin'] ?? new Simple301RedirectsPlugin();
            yield 46 => $this->privates['Staatic\WordPress\Module\Integration\Wordpress'] = $this->privates['Staatic\WordPress\Module\Integration\Wordpress'] ?? new Wordpress();
            yield 47 => $this->privates['Staatic\WordPress\Module\Integration\WpFastestCachePlugin'] = $this->privates['Staatic\WordPress\Module\Integration\WpFastestCachePlugin'] ?? new WpFastestCachePlugin();
            yield 48 => $this->privates['Staatic\WordPress\Module\Integration\YoastPremiumPlugin'] = $this->privates['Staatic\WordPress\Module\Integration\YoastPremiumPlugin'] ?? new YoastPremiumPlugin();
            yield 49 => $this->privates['Staatic\WordPress\Module\RegisterPublishHook'] ?? $this->getRegisterPublishHookService();
            yield 50 => $this->privates['Staatic\WordPress\Module\Rest\PublicationLogsEndpoint'] ?? $this->getPublicationLogsEndpointService();
            yield 51 => $this->privates['Staatic\WordPress\Module\Rest\PublicationStatusEndpoint'] ?? $this->getPublicationStatusEndpointService();
            yield 52 => $this->privates['Staatic\WordPress\Module\Rest\SiteHealthTestsEndpoint'] ?? $this->getSiteHealthTestsEndpointService();
            yield 53 => $this->privates['Staatic\WordPress\Module\ScheduleTestRequest'] ?? $this->getScheduleTestRequestService();
        }, 54));
    }

    /**
     * Gets the public 'staatic.admin_navigation' shared autowired service.
     *
     * @return AdminNavigation
     */
    protected function getStaatic_AdminNavigationService()
    {
        return $this->services['staatic.admin_navigation'] = new AdminNavigation();
    }

    /**
     * Gets the public 'staatic.build_repository' shared autowired service.
     *
     * @return BuildRepository
     */
    protected function getStaatic_BuildRepositoryService($lazyLoad = \true)
    {
        if (\true === $lazyLoad) {
            return $this->services['staatic.build_repository'] = $this->createProxy(
                'BuildRepositoryProxy52119c7',
                function () {
                return \Staatic\Vendor\BuildRepositoryProxy52119c7::createLazyProxy(function () {
                    return $this->getStaatic_BuildRepositoryService(\false);
                });
            }
            );
        }

        return new BuildRepository(
            $this->privates['wpdb'] ?? $this->getWpdbService(),
            $this->privates['Staatic\WordPress\Bridge\ResultRepository'] ?? $this->getResultRepositoryService()
        );
    }

    /**
     * Gets the public 'staatic.cache' shared autowired service.
     *
     * @return TransientCache
     */
    protected function getStaatic_CacheService($lazyLoad = \true)
    {
        if (\true === $lazyLoad) {
            return $this->services['staatic.cache'] = $this->createProxy('TransientCacheProxy1ba92cc', function () {
                return \Staatic\Vendor\TransientCacheProxy1ba92cc::createLazyProxy(function () {
                    return $this->getStaatic_CacheService(\false);
                });
            });
        }

        return new TransientCache($this->privates['wpdb'] ?? $this->getWpdbService());
    }

    /**
     * Gets the public 'staatic.crawl_queue' shared autowired service.
     *
     * @return CrawlQueue
     */
    protected function getStaatic_CrawlQueueService($lazyLoad = \true)
    {
        if (\true === $lazyLoad) {
            return $this->services['staatic.crawl_queue'] = $this->createProxy('CrawlQueueProxy5f75fbb', function () {
                return \Staatic\Vendor\CrawlQueueProxy5f75fbb::createLazyProxy(function () {
                    return $this->getStaatic_CrawlQueueService(\false);
                });
            });
        }

        return new CrawlQueue($this->privates['wpdb'] ?? $this->getWpdbService());
    }

    /**
     * Gets the public 'staatic.deployment_repository' shared autowired service.
     *
     * @return DeploymentRepository
     */
    protected function getStaatic_DeploymentRepositoryService($lazyLoad = \true)
    {
        if (\true === $lazyLoad) {
            return $this->services['staatic.deployment_repository'] = $this->createProxy(
                'DeploymentRepositoryProxyE31553a',
                function () {
                return \Staatic\Vendor\DeploymentRepositoryProxyE31553a::createLazyProxy(function () {
                    return $this->getStaatic_DeploymentRepositoryService(\false);
                });
            }
            );
        }

        return new DeploymentRepository($this->privates['wpdb'] ?? $this->getWpdbService());
    }

    /**
     * Gets the public 'staatic.http_client_factory' shared autowired service.
     *
     * @return HttpClientFactory
     */
    protected function getStaatic_HttpClientFactoryService()
    {
        return $this->services['staatic.http_client_factory'] = new HttpClientFactory(
            $this->privates['Staatic\WordPress\Setting\Advanced\HttpConcurrencySetting'] = $this->privates['Staatic\WordPress\Setting\Advanced\HttpConcurrencySetting'] ?? new HttpConcurrencySetting(),
            $this->privates['Staatic\WordPress\Setting\Advanced\HttpTimeoutSetting'] = $this->privates['Staatic\WordPress\Setting\Advanced\HttpTimeoutSetting'] ?? new HttpTimeoutSetting(),
            $this->privates['Staatic\WordPress\Setting\Advanced\HttpDelaySetting'] = $this->privates['Staatic\WordPress\Setting\Advanced\HttpDelaySetting'] ?? new HttpDelaySetting(),
            $this->privates['Staatic\WordPress\Setting\Advanced\SslVerifyBehaviorSetting'] = $this->privates['Staatic\WordPress\Setting\Advanced\SslVerifyBehaviorSetting'] ?? new SslVerifyBehaviorSetting(),
            $this->privates['Staatic\WordPress\Setting\Advanced\SslVerifyPathSetting'] = $this->privates['Staatic\WordPress\Setting\Advanced\SslVerifyPathSetting'] ?? new SslVerifyPathSetting(),
            $this->privates['Staatic\WordPress\Setting\Advanced\HttpAuthenticationUsernameSetting'] = $this->privates['Staatic\WordPress\Setting\Advanced\HttpAuthenticationUsernameSetting'] ?? new HttpAuthenticationUsernameSetting(),
            $this->privates['Staatic\WordPress\Setting\Advanced\HttpAuthenticationPasswordSetting'] = $this->privates['Staatic\WordPress\Setting\Advanced\HttpAuthenticationPasswordSetting'] ?? new HttpAuthenticationPasswordSetting(),
            $this->privates['Staatic\WordPress\Setting\Advanced\HttpToHttpsSetting'] = $this->privates['Staatic\WordPress\Setting\Advanced\HttpToHttpsSetting'] ?? new HttpToHttpsSetting(),
            $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] = $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] ?? new SiteUrlProvider()
        );
    }

    /**
     * Gets the public 'staatic.log_entry_repository' shared autowired service.
     *
     * @return LogEntryRepository
     */
    protected function getStaatic_LogEntryRepositoryService()
    {
        return $this->services['staatic.log_entry_repository'] = new LogEntryRepository(
            $this->privates['wpdb'] ?? $this->getWpdbService()
        );
    }

    /**
     * Gets the public 'staatic.logger' shared autowired service.
     *
     * @return Logger
     */
    protected function getStaatic_LoggerService($lazyLoad = \true)
    {
        if (\true === $lazyLoad) {
            return $this->services['staatic.logger'] = $this->createProxy('LoggerProxy58fa090', function () {
                return \Staatic\Vendor\LoggerProxy58fa090::createLazyProxy(function () {
                    return $this->getStaatic_LoggerService(\false);
                });
            });
        }

        return (new LoggerFactory(
            $this->privates['Staatic\WordPress\Setting\Advanced\LoggingLevelSetting'] = $this->privates['Staatic\WordPress\Setting\Advanced\LoggingLevelSetting'] ?? new LoggingLevelSetting(),
            new DatabaseLogger(
            $this->services['staatic.log_entry_repository'] ?? $this->getStaatic_LogEntryRepositoryService()
        ),
            new ConsoleLogger()
        ))->__invoke();
    }

    /**
     * Gets the public 'staatic.publication_manager' shared autowired service.
     *
     * @return PublicationManager
     */
    protected function getStaatic_PublicationManagerService()
    {
        $a = $this->services['staatic.publication_repository'] ?? $this->getStaatic_PublicationRepositoryService();

        return $this->services['staatic.publication_manager'] = new PublicationManager(
            $this->privates['wpdb'] ?? $this->getWpdbService(),
            $this->services['staatic.build_repository'] ?? $this->getStaatic_BuildRepositoryService(),
            $a,
            new BackgroundPublisher(
            $this->services['staatic.logger'] ?? $this->getStaatic_LoggerService(),
            $a,
            $this->services['staatic.publication_task_provider'] ?? $this->getStaatic_PublicationTaskProviderService()
        ),
            new DeploymentFactory(
            $this->services['staatic.deployment_repository'] ?? $this->getStaatic_DeploymentRepositoryService()
        ),
            $this->privates['Staatic\WordPress\Setting\Build\DestinationUrlSetting'] = $this->privates['Staatic\WordPress\Setting\Build\DestinationUrlSetting'] ?? new DestinationUrlSetting(),
            $this->privates['Staatic\WordPress\Setting\Build\PreviewUrlSetting'] = $this->privates['Staatic\WordPress\Setting\Build\PreviewUrlSetting'] ?? new PreviewUrlSetting(),
            $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] = $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] ?? new SiteUrlProvider()
        );
    }

    /**
     * Gets the public 'staatic.publication_repository' shared autowired service.
     *
     * @return PublicationRepository
     */
    protected function getStaatic_PublicationRepositoryService()
    {
        $a = $this->privates['wpdb'] ?? $this->getWpdbService();

        return $this->services['staatic.publication_repository'] = new PublicationRepository($a, new BuildRepository(
            $a,
            $this->privates['Staatic\WordPress\Bridge\ResultRepository'] ?? $this->getResultRepositoryService()
        ), new DeploymentRepository(
            $a
        ), $this->privates['Staatic\WordPress\Setting\Advanced\WorkDirectorySetting'] = $this->privates['Staatic\WordPress\Setting\Advanced\WorkDirectorySetting'] ?? new WorkDirectorySetting());
    }

    /**
     * Gets the public 'staatic.publication_task_provider' shared autowired service.
     *
     * @return PublicationTaskProvider
     */
    protected function getStaatic_PublicationTaskProviderService()
    {
        return $this->services['staatic.publication_task_provider'] = new PublicationTaskProvider(new RewindableGenerator(function () {
            yield 0 => $this->privates['Staatic\WordPress\Publication\Task\SetupTask'] ?? $this->getSetupTaskService();
            yield 1 => $this->privates['Staatic\WordPress\Publication\Task\InitializeCrawlerTask'] ?? $this->getInitializeCrawlerTaskService();
            yield 2 => $this->privates['Staatic\WordPress\Publication\Task\CrawlTask'] ?? $this->getCrawlTaskService();
            yield 3 => $this->privates['Staatic\WordPress\Publication\Task\FinishCrawlerTask'] ?? $this->getFinishCrawlerTaskService();
            yield 4 => $this->privates['Staatic\WordPress\Publication\Task\PostProcessTask'] ?? $this->getPostProcessTaskService();
            yield 5 => $this->privates['Staatic\WordPress\Publication\Task\InitiateDeploymentTask'] ?? $this->getInitiateDeploymentTaskService();
            yield 6 => $this->privates['Staatic\WordPress\Publication\Task\DeployTask'] ?? $this->getDeployTaskService();
            yield 7 => $this->privates['Staatic\WordPress\Publication\Task\FinishDeploymentTask'] ?? $this->getFinishDeploymentTaskService();
            yield 8 => $this->privates['Staatic\WordPress\Publication\Task\FinishTask'] ?? $this->getFinishTaskService();
        }, 9));
    }

    /**
     * Gets the public 'staatic.resource_repository' shared autowired service.
     *
     * @return ResourceRepositoryInterface
     */
    protected function getStaatic_ResourceRepositoryService($lazyLoad = \true)
    {
        if (\true === $lazyLoad) {
            return $this->services['staatic.resource_repository'] = $this->createProxy(
                'ResourceRepositoryInterfaceProxyAe4180b',
                function () {
                return \Staatic\Vendor\ResourceRepositoryInterfaceProxyAe4180b::createLazyProxy(function () {
                    return $this->getStaatic_ResourceRepositoryService(\false);
                });
            }
            );
        }

        return (new ResourceRepositoryFactory(
            $this->privates['Staatic\WordPress\Setting\Advanced\WorkDirectorySetting'] = $this->privates['Staatic\WordPress\Setting\Advanced\WorkDirectorySetting'] ?? new WorkDirectorySetting()
        ))->__invoke();
    }

    /**
     * Gets the public 'staatic.result_repository' shared autowired service.
     *
     * @return ResultRepository
     */
    protected function getStaatic_ResultRepositoryService($lazyLoad = \true)
    {
        if (\true === $lazyLoad) {
            return $this->services['staatic.result_repository'] = $this->createProxy(
                'ResultRepositoryProxyE12a645',
                function () {
                return \Staatic\Vendor\ResultRepositoryProxyE12a645::createLazyProxy(function () {
                    return $this->getStaatic_ResultRepositoryService(\false);
                });
            }
            );
        }

        return new ResultRepository($this->privates['wpdb'] ?? $this->getWpdbService());
    }

    /**
     * Gets the public 'staatic.scheduler' shared autowired service.
     *
     * @return Scheduler
     */
    protected function getStaatic_SchedulerService()
    {
        return $this->services['staatic.scheduler'] = new Scheduler();
    }

    /**
     * Gets the public 'staatic.settings' shared autowired service.
     *
     * @return Settings
     */
    protected function getStaatic_SettingsService()
    {
        return $this->services['staatic.settings'] = new Settings(
            $this->privates['Staatic\WordPress\Service\PartialRenderer'] ?? $this->getPartialRendererService(),
            new EncrypterFactory()
        );
    }

    /**
     * Gets the public 'staatic.url_transformer' shared autowired service.
     *
     * @return UrlTransformerInterface
     */
    protected function getStaatic_UrlTransformerService($lazyLoad = \true)
    {
        if (\true === $lazyLoad) {
            return $this->services['staatic.url_transformer'] = $this->createProxy(
                'UrlTransformerInterfaceProxy3bb5952',
                function () {
                return \Staatic\Vendor\UrlTransformerInterfaceProxy3bb5952::createLazyProxy(function () {
                    return $this->getStaatic_UrlTransformerService(\false);
                });
            }
            );
        }

        return ($this->privates['Staatic\WordPress\Factory\UrlTransformerFactory'] ?? $this->getUrlTransformerFactoryService())->__invoke();
    }

    /**
     * Gets the private '.service_locator.LQWyeDB' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    protected function get_ServiceLocator_LQWyeDBService()
    {
        return $this->privates['.service_locator.LQWyeDB'] = new ServiceLocator($this->getService, [
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\ApacheConfigurationFileSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\FilesystemDeployer\ApacheConfigurationFileSetting',
                'getApacheConfigurationFileSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\ConfigurationFilesSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\FilesystemDeployer\ConfigurationFilesSetting',
                'getConfigurationFilesSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\NginxConfigurationFileSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\FilesystemDeployer\NginxConfigurationFileSetting',
                'getNginxConfigurationFileSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\RetainPathsSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\FilesystemDeployer\RetainPathsSetting',
                'getRetainPathsSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\SymlinkUploadsDirectorySetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\FilesystemDeployer\SymlinkUploadsDirectorySetting',
                'getSymlinkUploadsDirectorySettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\TargetDirectorySetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\FilesystemDeployer\TargetDirectorySetting',
                'getTargetDirectorySettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\GithubDeployer\AuthSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\GithubDeployer\AuthSetting',
                'getAuthSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\GithubDeployer\BranchSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\GithubDeployer\BranchSetting',
                'getBranchSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\GithubDeployer\CommitMessageSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\GithubDeployer\CommitMessageSetting',
                'getCommitMessageSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\GithubDeployer\GitSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\GithubDeployer\GitSetting',
                'getGitSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\GithubDeployer\PrefixSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\GithubDeployer\PrefixSetting',
                'getPrefixSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\GithubDeployer\RepositorySetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\GithubDeployer\RepositorySetting',
                'getRepositorySettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\GithubDeployer\RetainPathsSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\GithubDeployer\RetainPathsSetting',
                'getRetainPathsSetting2Service',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\GithubDeployer\TokenSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\GithubDeployer\TokenSetting',
                'getTokenSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\NetlifyDeployer\AccessTokenSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\NetlifyDeployer\AccessTokenSetting',
                'getAccessTokenSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\NetlifyDeployer\AuthSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\NetlifyDeployer\AuthSetting',
                'getAuthSetting2Service',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\NetlifyDeployer\SiteIdSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\NetlifyDeployer\SiteIdSetting',
                'getSiteIdSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\S3Deployer\AuthAccessKeyIdSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\S3Deployer\AuthAccessKeyIdSetting',
                'getAuthAccessKeyIdSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\S3Deployer\AuthProfileSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\S3Deployer\AuthProfileSetting',
                'getAuthProfileSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\S3Deployer\AuthSecretAccessKeySetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\S3Deployer\AuthSecretAccessKeySetting',
                'getAuthSecretAccessKeySettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\S3Deployer\AuthSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\S3Deployer\AuthSetting',
                'getAuthSetting3Service',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontDistributionIdSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontDistributionIdSetting',
                'getCloudFrontDistributionIdSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontInvalidateEverythingPath' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontInvalidateEverythingPath',
                'getCloudFrontInvalidateEverythingPathService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontMaxInvalidationPathsSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontMaxInvalidationPathsSetting',
                'getCloudFrontMaxInvalidationPathsSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontSetting',
                'getCloudFrontSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\S3Deployer\EndpointSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\S3Deployer\EndpointSetting',
                'getEndpointSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\S3Deployer\RetainPathsSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\S3Deployer\RetainPathsSetting',
                'getRetainPathsSetting3Service',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\S3Deployer\S3BucketSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\S3Deployer\S3BucketSetting',
                'getS3BucketSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\S3Deployer\S3ObjectAcl' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\S3Deployer\S3ObjectAcl',
                'getS3ObjectAclService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\S3Deployer\S3PrefixSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\S3Deployer\S3PrefixSetting',
                'getS3PrefixSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\S3Deployer\S3RegionSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\S3Deployer\S3RegionSetting',
                'getS3RegionSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\S3Deployer\S3Setting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\S3Deployer\S3Setting',
                'getS3SettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\SftpDeployer\AuthSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\SftpDeployer\AuthSetting',
                'getAuthSetting4Service',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\SftpDeployer\HostSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\SftpDeployer\HostSetting',
                'getHostSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\SftpDeployer\NetworkSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\SftpDeployer\NetworkSetting',
                'getNetworkSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\SftpDeployer\PasswordSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\SftpDeployer\PasswordSetting',
                'getPasswordSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\SftpDeployer\PortSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\SftpDeployer\PortSetting',
                'getPortSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\SftpDeployer\SftpSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\SftpDeployer\SftpSetting',
                'getSftpSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeyPasswordSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeyPasswordSetting',
                'getSshKeyPasswordSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeySetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeySetting',
                'getSshKeySettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\SftpDeployer\TargetDirectorySetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\SftpDeployer\TargetDirectorySetting',
                'getTargetDirectorySetting2Service',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\SftpDeployer\TimeoutSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\SftpDeployer\TimeoutSetting',
                'getTimeoutSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\SftpDeployer\UsernameSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\SftpDeployer\UsernameSetting',
                'getUsernameSettingService',
                \false
            ],
            'Staatic\WordPress\Module\Deployer\ZipfileDeployer\ZipfileSetting' => [
                'privates',
                'Staatic\WordPress\Module\Deployer\ZipfileDeployer\ZipfileSetting',
                'getZipfileSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\BackgroundProcessTimeout' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\BackgroundProcessTimeout',
                'getBackgroundProcessTimeoutService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\CrawlerDomParserSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\CrawlerDomParserSetting',
                'getCrawlerDomParserSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\CrawlerLowercaseUrlsSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\CrawlerLowercaseUrlsSetting',
                'getCrawlerLowercaseUrlsSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\CrawlerProcessNotFoundSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\CrawlerProcessNotFoundSetting',
                'getCrawlerProcessNotFoundSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\CrawlerSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\CrawlerSetting',
                'getCrawlerSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\HttpAuthenticationPasswordSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\HttpAuthenticationPasswordSetting',
                'getHttpAuthenticationPasswordSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\HttpAuthenticationSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\HttpAuthenticationSetting',
                'getHttpAuthenticationSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\HttpAuthenticationUsernameSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\HttpAuthenticationUsernameSetting',
                'getHttpAuthenticationUsernameSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\HttpConcurrencySetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\HttpConcurrencySetting',
                'getHttpConcurrencySettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\HttpDelaySetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\HttpDelaySetting',
                'getHttpDelaySettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\HttpNetworkSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\HttpNetworkSetting',
                'getHttpNetworkSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\HttpTimeoutSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\HttpTimeoutSetting',
                'getHttpTimeoutSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\HttpToHttpsSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\HttpToHttpsSetting',
                'getHttpToHttpsSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\LoggingLevelSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\LoggingLevelSetting',
                'getLoggingLevelSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\OverrideSiteUrlSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\OverrideSiteUrlSetting',
                'getOverrideSiteUrlSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\PageNotFoundPathSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\PageNotFoundPathSetting',
                'getPageNotFoundPathSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\SslVerifyBehaviorSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\SslVerifyBehaviorSetting',
                'getSslVerifyBehaviorSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\SslVerifyPathSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\SslVerifyPathSetting',
                'getSslVerifyPathSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\SslVerifySetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\SslVerifySetting',
                'getSslVerifySettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\UninstallDataSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\UninstallDataSetting',
                'getUninstallDataSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\UninstallSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\UninstallSetting',
                'getUninstallSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\UninstallSettingsSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\UninstallSettingsSetting',
                'getUninstallSettingsSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Advanced\WorkDirectorySetting' => [
                'privates',
                'Staatic\WordPress\Setting\Advanced\WorkDirectorySetting',
                'getWorkDirectorySettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Build\AdditionalPathsSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Build\AdditionalPathsSetting',
                'getAdditionalPathsSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Build\AdditionalRedirectsSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Build\AdditionalRedirectsSetting',
                'getAdditionalRedirectsSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Build\AdditionalUrlsSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Build\AdditionalUrlsSetting',
                'getAdditionalUrlsSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Build\DestinationUrlSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Build\DestinationUrlSetting',
                'getDestinationUrlSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Build\ExcludeUrlsSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Build\ExcludeUrlsSetting',
                'getExcludeUrlsSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Build\PreviewUrlSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Build\PreviewUrlSetting',
                'getPreviewUrlSettingService',
                \false
            ],
            'Staatic\WordPress\Setting\Deployment\DeploymentMethodSetting' => [
                'privates',
                'Staatic\WordPress\Setting\Deployment\DeploymentMethodSetting',
                'getDeploymentMethodSettingService',
                \false
            ]
        ], [
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\ApacheConfigurationFileSetting' => 'Staatic\WordPress\Module\Deployer\FilesystemDeployer\ApacheConfigurationFileSetting',
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\ConfigurationFilesSetting' => 'Staatic\WordPress\Module\Deployer\FilesystemDeployer\ConfigurationFilesSetting',
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\NginxConfigurationFileSetting' => 'Staatic\WordPress\Module\Deployer\FilesystemDeployer\NginxConfigurationFileSetting',
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\RetainPathsSetting' => 'Staatic\WordPress\Module\Deployer\FilesystemDeployer\RetainPathsSetting',
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\SymlinkUploadsDirectorySetting' => 'Staatic\WordPress\Module\Deployer\FilesystemDeployer\SymlinkUploadsDirectorySetting',
            'Staatic\WordPress\Module\Deployer\FilesystemDeployer\TargetDirectorySetting' => 'Staatic\WordPress\Module\Deployer\FilesystemDeployer\TargetDirectorySetting',
            'Staatic\WordPress\Module\Deployer\GithubDeployer\AuthSetting' => 'Staatic\WordPress\Module\Deployer\GithubDeployer\AuthSetting',
            'Staatic\WordPress\Module\Deployer\GithubDeployer\BranchSetting' => 'Staatic\WordPress\Module\Deployer\GithubDeployer\BranchSetting',
            'Staatic\WordPress\Module\Deployer\GithubDeployer\CommitMessageSetting' => 'Staatic\WordPress\Module\Deployer\GithubDeployer\CommitMessageSetting',
            'Staatic\WordPress\Module\Deployer\GithubDeployer\GitSetting' => 'Staatic\WordPress\Module\Deployer\GithubDeployer\GitSetting',
            'Staatic\WordPress\Module\Deployer\GithubDeployer\PrefixSetting' => 'Staatic\WordPress\Module\Deployer\GithubDeployer\PrefixSetting',
            'Staatic\WordPress\Module\Deployer\GithubDeployer\RepositorySetting' => 'Staatic\WordPress\Module\Deployer\GithubDeployer\RepositorySetting',
            'Staatic\WordPress\Module\Deployer\GithubDeployer\RetainPathsSetting' => 'Staatic\WordPress\Module\Deployer\GithubDeployer\RetainPathsSetting',
            'Staatic\WordPress\Module\Deployer\GithubDeployer\TokenSetting' => 'Staatic\WordPress\Module\Deployer\GithubDeployer\TokenSetting',
            'Staatic\WordPress\Module\Deployer\NetlifyDeployer\AccessTokenSetting' => 'Staatic\WordPress\Module\Deployer\NetlifyDeployer\AccessTokenSetting',
            'Staatic\WordPress\Module\Deployer\NetlifyDeployer\AuthSetting' => 'Staatic\WordPress\Module\Deployer\NetlifyDeployer\AuthSetting',
            'Staatic\WordPress\Module\Deployer\NetlifyDeployer\SiteIdSetting' => 'Staatic\WordPress\Module\Deployer\NetlifyDeployer\SiteIdSetting',
            'Staatic\WordPress\Module\Deployer\S3Deployer\AuthAccessKeyIdSetting' => 'Staatic\WordPress\Module\Deployer\S3Deployer\AuthAccessKeyIdSetting',
            'Staatic\WordPress\Module\Deployer\S3Deployer\AuthProfileSetting' => 'Staatic\WordPress\Module\Deployer\S3Deployer\AuthProfileSetting',
            'Staatic\WordPress\Module\Deployer\S3Deployer\AuthSecretAccessKeySetting' => 'Staatic\WordPress\Module\Deployer\S3Deployer\AuthSecretAccessKeySetting',
            'Staatic\WordPress\Module\Deployer\S3Deployer\AuthSetting' => 'Staatic\WordPress\Module\Deployer\S3Deployer\AuthSetting',
            'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontDistributionIdSetting' => 'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontDistributionIdSetting',
            'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontInvalidateEverythingPath' => 'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontInvalidateEverythingPath',
            'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontMaxInvalidationPathsSetting' => 'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontMaxInvalidationPathsSetting',
            'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontSetting' => 'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontSetting',
            'Staatic\WordPress\Module\Deployer\S3Deployer\EndpointSetting' => 'Staatic\WordPress\Module\Deployer\S3Deployer\EndpointSetting',
            'Staatic\WordPress\Module\Deployer\S3Deployer\RetainPathsSetting' => 'Staatic\WordPress\Module\Deployer\S3Deployer\RetainPathsSetting',
            'Staatic\WordPress\Module\Deployer\S3Deployer\S3BucketSetting' => 'Staatic\WordPress\Module\Deployer\S3Deployer\S3BucketSetting',
            'Staatic\WordPress\Module\Deployer\S3Deployer\S3ObjectAcl' => 'Staatic\WordPress\Module\Deployer\S3Deployer\S3ObjectAcl',
            'Staatic\WordPress\Module\Deployer\S3Deployer\S3PrefixSetting' => 'Staatic\WordPress\Module\Deployer\S3Deployer\S3PrefixSetting',
            'Staatic\WordPress\Module\Deployer\S3Deployer\S3RegionSetting' => 'Staatic\WordPress\Module\Deployer\S3Deployer\S3RegionSetting',
            'Staatic\WordPress\Module\Deployer\S3Deployer\S3Setting' => 'Staatic\WordPress\Module\Deployer\S3Deployer\S3Setting',
            'Staatic\WordPress\Module\Deployer\SftpDeployer\AuthSetting' => 'Staatic\WordPress\Module\Deployer\SftpDeployer\AuthSetting',
            'Staatic\WordPress\Module\Deployer\SftpDeployer\HostSetting' => 'Staatic\WordPress\Module\Deployer\SftpDeployer\HostSetting',
            'Staatic\WordPress\Module\Deployer\SftpDeployer\NetworkSetting' => 'Staatic\WordPress\Module\Deployer\SftpDeployer\NetworkSetting',
            'Staatic\WordPress\Module\Deployer\SftpDeployer\PasswordSetting' => 'Staatic\WordPress\Module\Deployer\SftpDeployer\PasswordSetting',
            'Staatic\WordPress\Module\Deployer\SftpDeployer\PortSetting' => 'Staatic\WordPress\Module\Deployer\SftpDeployer\PortSetting',
            'Staatic\WordPress\Module\Deployer\SftpDeployer\SftpSetting' => 'Staatic\WordPress\Module\Deployer\SftpDeployer\SftpSetting',
            'Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeyPasswordSetting' => 'Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeyPasswordSetting',
            'Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeySetting' => 'Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeySetting',
            'Staatic\WordPress\Module\Deployer\SftpDeployer\TargetDirectorySetting' => 'Staatic\WordPress\Module\Deployer\SftpDeployer\TargetDirectorySetting',
            'Staatic\WordPress\Module\Deployer\SftpDeployer\TimeoutSetting' => 'Staatic\WordPress\Module\Deployer\SftpDeployer\TimeoutSetting',
            'Staatic\WordPress\Module\Deployer\SftpDeployer\UsernameSetting' => 'Staatic\WordPress\Module\Deployer\SftpDeployer\UsernameSetting',
            'Staatic\WordPress\Module\Deployer\ZipfileDeployer\ZipfileSetting' => 'Staatic\WordPress\Module\Deployer\ZipfileDeployer\ZipfileSetting',
            'Staatic\WordPress\Setting\Advanced\BackgroundProcessTimeout' => 'Staatic\WordPress\Setting\Advanced\BackgroundProcessTimeout',
            'Staatic\WordPress\Setting\Advanced\CrawlerDomParserSetting' => 'Staatic\WordPress\Setting\Advanced\CrawlerDomParserSetting',
            'Staatic\WordPress\Setting\Advanced\CrawlerLowercaseUrlsSetting' => 'Staatic\WordPress\Setting\Advanced\CrawlerLowercaseUrlsSetting',
            'Staatic\WordPress\Setting\Advanced\CrawlerProcessNotFoundSetting' => 'Staatic\WordPress\Setting\Advanced\CrawlerProcessNotFoundSetting',
            'Staatic\WordPress\Setting\Advanced\CrawlerSetting' => 'Staatic\WordPress\Setting\Advanced\CrawlerSetting',
            'Staatic\WordPress\Setting\Advanced\HttpAuthenticationPasswordSetting' => 'Staatic\WordPress\Setting\Advanced\HttpAuthenticationPasswordSetting',
            'Staatic\WordPress\Setting\Advanced\HttpAuthenticationSetting' => 'Staatic\WordPress\Setting\Advanced\HttpAuthenticationSetting',
            'Staatic\WordPress\Setting\Advanced\HttpAuthenticationUsernameSetting' => 'Staatic\WordPress\Setting\Advanced\HttpAuthenticationUsernameSetting',
            'Staatic\WordPress\Setting\Advanced\HttpConcurrencySetting' => 'Staatic\WordPress\Setting\Advanced\HttpConcurrencySetting',
            'Staatic\WordPress\Setting\Advanced\HttpDelaySetting' => 'Staatic\WordPress\Setting\Advanced\HttpDelaySetting',
            'Staatic\WordPress\Setting\Advanced\HttpNetworkSetting' => 'Staatic\WordPress\Setting\Advanced\HttpNetworkSetting',
            'Staatic\WordPress\Setting\Advanced\HttpTimeoutSetting' => 'Staatic\WordPress\Setting\Advanced\HttpTimeoutSetting',
            'Staatic\WordPress\Setting\Advanced\HttpToHttpsSetting' => 'Staatic\WordPress\Setting\Advanced\HttpToHttpsSetting',
            'Staatic\WordPress\Setting\Advanced\LoggingLevelSetting' => 'Staatic\WordPress\Setting\Advanced\LoggingLevelSetting',
            'Staatic\WordPress\Setting\Advanced\OverrideSiteUrlSetting' => 'Staatic\WordPress\Setting\Advanced\OverrideSiteUrlSetting',
            'Staatic\WordPress\Setting\Advanced\PageNotFoundPathSetting' => 'Staatic\WordPress\Setting\Advanced\PageNotFoundPathSetting',
            'Staatic\WordPress\Setting\Advanced\SslVerifyBehaviorSetting' => 'Staatic\WordPress\Setting\Advanced\SslVerifyBehaviorSetting',
            'Staatic\WordPress\Setting\Advanced\SslVerifyPathSetting' => 'Staatic\WordPress\Setting\Advanced\SslVerifyPathSetting',
            'Staatic\WordPress\Setting\Advanced\SslVerifySetting' => 'Staatic\WordPress\Setting\Advanced\SslVerifySetting',
            'Staatic\WordPress\Setting\Advanced\UninstallDataSetting' => 'Staatic\WordPress\Setting\Advanced\UninstallDataSetting',
            'Staatic\WordPress\Setting\Advanced\UninstallSetting' => 'Staatic\WordPress\Setting\Advanced\UninstallSetting',
            'Staatic\WordPress\Setting\Advanced\UninstallSettingsSetting' => 'Staatic\WordPress\Setting\Advanced\UninstallSettingsSetting',
            'Staatic\WordPress\Setting\Advanced\WorkDirectorySetting' => 'Staatic\WordPress\Setting\Advanced\WorkDirectorySetting',
            'Staatic\WordPress\Setting\Build\AdditionalPathsSetting' => 'Staatic\WordPress\Setting\Build\AdditionalPathsSetting',
            'Staatic\WordPress\Setting\Build\AdditionalRedirectsSetting' => 'Staatic\WordPress\Setting\Build\AdditionalRedirectsSetting',
            'Staatic\WordPress\Setting\Build\AdditionalUrlsSetting' => 'Staatic\WordPress\Setting\Build\AdditionalUrlsSetting',
            'Staatic\WordPress\Setting\Build\DestinationUrlSetting' => 'Staatic\WordPress\Setting\Build\DestinationUrlSetting',
            'Staatic\WordPress\Setting\Build\ExcludeUrlsSetting' => 'Staatic\WordPress\Setting\Build\ExcludeUrlsSetting',
            'Staatic\WordPress\Setting\Build\PreviewUrlSetting' => 'Staatic\WordPress\Setting\Build\PreviewUrlSetting',
            'Staatic\WordPress\Setting\Deployment\DeploymentMethodSetting' => 'Staatic\WordPress\Setting\Deployment\DeploymentMethodSetting'
        ]);
    }

    /**
     * Gets the private 'Staatic\WordPress\Bridge\ResultRepository' shared autowired service.
     *
     * @return ResultRepository
     */
    protected function getResultRepositoryService()
    {
        return $this->privates['Staatic\WordPress\Bridge\ResultRepository'] = new ResultRepository(
            $this->privates['wpdb'] ?? $this->getWpdbService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Factory\StaticDeployerFactory' shared autowired service.
     *
     * @return StaticDeployerFactory
     */
    protected function getStaticDeployerFactoryService()
    {
        return $this->privates['Staatic\WordPress\Factory\StaticDeployerFactory'] = new StaticDeployerFactory(
            $this->services['staatic.logger'] ?? $this->getStaatic_LoggerService(),
            $this->services['staatic.deployment_repository'] ?? $this->getStaatic_DeploymentRepositoryService(),
            $this->services['staatic.result_repository'] ?? $this->getStaatic_ResultRepositoryService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Factory\StaticGeneratorFactory' shared autowired service.
     *
     * @return StaticGeneratorFactory
     */
    protected function getStaticGeneratorFactoryService()
    {
        return $this->privates['Staatic\WordPress\Factory\StaticGeneratorFactory'] = new StaticGeneratorFactory(
            $this->services['staatic.logger'] ?? $this->getStaatic_LoggerService(),
            $this->services['staatic.http_client_factory'] ?? $this->getStaatic_HttpClientFactoryService(),
            new CrawlProfileFactory(
            $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] = $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] ?? new SiteUrlProvider(),
            $this->privates['Staatic\WordPress\Setting\Build\ExcludeUrlsSetting'] ?? $this->getExcludeUrlsSettingService()
        ),
            $this->services['staatic.crawl_queue'] ?? $this->getStaatic_CrawlQueueService(),
            new KnownUrlsContainerFactory(
            $this->privates['wpdb'] ?? $this->getWpdbService()
        ),
            $this->services['staatic.build_repository'] ?? $this->getStaatic_BuildRepositoryService(),
            $this->services['staatic.result_repository'] ?? $this->getStaatic_ResultRepositoryService(),
            $this->services['staatic.resource_repository'] ?? $this->getStaatic_ResourceRepositoryService(),
            $this->privates['Staatic\WordPress\Factory\UrlTransformerFactory'] ?? $this->getUrlTransformerFactoryService(),
            new HtmlUrlExtractorMapping()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Factory\UrlTransformerFactory' shared autowired service.
     *
     * @return UrlTransformerFactory
     */
    protected function getUrlTransformerFactoryService()
    {
        return $this->privates['Staatic\WordPress\Factory\UrlTransformerFactory'] = new UrlTransformerFactory(
            $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] = $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] ?? new SiteUrlProvider(),
            $this->privates['Staatic\WordPress\Setting\Build\DestinationUrlSetting'] = $this->privates['Staatic\WordPress\Setting\Build\DestinationUrlSetting'] ?? new DestinationUrlSetting()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Migrations\MigrationCoordinatorFactory' shared autowired service.
     *
     * @return MigrationCoordinatorFactory
     */
    protected function getMigrationCoordinatorFactoryService()
    {
        return $this->privates['Staatic\WordPress\Migrations\MigrationCoordinatorFactory'] = new MigrationCoordinatorFactory(
            $this->privates['wpdb'] ?? $this->getWpdbService(),
            '1.10.4'
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Admin\ExtendSiteHealth' shared autowired service.
     *
     * @return ExtendSiteHealth
     */
    protected function getExtendSiteHealthService()
    {
        return $this->privates['Staatic\WordPress\Module\Admin\ExtendSiteHealth'] = new ExtendSiteHealth(
            $this->privates['Staatic\WordPress\Service\Formatter'] = $this->privates['Staatic\WordPress\Service\Formatter'] ?? new Formatter(),
            $this->privates['Staatic\WordPress\Service\HealthChecks'] ?? $this->getHealthChecksService(),
            $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] = $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] ?? new SiteUrlProvider()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Admin\Page\BuildResultPage' shared autowired service.
     *
     * @return BuildResultPage
     */
    protected function getBuildResultPageService()
    {
        return $this->privates['Staatic\WordPress\Module\Admin\Page\BuildResultPage'] = new BuildResultPage(
            $this->services['staatic.result_repository'] ?? $this->getStaatic_ResultRepositoryService(),
            $this->services['staatic.resource_repository'] ?? $this->getStaatic_ResourceRepositoryService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsExportPage' shared autowired service.
     *
     * @return PublicationLogsExportPage
     */
    protected function getPublicationLogsExportPageService()
    {
        $a = $this->services['staatic.publication_repository'] ?? $this->getStaatic_PublicationRepositoryService();

        return $this->privates['Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsExportPage'] = new PublicationLogsExportPage(
            $this->services['staatic.admin_navigation'] = $this->services['staatic.admin_navigation'] ?? new AdminNavigation(),
            $a,
            new PublicationLogsExporter(
            $a,
            $this->services['staatic.log_entry_repository'] ?? $this->getStaatic_LogEntryRepositoryService()
        )
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsPage' shared autowired service.
     *
     * @return PublicationLogsPage
     */
    protected function getPublicationLogsPageService()
    {
        return $this->privates['Staatic\WordPress\Module\Admin\Page\PublicationLogs\PublicationLogsPage'] = new PublicationLogsPage(
            $this->services['staatic.admin_navigation'] = $this->services['staatic.admin_navigation'] ?? new AdminNavigation(),
            $this->privates['Staatic\WordPress\Service\PartialRenderer'] ?? $this->getPartialRendererService(),
            $this->services['staatic.publication_repository'] ?? $this->getStaatic_PublicationRepositoryService(),
            new PublicationLogsTable(
            $this->privates['Staatic\WordPress\Service\Formatter'] = $this->privates['Staatic\WordPress\Service\Formatter'] ?? new Formatter(),
            $this->services['staatic.log_entry_repository'] ?? $this->getStaatic_LogEntryRepositoryService()
        )
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Admin\Page\PublicationResults\PublicationResultsPage' shared autowired service.
     *
     * @return PublicationResultsPage
     */
    protected function getPublicationResultsPageService()
    {
        return $this->privates['Staatic\WordPress\Module\Admin\Page\PublicationResults\PublicationResultsPage'] = new PublicationResultsPage(
            $this->services['staatic.admin_navigation'] = $this->services['staatic.admin_navigation'] ?? new AdminNavigation(),
            $this->privates['Staatic\WordPress\Service\PartialRenderer'] ?? $this->getPartialRendererService(),
            $this->services['staatic.publication_repository'] ?? $this->getStaatic_PublicationRepositoryService(),
            new PublicationResultsTable(
            $this->privates['Staatic\WordPress\Service\Formatter'] = $this->privates['Staatic\WordPress\Service\Formatter'] ?? new Formatter(),
            $this->privates['Staatic\WordPress\Bridge\ResultRepository'] ?? $this->getResultRepositoryService()
        )
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Admin\Page\Publications\PublicationDeletePage' shared autowired service.
     *
     * @return PublicationDeletePage
     */
    protected function getPublicationDeletePageService()
    {
        return $this->privates['Staatic\WordPress\Module\Admin\Page\Publications\PublicationDeletePage'] = new PublicationDeletePage(
            $this->services['staatic.admin_navigation'] = $this->services['staatic.admin_navigation'] ?? new AdminNavigation(),
            $this->services['staatic.publication_repository'] ?? $this->getStaatic_PublicationRepositoryService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Admin\Page\Publications\PublicationDownloadPage' shared autowired service.
     *
     * @return PublicationDownloadPage
     */
    protected function getPublicationDownloadPageService()
    {
        $a = $this->services['staatic.publication_repository'] ?? $this->getStaatic_PublicationRepositoryService();

        return $this->privates['Staatic\WordPress\Module\Admin\Page\Publications\PublicationDownloadPage'] = new PublicationDownloadPage(
            $this->services['staatic.admin_navigation'] = $this->services['staatic.admin_navigation'] ?? new AdminNavigation(),
            $a,
            new PublicationArchiver(
            $a,
            $this->privates['Staatic\WordPress\Bridge\ResultRepository'] ?? $this->getResultRepositoryService(),
            $this->services['staatic.resource_repository'] ?? $this->getStaatic_ResourceRepositoryService()
        )
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Admin\Page\Publications\PublicationSummaryPage' shared autowired service.
     *
     * @return PublicationSummaryPage
     */
    protected function getPublicationSummaryPageService()
    {
        return $this->privates['Staatic\WordPress\Module\Admin\Page\Publications\PublicationSummaryPage'] = new PublicationSummaryPage(
            $this->services['staatic.admin_navigation'] = $this->services['staatic.admin_navigation'] ?? new AdminNavigation(),
            $this->privates['Staatic\WordPress\Service\PartialRenderer'] ?? $this->getPartialRendererService(),
            $this->services['staatic.publication_repository'] ?? $this->getStaatic_PublicationRepositoryService(),
            $this->privates['Staatic\WordPress\Bridge\ResultRepository'] ?? $this->getResultRepositoryService(),
            $this->services['staatic.log_entry_repository'] ?? $this->getStaatic_LogEntryRepositoryService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Admin\Page\Publications\PublicationsPage' shared autowired service.
     *
     * @return PublicationsPage
     */
    protected function getPublicationsPageService()
    {
        $a = $this->privates['Staatic\WordPress\Service\Formatter'] = $this->privates['Staatic\WordPress\Service\Formatter'] ?? new Formatter();

        return $this->privates['Staatic\WordPress\Module\Admin\Page\Publications\PublicationsPage'] = new PublicationsPage(
            $this->services['staatic.admin_navigation'] = $this->services['staatic.admin_navigation'] ?? new AdminNavigation(),
            $this->privates['Staatic\WordPress\Service\PartialRenderer'] ?? $this->getPartialRendererService(),
            new PublicationsTable(
            $a,
            new ColumnFactory(
            $a
        ),
            $this->services['staatic.publication_repository'] ?? $this->getStaatic_PublicationRepositoryService(),
            $this->services['staatic.publication_task_provider'] ?? $this->getStaatic_PublicationTaskProviderService()
        )
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Admin\Page\PublishPage' shared autowired service.
     *
     * @return PublishPage
     */
    protected function getPublishPageService()
    {
        return $this->privates['Staatic\WordPress\Module\Admin\Page\PublishPage'] = new PublishPage(
            $this->services['staatic.admin_navigation'] = $this->services['staatic.admin_navigation'] ?? new AdminNavigation(),
            $this->privates['Staatic\WordPress\Service\PartialRenderer'] ?? $this->getPartialRendererService(),
            $this->services['staatic.publication_repository'] ?? $this->getStaatic_PublicationRepositoryService(),
            $this->services['staatic.publication_manager'] ?? $this->getStaatic_PublicationManagerService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Admin\Page\PublishSubsetPage' shared autowired service.
     *
     * @return PublishSubsetPage
     */
    protected function getPublishSubsetPageService()
    {
        return $this->privates['Staatic\WordPress\Module\Admin\Page\PublishSubsetPage'] = new PublishSubsetPage(
            $this->services['staatic.admin_navigation'] = $this->services['staatic.admin_navigation'] ?? new AdminNavigation(),
            $this->privates['Staatic\WordPress\Service\PartialRenderer'] ?? $this->getPartialRendererService(),
            $this->services['staatic.publication_manager'] ?? $this->getStaatic_PublicationManagerService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Admin\Page\SettingsPage' shared autowired service.
     *
     * @return SettingsPage
     */
    protected function getSettingsPageService()
    {
        return $this->privates['Staatic\WordPress\Module\Admin\Page\SettingsPage'] = new SettingsPage(
            $this->services['staatic.admin_navigation'] = $this->services['staatic.admin_navigation'] ?? new AdminNavigation(),
            $this->privates['Staatic\WordPress\Service\PartialRenderer'] ?? $this->getPartialRendererService(),
            $this->services['staatic.settings'] ?? $this->getStaatic_SettingsService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Admin\Page\TestRequestPage' shared autowired service.
     *
     * @return TestRequestPage
     */
    protected function getTestRequestPageService()
    {
        return $this->privates['Staatic\WordPress\Module\Admin\Page\TestRequestPage'] = new TestRequestPage(
            $this->services['staatic.admin_navigation'] = $this->services['staatic.admin_navigation'] ?? new AdminNavigation(),
            $this->privates['Staatic\WordPress\Service\PartialRenderer'] ?? $this->getPartialRendererService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Admin\RegisterAdminBar' shared autowired service.
     *
     * @return RegisterAdminBar
     */
    protected function getRegisterAdminBarService()
    {
        return $this->privates['Staatic\WordPress\Module\Admin\RegisterAdminBar'] = new RegisterAdminBar(
            $this->privates['Staatic\WordPress\Service\Formatter'] = $this->privates['Staatic\WordPress\Service\Formatter'] ?? new Formatter(),
            $this->services['staatic.publication_repository'] ?? $this->getStaatic_PublicationRepositoryService(),
            $this->privates['Staatic\WordPress\Factory\UrlTransformerFactory'] ?? $this->getUrlTransformerFactoryService(),
            $this->privates['Staatic\WordPress\Setting\Build\DestinationUrlSetting'] = $this->privates['Staatic\WordPress\Setting\Build\DestinationUrlSetting'] ?? new DestinationUrlSetting()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Admin\RegisterNavigation' shared autowired service.
     *
     * @return RegisterNavigation
     */
    protected function getRegisterNavigationService()
    {
        return $this->privates['Staatic\WordPress\Module\Admin\RegisterNavigation'] = new RegisterNavigation(
            $this->services['staatic.admin_navigation'] = $this->services['staatic.admin_navigation'] ?? new AdminNavigation()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Admin\Widget\PublicationLogsWidget' shared autowired service.
     *
     * @return PublicationLogsWidget
     */
    protected function getPublicationLogsWidgetService()
    {
        return $this->privates['Staatic\WordPress\Module\Admin\Widget\PublicationLogsWidget'] = new PublicationLogsWidget(
            $this->privates['Staatic\WordPress\Service\PartialRenderer'] ?? $this->getPartialRendererService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Admin\Widget\PublicationStatusWidget' shared autowired service.
     *
     * @return PublicationStatusWidget
     */
    protected function getPublicationStatusWidgetService()
    {
        return $this->privates['Staatic\WordPress\Module\Admin\Widget\PublicationStatusWidget'] = new PublicationStatusWidget(
            $this->privates['Staatic\WordPress\Service\PartialRenderer'] ?? $this->getPartialRendererService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Cleanup' shared autowired service.
     *
     * @return Cleanup
     */
    protected function getCleanupService()
    {
        $a = $this->services['staatic.logger'] ?? $this->getStaatic_LoggerService();

        return $this->privates['Staatic\WordPress\Module\Cleanup'] = new Cleanup(
            $this->services['staatic.scheduler'] = $this->services['staatic.scheduler'] ?? new Scheduler(),
            new LogEntryCleanup(
            $this->services['staatic.log_entry_repository'] ?? $this->getStaatic_LogEntryRepositoryService()
        ),
            new PublicationCleanup(
            $a,
            $this->services['staatic.publication_repository'] ?? $this->getStaatic_PublicationRepositoryService()
        ),
            new ResourceCleanup(
            $this->privates['Staatic\WordPress\Bridge\ResultRepository'] ?? $this->getResultRepositoryService(),
            $this->privates['Staatic\WordPress\Setting\Advanced\WorkDirectorySetting'] = $this->privates['Staatic\WordPress\Setting\Advanced\WorkDirectorySetting'] ?? new WorkDirectorySetting(),
            $a
        )
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Cli\RegisterCommands' shared autowired service.
     *
     * @return RegisterCommands
     */
    protected function getRegisterCommandsService()
    {
        $a = $this->services['staatic.logger'] ?? $this->getStaatic_LoggerService();
        $b = $this->privates['Staatic\WordPress\Service\Formatter'] = $this->privates['Staatic\WordPress\Service\Formatter'] ?? new Formatter();
        $c = $this->services['staatic.publication_repository'] ?? $this->getStaatic_PublicationRepositoryService();
        $d = $this->services['staatic.publication_manager'] ?? $this->getStaatic_PublicationManagerService();
        $e = $this->services['staatic.publication_task_provider'] ?? $this->getStaatic_PublicationTaskProviderService();

        return $this->privates['Staatic\WordPress\Module\Cli\RegisterCommands'] = new RegisterCommands(
            new MigrateCommand(
            $a,
            $b,
            $this->privates['Staatic\WordPress\Migrations\MigrationCoordinatorFactory'] ?? $this->getMigrationCoordinatorFactoryService()
        ),
            new PublishCommand(
            $a,
            $b,
            $c,
            $d,
            $e
        ),
            new RedeployCommand(
            $a,
            $b,
            $c,
            $d,
            $e
        )
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\FilesystemDeployer\ApacheConfigurationFileSetting' shared autowired service.
     *
     * @return ApacheConfigurationFileSetting
     */
    protected function getApacheConfigurationFileSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\FilesystemDeployer\ApacheConfigurationFileSetting'] = new ApacheConfigurationFileSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\FilesystemDeployer\ConfigurationFilesSetting' shared autowired service.
     *
     * @return ConfigurationFilesSetting
     */
    protected function getConfigurationFilesSettingService()
    {
        $a = $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService();
        if (isset($this->privates['Staatic\WordPress\Module\Deployer\FilesystemDeployer\ConfigurationFilesSetting'])) {
            return $this->privates['Staatic\WordPress\Module\Deployer\FilesystemDeployer\ConfigurationFilesSetting'];
        }

        return $this->privates['Staatic\WordPress\Module\Deployer\FilesystemDeployer\ConfigurationFilesSetting'] = new ConfigurationFilesSetting(
            $a
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\FilesystemDeployer\FilesystemDeployerModule' shared autowired service.
     *
     * @return FilesystemDeployerModule
     */
    protected function getFilesystemDeployerModuleService()
    {
        $a = $this->services['staatic.resource_repository'] ?? $this->getStaatic_ResourceRepositoryService();

        return $this->privates['Staatic\WordPress\Module\Deployer\FilesystemDeployer\FilesystemDeployerModule'] = new FilesystemDeployerModule(
            $this->services['staatic.settings'] ?? $this->getStaatic_SettingsService(),
            $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService(),
            new FilesystemDeployStrategyFactory(
            $a
        ),
            $this->services['staatic.result_repository'] ?? $this->getStaatic_ResultRepositoryService(),
            $a,
            $this->privates['Staatic\WordPress\Factory\UrlTransformerFactory'] ?? $this->getUrlTransformerFactoryService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\FilesystemDeployer\NginxConfigurationFileSetting' shared autowired service.
     *
     * @return NginxConfigurationFileSetting
     */
    protected function getNginxConfigurationFileSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\FilesystemDeployer\NginxConfigurationFileSetting'] = new NginxConfigurationFileSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\FilesystemDeployer\RetainPathsSetting' shared autowired service.
     *
     * @return RetainPathsSetting
     */
    protected function getRetainPathsSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\FilesystemDeployer\RetainPathsSetting'] = new RetainPathsSetting(
            $this->privates['Staatic\WordPress\Module\Deployer\FilesystemDeployer\TargetDirectorySetting'] = $this->privates['Staatic\WordPress\Module\Deployer\FilesystemDeployer\TargetDirectorySetting'] ?? new TargetDirectorySetting()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\FilesystemDeployer\SymlinkUploadsDirectorySetting' shared autowired service.
     *
     * @return SymlinkUploadsDirectorySetting
     */
    protected function getSymlinkUploadsDirectorySettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\FilesystemDeployer\SymlinkUploadsDirectorySetting'] = new SymlinkUploadsDirectorySetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\FilesystemDeployer\TargetDirectorySetting' shared autowired service.
     *
     * @return TargetDirectorySetting
     */
    protected function getTargetDirectorySettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\FilesystemDeployer\TargetDirectorySetting'] = new TargetDirectorySetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\GithubDeployer\AuthSetting' shared autowired service.
     *
     * @return AuthSetting
     */
    protected function getAuthSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\AuthSetting'] = new AuthSetting(
            $this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\TokenSetting'] = $this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\TokenSetting'] ?? new TokenSetting()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\GithubDeployer\BranchSetting' shared autowired service.
     *
     * @return BranchSetting
     */
    protected function getBranchSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\BranchSetting'] = new BranchSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\GithubDeployer\CommitMessageSetting' shared autowired service.
     *
     * @return CommitMessageSetting
     */
    protected function getCommitMessageSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\CommitMessageSetting'] = new CommitMessageSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\GithubDeployer\GitSetting' shared autowired service.
     *
     * @return GitSetting
     */
    protected function getGitSettingService()
    {
        $a = $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService();
        if (isset($this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\GitSetting'])) {
            return $this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\GitSetting'];
        }

        return $this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\GitSetting'] = new GitSetting($a);
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\GithubDeployer\GithubDeployerModule' shared autowired service.
     *
     * @return GithubDeployerModule
     */
    protected function getGithubDeployerModuleService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\GithubDeployerModule'] = new GithubDeployerModule(
            $this->services['staatic.settings'] ?? $this->getStaatic_SettingsService(),
            $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService(),
            new GithubDeployStrategyFactory(
            $this->services['staatic.result_repository'] ?? $this->getStaatic_ResultRepositoryService(),
            $this->services['staatic.resource_repository'] ?? $this->getStaatic_ResourceRepositoryService(),
            $this->services['staatic.http_client_factory'] ?? $this->getStaatic_HttpClientFactoryService()
        )
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\GithubDeployer\GithubStatusEndpoint' shared autowired service.
     *
     * @return GithubStatusEndpoint
     */
    protected function getGithubStatusEndpointService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\GithubStatusEndpoint'] = new GithubStatusEndpoint(
            $this->services['staatic.http_client_factory'] ?? $this->getStaatic_HttpClientFactoryService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\GithubDeployer\PrefixSetting' shared autowired service.
     *
     * @return PrefixSetting
     */
    protected function getPrefixSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\PrefixSetting'] = new PrefixSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\GithubDeployer\RepositorySetting' shared autowired service.
     *
     * @return RepositorySetting
     */
    protected function getRepositorySettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\RepositorySetting'] = new RepositorySetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\GithubDeployer\RetainPathsSetting' shared autowired service.
     *
     * @return \Staatic\WordPress\Module\Deployer\GithubDeployer\RetainPathsSetting
     */
    protected function getRetainPathsSetting2Service()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\RetainPathsSetting'] = new \Staatic\WordPress\Module\Deployer\GithubDeployer\RetainPathsSetting(
            $this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\PrefixSetting'] = $this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\PrefixSetting'] ?? new PrefixSetting()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\GithubDeployer\TokenSetting' shared autowired service.
     *
     * @return TokenSetting
     */
    protected function getTokenSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\GithubDeployer\TokenSetting'] = new TokenSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\NetlifyDeployer\AccessTokenSetting' shared autowired service.
     *
     * @return AccessTokenSetting
     */
    protected function getAccessTokenSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\NetlifyDeployer\AccessTokenSetting'] = new AccessTokenSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\NetlifyDeployer\AuthSetting' shared autowired service.
     *
     * @return \Staatic\WordPress\Module\Deployer\NetlifyDeployer\AuthSetting
     */
    protected function getAuthSetting2Service()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\NetlifyDeployer\AuthSetting'] = new \Staatic\WordPress\Module\Deployer\NetlifyDeployer\AuthSetting(
            $this->privates['Staatic\WordPress\Module\Deployer\NetlifyDeployer\AccessTokenSetting'] = $this->privates['Staatic\WordPress\Module\Deployer\NetlifyDeployer\AccessTokenSetting'] ?? new AccessTokenSetting(),
            $this->privates['Staatic\WordPress\Module\Deployer\NetlifyDeployer\SiteIdSetting'] = $this->privates['Staatic\WordPress\Module\Deployer\NetlifyDeployer\SiteIdSetting'] ?? new SiteIdSetting()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\NetlifyDeployer\NetlifyDeployerModule' shared autowired service.
     *
     * @return NetlifyDeployerModule
     */
    protected function getNetlifyDeployerModuleService()
    {
        $a = $this->services['staatic.result_repository'] ?? $this->getStaatic_ResultRepositoryService();
        $b = $this->services['staatic.resource_repository'] ?? $this->getStaatic_ResourceRepositoryService();

        return $this->privates['Staatic\WordPress\Module\Deployer\NetlifyDeployer\NetlifyDeployerModule'] = new NetlifyDeployerModule(
            $this->services['staatic.settings'] ?? $this->getStaatic_SettingsService(),
            $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService(),
            new NetlifyDeployStrategyFactory(
            $a,
            $b,
            $this->services['staatic.http_client_factory'] ?? $this->getStaatic_HttpClientFactoryService()
        ),
            $a,
            $b,
            $this->privates['Staatic\WordPress\Factory\UrlTransformerFactory'] ?? $this->getUrlTransformerFactoryService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\NetlifyDeployer\NetlifyStatusEndpoint' shared autowired service.
     *
     * @return NetlifyStatusEndpoint
     */
    protected function getNetlifyStatusEndpointService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\NetlifyDeployer\NetlifyStatusEndpoint'] = new NetlifyStatusEndpoint(
            $this->services['staatic.http_client_factory'] ?? $this->getStaatic_HttpClientFactoryService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\NetlifyDeployer\SiteIdSetting' shared autowired service.
     *
     * @return SiteIdSetting
     */
    protected function getSiteIdSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\NetlifyDeployer\SiteIdSetting'] = new SiteIdSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\S3Deployer\AuthAccessKeyIdSetting' shared autowired service.
     *
     * @return AuthAccessKeyIdSetting
     */
    protected function getAuthAccessKeyIdSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\AuthAccessKeyIdSetting'] = new AuthAccessKeyIdSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\S3Deployer\AuthProfileSetting' shared autowired service.
     *
     * @return AuthProfileSetting
     */
    protected function getAuthProfileSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\AuthProfileSetting'] = new AuthProfileSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\S3Deployer\AuthSecretAccessKeySetting' shared autowired service.
     *
     * @return AuthSecretAccessKeySetting
     */
    protected function getAuthSecretAccessKeySettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\AuthSecretAccessKeySetting'] = new AuthSecretAccessKeySetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\S3Deployer\AuthSetting' shared autowired service.
     *
     * @return \Staatic\WordPress\Module\Deployer\S3Deployer\AuthSetting
     */
    protected function getAuthSetting3Service()
    {
        $a = $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService();
        if (isset($this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\AuthSetting'])) {
            return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\AuthSetting'];
        }

        return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\AuthSetting'] = new \Staatic\WordPress\Module\Deployer\S3Deployer\AuthSetting(
            $a
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontDistributionIdSetting' shared autowired service.
     *
     * @return CloudFrontDistributionIdSetting
     */
    protected function getCloudFrontDistributionIdSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontDistributionIdSetting'] = new CloudFrontDistributionIdSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontInvalidateEverythingPath' shared autowired service.
     *
     * @return CloudFrontInvalidateEverythingPath
     */
    protected function getCloudFrontInvalidateEverythingPathService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontInvalidateEverythingPath'] = new CloudFrontInvalidateEverythingPath();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontMaxInvalidationPathsSetting' shared autowired service.
     *
     * @return CloudFrontMaxInvalidationPathsSetting
     */
    protected function getCloudFrontMaxInvalidationPathsSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontMaxInvalidationPathsSetting'] = new CloudFrontMaxInvalidationPathsSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontSetting' shared autowired service.
     *
     * @return CloudFrontSetting
     */
    protected function getCloudFrontSettingService()
    {
        $a = $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService();
        if (isset($this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontSetting'])) {
            return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontSetting'];
        }

        return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\CloudFrontSetting'] = new CloudFrontSetting(
            $a
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\S3Deployer\EndpointSetting' shared autowired service.
     *
     * @return EndpointSetting
     */
    protected function getEndpointSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\EndpointSetting'] = new EndpointSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\S3Deployer\RetainPathsSetting' shared autowired service.
     *
     * @return \Staatic\WordPress\Module\Deployer\S3Deployer\RetainPathsSetting
     */
    protected function getRetainPathsSetting3Service()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\RetainPathsSetting'] = new \Staatic\WordPress\Module\Deployer\S3Deployer\RetainPathsSetting(
            $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\S3PrefixSetting'] = $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\S3PrefixSetting'] ?? new S3PrefixSetting()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\S3Deployer\S3BucketSetting' shared autowired service.
     *
     * @return S3BucketSetting
     */
    protected function getS3BucketSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\S3BucketSetting'] = new S3BucketSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\S3Deployer\S3DeployerModule' shared autowired service.
     *
     * @return S3DeployerModule
     */
    protected function getS3DeployerModuleService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\S3DeployerModule'] = new S3DeployerModule(
            $this->services['staatic.settings'] ?? $this->getStaatic_SettingsService(),
            $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService(),
            new AwsDeployStrategyFactory(
            $this->services['staatic.result_repository'] ?? $this->getStaatic_ResultRepositoryService(),
            $this->services['staatic.resource_repository'] ?? $this->getStaatic_ResourceRepositoryService(),
            $this->services['staatic.http_client_factory'] ?? $this->getStaatic_HttpClientFactoryService()
        )
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\S3Deployer\S3ObjectAcl' shared autowired service.
     *
     * @return S3ObjectAcl
     */
    protected function getS3ObjectAclService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\S3ObjectAcl'] = new S3ObjectAcl();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\S3Deployer\S3PrefixSetting' shared autowired service.
     *
     * @return S3PrefixSetting
     */
    protected function getS3PrefixSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\S3PrefixSetting'] = new S3PrefixSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\S3Deployer\S3RegionSetting' shared autowired service.
     *
     * @return S3RegionSetting
     */
    protected function getS3RegionSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\S3RegionSetting'] = new S3RegionSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\S3Deployer\S3Setting' shared autowired service.
     *
     * @return S3Setting
     */
    protected function getS3SettingService()
    {
        $a = $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService();
        if (isset($this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\S3Setting'])) {
            return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\S3Setting'];
        }

        return $this->privates['Staatic\WordPress\Module\Deployer\S3Deployer\S3Setting'] = new S3Setting($a);
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\SftpDeployer\AuthSetting' shared autowired service.
     *
     * @return \Staatic\WordPress\Module\Deployer\SftpDeployer\AuthSetting
     */
    protected function getAuthSetting4Service()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\AuthSetting'] = new \Staatic\WordPress\Module\Deployer\SftpDeployer\AuthSetting(
            $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\HostSetting'] = $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\HostSetting'] ?? new HostSetting(),
            $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\PortSetting'] = $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\PortSetting'] ?? new PortSetting(),
            $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\UsernameSetting'] = $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\UsernameSetting'] ?? new UsernameSetting(),
            $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\PasswordSetting'] = $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\PasswordSetting'] ?? new PasswordSetting(),
            $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeySetting'] = $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeySetting'] ?? new SshKeySetting(),
            $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeyPasswordSetting'] = $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeyPasswordSetting'] ?? new SshKeyPasswordSetting()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\SftpDeployer\HostSetting' shared autowired service.
     *
     * @return HostSetting
     */
    protected function getHostSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\HostSetting'] = new HostSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\SftpDeployer\NetworkSetting' shared autowired service.
     *
     * @return NetworkSetting
     */
    protected function getNetworkSettingService()
    {
        $a = $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService();
        if (isset($this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\NetworkSetting'])) {
            return $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\NetworkSetting'];
        }

        return $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\NetworkSetting'] = new NetworkSetting(
            $a
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\SftpDeployer\PasswordSetting' shared autowired service.
     *
     * @return PasswordSetting
     */
    protected function getPasswordSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\PasswordSetting'] = new PasswordSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\SftpDeployer\PortSetting' shared autowired service.
     *
     * @return PortSetting
     */
    protected function getPortSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\PortSetting'] = new PortSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\SftpDeployer\SftpDeployerModule' shared autowired service.
     *
     * @return SftpDeployerModule
     */
    protected function getSftpDeployerModuleService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\SftpDeployerModule'] = new SftpDeployerModule(
            $this->services['staatic.settings'] ?? $this->getStaatic_SettingsService(),
            $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService(),
            new SftpDeployStrategyFactory(
            $this->services['staatic.result_repository'] ?? $this->getStaatic_ResultRepositoryService(),
            $this->services['staatic.resource_repository'] ?? $this->getStaatic_ResourceRepositoryService(),
            $this->services['staatic.http_client_factory'] ?? $this->getStaatic_HttpClientFactoryService()
        )
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\SftpDeployer\SftpSetting' shared autowired service.
     *
     * @return SftpSetting
     */
    protected function getSftpSettingService()
    {
        $a = $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService();
        if (isset($this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\SftpSetting'])) {
            return $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\SftpSetting'];
        }

        return $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\SftpSetting'] = new SftpSetting($a);
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeyPasswordSetting' shared autowired service.
     *
     * @return SshKeyPasswordSetting
     */
    protected function getSshKeyPasswordSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeyPasswordSetting'] = new SshKeyPasswordSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeySetting' shared autowired service.
     *
     * @return SshKeySetting
     */
    protected function getSshKeySettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\SshKeySetting'] = new SshKeySetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\SftpDeployer\TargetDirectorySetting' shared autowired service.
     *
     * @return \Staatic\WordPress\Module\Deployer\SftpDeployer\TargetDirectorySetting
     */
    protected function getTargetDirectorySetting2Service()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\TargetDirectorySetting'] = new \Staatic\WordPress\Module\Deployer\SftpDeployer\TargetDirectorySetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\SftpDeployer\TimeoutSetting' shared autowired service.
     *
     * @return TimeoutSetting
     */
    protected function getTimeoutSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\TimeoutSetting'] = new TimeoutSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\SftpDeployer\UsernameSetting' shared autowired service.
     *
     * @return UsernameSetting
     */
    protected function getUsernameSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\SftpDeployer\UsernameSetting'] = new UsernameSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\ZipfileDeployer\ZipfileDeployerModule' shared autowired service.
     *
     * @return ZipfileDeployerModule
     */
    protected function getZipfileDeployerModuleService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\ZipfileDeployer\ZipfileDeployerModule'] = new ZipfileDeployerModule(
            $this->services['staatic.settings'] ?? $this->getStaatic_SettingsService(),
            $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Deployer\ZipfileDeployer\ZipfileSetting' shared autowired service.
     *
     * @return ZipfileSetting
     */
    protected function getZipfileSettingService()
    {
        return $this->privates['Staatic\WordPress\Module\Deployer\ZipfileDeployer\ZipfileSetting'] = new ZipfileSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\EnsureMigrated' shared autowired service.
     *
     * @return EnsureMigrated
     */
    protected function getEnsureMigratedService()
    {
        return $this->privates['Staatic\WordPress\Module\EnsureMigrated'] = new EnsureMigrated(
            $this->privates['Staatic\WordPress\Migrations\MigrationCoordinatorFactory'] ?? $this->getMigrationCoordinatorFactoryService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\HttpAuthHeaders' shared autowired service.
     *
     * @return HttpAuthHeaders
     */
    protected function getHttpAuthHeadersService()
    {
        return $this->privates['Staatic\WordPress\Module\HttpAuthHeaders'] = new HttpAuthHeaders(
            $this->privates['Staatic\WordPress\Setting\Advanced\HttpAuthenticationUsernameSetting'] = $this->privates['Staatic\WordPress\Setting\Advanced\HttpAuthenticationUsernameSetting'] ?? new HttpAuthenticationUsernameSetting(),
            $this->privates['Staatic\WordPress\Setting\Advanced\HttpAuthenticationPasswordSetting'] = $this->privates['Staatic\WordPress\Setting\Advanced\HttpAuthenticationPasswordSetting'] ?? new HttpAuthenticationPasswordSetting()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\HttpsToHttpDowngrade' shared autowired service.
     *
     * @return HttpsToHttpDowngrade
     */
    protected function getHttpsToHttpDowngradeService()
    {
        return $this->privates['Staatic\WordPress\Module\HttpsToHttpDowngrade'] = new HttpsToHttpDowngrade(
            $this->privates['Staatic\WordPress\Setting\Advanced\HttpToHttpsSetting'] = $this->privates['Staatic\WordPress\Setting\Advanced\HttpToHttpsSetting'] ?? new HttpToHttpsSetting()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Integration\ElementorPlugin' shared autowired service.
     *
     * @return ElementorPlugin
     */
    protected function getElementorPluginService()
    {
        return $this->privates['Staatic\WordPress\Module\Integration\ElementorPlugin'] = new ElementorPlugin(
            $this->privates['Staatic\WordPress\Factory\UrlTransformerFactory'] ?? $this->getUrlTransformerFactoryService(),
            $this->privates['Staatic\WordPress\Setting\Build\DestinationUrlSetting'] = $this->privates['Staatic\WordPress\Setting\Build\DestinationUrlSetting'] ?? new DestinationUrlSetting()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\RegisterFieldTypes' shared autowired service.
     *
     * @return RegisterFieldTypes
     */
    protected function getRegisterFieldTypesService()
    {
        return $this->privates['Staatic\WordPress\Module\RegisterFieldTypes'] = new RegisterFieldTypes(
            $this->privates['wpdb'] ?? $this->getWpdbService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\RegisterPublishHook' shared autowired service.
     *
     * @return RegisterPublishHook
     */
    protected function getRegisterPublishHookService()
    {
        return $this->privates['Staatic\WordPress\Module\RegisterPublishHook'] = new RegisterPublishHook(
            $this->services['staatic.publication_manager'] ?? $this->getStaatic_PublicationManagerService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\RegisterSettings' shared autowired service.
     *
     * @return RegisterSettings
     */
    protected function getRegisterSettingsService()
    {
        return $this->privates['Staatic\WordPress\Module\RegisterSettings'] = new RegisterSettings(
            $this->services['staatic.settings'] ?? $this->getStaatic_SettingsService(),
            $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService(),
            $this->privates['Staatic\WordPress\Service\PartialRenderer'] ?? $this->getPartialRendererService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Rest\PublicationLogsEndpoint' shared autowired service.
     *
     * @return PublicationLogsEndpoint
     */
    protected function getPublicationLogsEndpointService()
    {
        return $this->privates['Staatic\WordPress\Module\Rest\PublicationLogsEndpoint'] = new PublicationLogsEndpoint(
            $this->services['staatic.publication_repository'] ?? $this->getStaatic_PublicationRepositoryService(),
            $this->services['staatic.log_entry_repository'] ?? $this->getStaatic_LogEntryRepositoryService(),
            $this->privates['Staatic\WordPress\Service\Formatter'] = $this->privates['Staatic\WordPress\Service\Formatter'] ?? new Formatter()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Rest\PublicationStatusEndpoint' shared autowired service.
     *
     * @return PublicationStatusEndpoint
     */
    protected function getPublicationStatusEndpointService()
    {
        return $this->privates['Staatic\WordPress\Module\Rest\PublicationStatusEndpoint'] = new PublicationStatusEndpoint(
            $this->services['staatic.publication_repository'] ?? $this->getStaatic_PublicationRepositoryService(),
            $this->services['staatic.publication_task_provider'] ?? $this->getStaatic_PublicationTaskProviderService(),
            $this->privates['Staatic\WordPress\Service\Formatter'] = $this->privates['Staatic\WordPress\Service\Formatter'] ?? new Formatter()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\Rest\SiteHealthTestsEndpoint' shared autowired service.
     *
     * @return SiteHealthTestsEndpoint
     */
    protected function getSiteHealthTestsEndpointService()
    {
        return $this->privates['Staatic\WordPress\Module\Rest\SiteHealthTestsEndpoint'] = new SiteHealthTestsEndpoint(
            $this->privates['Staatic\WordPress\Service\HealthChecks'] ?? $this->getHealthChecksService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Module\ScheduleTestRequest' shared autowired service.
     *
     * @return ScheduleTestRequest
     */
    protected function getScheduleTestRequestService()
    {
        return $this->privates['Staatic\WordPress\Module\ScheduleTestRequest'] = new ScheduleTestRequest(
            $this->services['staatic.scheduler'] = $this->services['staatic.scheduler'] ?? new Scheduler()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Publication\Task\CrawlTask' shared autowired service.
     *
     * @return CrawlTask
     */
    protected function getCrawlTaskService()
    {
        return $this->privates['Staatic\WordPress\Publication\Task\CrawlTask'] = new CrawlTask(
            $this->privates['Staatic\WordPress\Factory\StaticGeneratorFactory'] ?? $this->getStaticGeneratorFactoryService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Publication\Task\DeployTask' shared autowired service.
     *
     * @return DeployTask
     */
    protected function getDeployTaskService()
    {
        return $this->privates['Staatic\WordPress\Publication\Task\DeployTask'] = new DeployTask(
            $this->privates['Staatic\WordPress\Factory\StaticDeployerFactory'] ?? $this->getStaticDeployerFactoryService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Publication\Task\FinishCrawlerTask' shared autowired service.
     *
     * @return FinishCrawlerTask
     */
    protected function getFinishCrawlerTaskService()
    {
        return $this->privates['Staatic\WordPress\Publication\Task\FinishCrawlerTask'] = new FinishCrawlerTask(
            $this->privates['Staatic\WordPress\Factory\StaticGeneratorFactory'] ?? $this->getStaticGeneratorFactoryService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Publication\Task\FinishDeploymentTask' shared autowired service.
     *
     * @return FinishDeploymentTask
     */
    protected function getFinishDeploymentTaskService()
    {
        return $this->privates['Staatic\WordPress\Publication\Task\FinishDeploymentTask'] = new FinishDeploymentTask(
            $this->privates['Staatic\WordPress\Factory\StaticDeployerFactory'] ?? $this->getStaticDeployerFactoryService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Publication\Task\FinishTask' shared autowired service.
     *
     * @return FinishTask
     */
    protected function getFinishTaskService()
    {
        return $this->privates['Staatic\WordPress\Publication\Task\FinishTask'] = new FinishTask(
            $this->services['staatic.publication_repository'] ?? $this->getStaatic_PublicationRepositoryService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Publication\Task\InitializeCrawlerTask' shared autowired service.
     *
     * @return InitializeCrawlerTask
     */
    protected function getInitializeCrawlerTaskService()
    {
        return $this->privates['Staatic\WordPress\Publication\Task\InitializeCrawlerTask'] = new InitializeCrawlerTask(
            $this->privates['Staatic\WordPress\Factory\StaticGeneratorFactory'] ?? $this->getStaticGeneratorFactoryService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Publication\Task\InitiateDeploymentTask' shared autowired service.
     *
     * @return InitiateDeploymentTask
     */
    protected function getInitiateDeploymentTaskService()
    {
        return $this->privates['Staatic\WordPress\Publication\Task\InitiateDeploymentTask'] = new InitiateDeploymentTask(
            $this->privates['Staatic\WordPress\Factory\StaticDeployerFactory'] ?? $this->getStaticDeployerFactoryService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Publication\Task\PostProcessTask' shared autowired service.
     *
     * @return PostProcessTask
     */
    protected function getPostProcessTaskService()
    {
        return $this->privates['Staatic\WordPress\Publication\Task\PostProcessTask'] = new PostProcessTask(
            $this->privates['Staatic\WordPress\Factory\StaticGeneratorFactory'] ?? $this->getStaticGeneratorFactoryService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Publication\Task\SetupTask' shared autowired service.
     *
     * @return SetupTask
     */
    protected function getSetupTaskService()
    {
        return $this->privates['Staatic\WordPress\Publication\Task\SetupTask'] = new SetupTask(
            $this->privates['Staatic\WordPress\Setting\Advanced\WorkDirectorySetting'] = $this->privates['Staatic\WordPress\Setting\Advanced\WorkDirectorySetting'] ?? new WorkDirectorySetting(),
            $this->services['staatic.logger'] ?? $this->getStaatic_LoggerService()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Service\HealthChecks' shared autowired service.
     *
     * @return HealthChecks
     */
    protected function getHealthChecksService()
    {
        return $this->privates['Staatic\WordPress\Service\HealthChecks'] = new HealthChecks(
            $this->services['staatic.http_client_factory'] ?? $this->getStaatic_HttpClientFactoryService(),
            $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] = $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] ?? new SiteUrlProvider()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Service\PartialRenderer' shared autowired service.
     *
     * @return PartialRenderer
     */
    protected function getPartialRendererService()
    {
        return $this->privates['Staatic\WordPress\Service\PartialRenderer'] = (new PartialRendererFactory(
            $this->privates['Staatic\WordPress\Service\Formatter'] = $this->privates['Staatic\WordPress\Service\Formatter'] ?? new Formatter()
        ))->__invoke();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\BackgroundProcessTimeout' shared autowired service.
     *
     * @return BackgroundProcessTimeout
     */
    protected function getBackgroundProcessTimeoutService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\BackgroundProcessTimeout'] = new BackgroundProcessTimeout();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\CrawlerDomParserSetting' shared autowired service.
     *
     * @return CrawlerDomParserSetting
     */
    protected function getCrawlerDomParserSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\CrawlerDomParserSetting'] = new CrawlerDomParserSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\CrawlerLowercaseUrlsSetting' shared autowired service.
     *
     * @return CrawlerLowercaseUrlsSetting
     */
    protected function getCrawlerLowercaseUrlsSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\CrawlerLowercaseUrlsSetting'] = new CrawlerLowercaseUrlsSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\CrawlerProcessNotFoundSetting' shared autowired service.
     *
     * @return CrawlerProcessNotFoundSetting
     */
    protected function getCrawlerProcessNotFoundSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\CrawlerProcessNotFoundSetting'] = new CrawlerProcessNotFoundSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\CrawlerSetting' shared autowired service.
     *
     * @return CrawlerSetting
     */
    protected function getCrawlerSettingService()
    {
        $a = $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService();
        if (isset($this->privates['Staatic\WordPress\Setting\Advanced\CrawlerSetting'])) {
            return $this->privates['Staatic\WordPress\Setting\Advanced\CrawlerSetting'];
        }

        return $this->privates['Staatic\WordPress\Setting\Advanced\CrawlerSetting'] = new CrawlerSetting($a);
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\HttpAuthenticationPasswordSetting' shared autowired service.
     *
     * @return HttpAuthenticationPasswordSetting
     */
    protected function getHttpAuthenticationPasswordSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\HttpAuthenticationPasswordSetting'] = new HttpAuthenticationPasswordSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\HttpAuthenticationSetting' shared autowired service.
     *
     * @return HttpAuthenticationSetting
     */
    protected function getHttpAuthenticationSettingService()
    {
        $a = $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService();
        if (isset($this->privates['Staatic\WordPress\Setting\Advanced\HttpAuthenticationSetting'])) {
            return $this->privates['Staatic\WordPress\Setting\Advanced\HttpAuthenticationSetting'];
        }

        return $this->privates['Staatic\WordPress\Setting\Advanced\HttpAuthenticationSetting'] = new HttpAuthenticationSetting(
            $a
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\HttpAuthenticationUsernameSetting' shared autowired service.
     *
     * @return HttpAuthenticationUsernameSetting
     */
    protected function getHttpAuthenticationUsernameSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\HttpAuthenticationUsernameSetting'] = new HttpAuthenticationUsernameSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\HttpConcurrencySetting' shared autowired service.
     *
     * @return HttpConcurrencySetting
     */
    protected function getHttpConcurrencySettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\HttpConcurrencySetting'] = new HttpConcurrencySetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\HttpDelaySetting' shared autowired service.
     *
     * @return HttpDelaySetting
     */
    protected function getHttpDelaySettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\HttpDelaySetting'] = new HttpDelaySetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\HttpNetworkSetting' shared autowired service.
     *
     * @return HttpNetworkSetting
     */
    protected function getHttpNetworkSettingService()
    {
        $a = $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService();
        if (isset($this->privates['Staatic\WordPress\Setting\Advanced\HttpNetworkSetting'])) {
            return $this->privates['Staatic\WordPress\Setting\Advanced\HttpNetworkSetting'];
        }

        return $this->privates['Staatic\WordPress\Setting\Advanced\HttpNetworkSetting'] = new HttpNetworkSetting($a);
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\HttpTimeoutSetting' shared autowired service.
     *
     * @return HttpTimeoutSetting
     */
    protected function getHttpTimeoutSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\HttpTimeoutSetting'] = new HttpTimeoutSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\HttpToHttpsSetting' shared autowired service.
     *
     * @return HttpToHttpsSetting
     */
    protected function getHttpToHttpsSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\HttpToHttpsSetting'] = new HttpToHttpsSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\LoggingLevelSetting' shared autowired service.
     *
     * @return LoggingLevelSetting
     */
    protected function getLoggingLevelSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\LoggingLevelSetting'] = new LoggingLevelSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\OverrideSiteUrlSetting' shared autowired service.
     *
     * @return OverrideSiteUrlSetting
     */
    protected function getOverrideSiteUrlSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\OverrideSiteUrlSetting'] = new OverrideSiteUrlSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\PageNotFoundPathSetting' shared autowired service.
     *
     * @return PageNotFoundPathSetting
     */
    protected function getPageNotFoundPathSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\PageNotFoundPathSetting'] = new PageNotFoundPathSetting(
            $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] = $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] ?? new SiteUrlProvider()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\SslVerifyBehaviorSetting' shared autowired service.
     *
     * @return SslVerifyBehaviorSetting
     */
    protected function getSslVerifyBehaviorSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\SslVerifyBehaviorSetting'] = new SslVerifyBehaviorSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\SslVerifyPathSetting' shared autowired service.
     *
     * @return SslVerifyPathSetting
     */
    protected function getSslVerifyPathSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\SslVerifyPathSetting'] = new SslVerifyPathSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\SslVerifySetting' shared autowired service.
     *
     * @return SslVerifySetting
     */
    protected function getSslVerifySettingService()
    {
        $a = $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService();
        if (isset($this->privates['Staatic\WordPress\Setting\Advanced\SslVerifySetting'])) {
            return $this->privates['Staatic\WordPress\Setting\Advanced\SslVerifySetting'];
        }

        return $this->privates['Staatic\WordPress\Setting\Advanced\SslVerifySetting'] = new SslVerifySetting($a);
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\UninstallDataSetting' shared autowired service.
     *
     * @return UninstallDataSetting
     */
    protected function getUninstallDataSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\UninstallDataSetting'] = new UninstallDataSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\UninstallSetting' shared autowired service.
     *
     * @return UninstallSetting
     */
    protected function getUninstallSettingService()
    {
        $a = $this->privates['.service_locator.LQWyeDB'] ?? $this->get_ServiceLocator_LQWyeDBService();
        if (isset($this->privates['Staatic\WordPress\Setting\Advanced\UninstallSetting'])) {
            return $this->privates['Staatic\WordPress\Setting\Advanced\UninstallSetting'];
        }

        return $this->privates['Staatic\WordPress\Setting\Advanced\UninstallSetting'] = new UninstallSetting($a);
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\UninstallSettingsSetting' shared autowired service.
     *
     * @return UninstallSettingsSetting
     */
    protected function getUninstallSettingsSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\UninstallSettingsSetting'] = new UninstallSettingsSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Advanced\WorkDirectorySetting' shared autowired service.
     *
     * @return WorkDirectorySetting
     */
    protected function getWorkDirectorySettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Advanced\WorkDirectorySetting'] = new WorkDirectorySetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Build\AdditionalPathsSetting' shared autowired service.
     *
     * @return AdditionalPathsSetting
     */
    protected function getAdditionalPathsSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Build\AdditionalPathsSetting'] = new AdditionalPathsSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Build\AdditionalRedirectsSetting' shared autowired service.
     *
     * @return AdditionalRedirectsSetting
     */
    protected function getAdditionalRedirectsSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Build\AdditionalRedirectsSetting'] = new AdditionalRedirectsSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Build\AdditionalUrlsSetting' shared autowired service.
     *
     * @return AdditionalUrlsSetting
     */
    protected function getAdditionalUrlsSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Build\AdditionalUrlsSetting'] = new AdditionalUrlsSetting(
            $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] = $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] ?? new SiteUrlProvider()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Build\DestinationUrlSetting' shared autowired service.
     *
     * @return DestinationUrlSetting
     */
    protected function getDestinationUrlSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Build\DestinationUrlSetting'] = new DestinationUrlSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Build\ExcludeUrlsSetting' shared autowired service.
     *
     * @return ExcludeUrlsSetting
     */
    protected function getExcludeUrlsSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Build\ExcludeUrlsSetting'] = new ExcludeUrlsSetting(
            $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] = $this->privates['Staatic\WordPress\Service\SiteUrlProvider'] ?? new SiteUrlProvider()
        );
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Build\PreviewUrlSetting' shared autowired service.
     *
     * @return PreviewUrlSetting
     */
    protected function getPreviewUrlSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Build\PreviewUrlSetting'] = new PreviewUrlSetting();
    }

    /**
     * Gets the private 'Staatic\WordPress\Setting\Deployment\DeploymentMethodSetting' shared autowired service.
     *
     * @return DeploymentMethodSetting
     */
    protected function getDeploymentMethodSettingService()
    {
        return $this->privates['Staatic\WordPress\Setting\Deployment\DeploymentMethodSetting'] = new DeploymentMethodSetting();
    }

    /**
     * Gets the private 'wpdb' shared autowired service.
     *
     * @return wpdb
     */
    protected function getWpdbService()
    {
        return $this->privates['wpdb'] = (new WpdbWrapper())->get();
    }

    /**
     * @param string $name
     * @return mixed[]|bool|string|int|float|UnitEnum|null
     */
    public function getParameter($name)
    {
        if (!(isset($this->parameters[$name]) || isset($this->loadedDynamicParameters[$name]) || \array_key_exists(
            $name,
            $this->parameters
        ))) {
            throw new ParameterNotFoundException($name);
        }
        if (isset($this->loadedDynamicParameters[$name])) {
            return $this->loadedDynamicParameters[$name] ? $this->dynamicParameters[$name] : $this->getDynamicParameter(
                $name
            );
        }

        return $this->parameters[$name];
    }

    /**
     * @param string $name
     */
    public function hasParameter($name): bool
    {
        return isset($this->parameters[$name]) || isset($this->loadedDynamicParameters[$name]) || \array_key_exists(
            $name,
            $this->parameters
        );
    }

    /**
     * @param string $name
     */
    public function setParameter($name, $value): void
    {
        throw new LogicException('Impossible to call set() on a frozen ParameterBag.');
    }

    public function getParameterBag(): ParameterBagInterface
    {
        if (null === $this->parameterBag) {
            $parameters = $this->parameters;
            foreach ($this->loadedDynamicParameters as $name => $loaded) {
                $parameters[$name] = $loaded ? $this->dynamicParameters[$name] : $this->getDynamicParameter($name);
            }
            $this->parameterBag = new FrozenParameterBag($parameters);
        }

        return $this->parameterBag;
    }

    private $loadedDynamicParameters = [];

    private $dynamicParameters = [];

    private function getDynamicParameter(string $name)
    {
        throw new ParameterNotFoundException($name);
    }

    protected function getDefaultParameters(): array
    {
        return [
            'staatic.version' => '1.10.4'
        ];
    }
}
class BuildRepositoryProxy52119c7 implements BuildRepositoryInterface, LazyObjectInterface
{
    use LazyProxyTrait;

    private const LAZY_OBJECT_PROPERTY_SCOPES = [];

    public function initializeLazyObject(): BuildRepositoryInterface
    {
        if ($state = $this->lazyObjectState ?? null) {
            return $state->realInstance = $state->realInstance ?? ($state->initializer)();
        }

        return $this;
    }

    public function nextId(): string
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->nextId(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Framework\BuildRepository\BuildRepositoryInterface::nextId()".'
        );
    }

    /**
     * @param Build $build
     */
    public function add($build): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->add(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\Framework\BuildRepository\BuildRepositoryInterface::add()".'
            );
        }
    }

    /**
     * @param Build $build
     */
    public function update($build): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->update(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\Framework\BuildRepository\BuildRepositoryInterface::update()".'
            );
        }
    }

    /**
     * @param string $buildId
     */
    public function find($buildId): ?Build
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->find(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Framework\BuildRepository\BuildRepositoryInterface::find()".'
        );
    }
}
// Help opcache.preload discover always-needed symbols
class_exists(Hydrator::class);
class_exists(LazyObjectRegistry::class);
class_exists(LazyObjectState::class);
if (!\class_exists('Staatic\Vendor\BuildRepositoryProxy52119c7', \false)) {
    \class_alias(__NAMESPACE__ . '\BuildRepositoryProxy52119c7', 'Staatic\Vendor\BuildRepositoryProxy52119c7', \false);
}
class TransientCacheProxy1ba92cc implements CacheInterface, LazyObjectInterface
{
    use LazyProxyTrait;

    private const LAZY_OBJECT_PROPERTY_SCOPES = [];

    public function initializeLazyObject(): CacheInterface
    {
        if ($state = $this->lazyObjectState ?? null) {
            return $state->realInstance = $state->realInstance ?? ($state->initializer)();
        }

        return $this;
    }

    public function get($key, $default = null)
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->get(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException('Cannot forward abstract method "Psr\SimpleCache\CacheInterface::get()".');
    }

    public function set($key, $value, $ttl = null)
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->set(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException('Cannot forward abstract method "Psr\SimpleCache\CacheInterface::set()".');
    }

    public function delete($key)
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->delete(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException('Cannot forward abstract method "Psr\SimpleCache\CacheInterface::delete()".');
    }

    public function clear()
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->clear(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException('Cannot forward abstract method "Psr\SimpleCache\CacheInterface::clear()".');
    }

    public function getMultiple($keys, $default = null)
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->getMultiple(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Psr\SimpleCache\CacheInterface::getMultiple()".'
        );
    }

    public function setMultiple($values, $ttl = null)
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->setMultiple(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Psr\SimpleCache\CacheInterface::setMultiple()".'
        );
    }

    public function deleteMultiple($keys)
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->deleteMultiple(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Psr\SimpleCache\CacheInterface::deleteMultiple()".'
        );
    }

    public function has($key)
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->has(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException('Cannot forward abstract method "Psr\SimpleCache\CacheInterface::has()".');
    }
}
// Help opcache.preload discover always-needed symbols
class_exists(Hydrator::class);
class_exists(LazyObjectRegistry::class);
class_exists(LazyObjectState::class);
if (!\class_exists('Staatic\Vendor\TransientCacheProxy1ba92cc', \false)) {
    \class_alias(__NAMESPACE__ . '\TransientCacheProxy1ba92cc', 'Staatic\Vendor\TransientCacheProxy1ba92cc', \false);
}
class CrawlQueueProxy5f75fbb implements CrawlQueueInterface, LazyObjectInterface
{
    use LazyProxyTrait;

    private const LAZY_OBJECT_PROPERTY_SCOPES = [];

    public function initializeLazyObject(): CrawlQueueInterface
    {
        if ($state = $this->lazyObjectState ?? null) {
            return $state->realInstance = $state->realInstance ?? ($state->initializer)();
        }

        return $this;
    }

    public function clear(): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->clear(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\Crawler\CrawlQueue\CrawlQueueInterface::clear()".'
            );
        }
    }

    /**
     * @param CrawlUrl $crawlUrl
     * @param int $priority
     */
    public function enqueue($crawlUrl, $priority): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->enqueue(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\Crawler\CrawlQueue\CrawlQueueInterface::enqueue()".'
            );
        }
    }

    public function dequeue(): CrawlUrl
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->dequeue(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Crawler\CrawlQueue\CrawlQueueInterface::dequeue()".'
        );
    }

    #[ReturnTypeWillChange]
    public function count()
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->count(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException('Cannot forward abstract method "Countable::count()".');
    }
}
// Help opcache.preload discover always-needed symbols
class_exists(Hydrator::class);
class_exists(LazyObjectRegistry::class);
class_exists(LazyObjectState::class);
if (!\class_exists('Staatic\Vendor\CrawlQueueProxy5f75fbb', \false)) {
    \class_alias(__NAMESPACE__ . '\CrawlQueueProxy5f75fbb', 'Staatic\Vendor\CrawlQueueProxy5f75fbb', \false);
}
class DeploymentRepositoryProxyE31553a implements DeploymentRepositoryInterface, LazyObjectInterface
{
    use LazyProxyTrait;

    private const LAZY_OBJECT_PROPERTY_SCOPES = [];

    public function initializeLazyObject(): DeploymentRepositoryInterface
    {
        if ($state = $this->lazyObjectState ?? null) {
            return $state->realInstance = $state->realInstance ?? ($state->initializer)();
        }

        return $this;
    }

    public function nextId(): string
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->nextId(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Framework\DeploymentRepository\DeploymentRepositoryInterface::nextId()".'
        );
    }

    /**
     * @param Deployment $deployment
     */
    public function add($deployment): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->add(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\Framework\DeploymentRepository\DeploymentRepositoryInterface::add()".'
            );
        }
    }

    /**
     * @param Deployment $deployment
     */
    public function update($deployment): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->update(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\Framework\DeploymentRepository\DeploymentRepositoryInterface::update()".'
            );
        }
    }

    /**
     * @param string $deploymentId
     */
    public function find($deploymentId): ?Deployment
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->find(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Framework\DeploymentRepository\DeploymentRepositoryInterface::find()".'
        );
    }
}
// Help opcache.preload discover always-needed symbols
class_exists(Hydrator::class);
class_exists(LazyObjectRegistry::class);
class_exists(LazyObjectState::class);
if (!\class_exists('Staatic\Vendor\DeploymentRepositoryProxyE31553a', \false)) {
    \class_alias(
        __NAMESPACE__ . '\DeploymentRepositoryProxyE31553a',
        'Staatic\Vendor\DeploymentRepositoryProxyE31553a',
        \false
    );
}
class LoggerProxy58fa090 implements LoggerInterface, LazyObjectInterface
{
    use LazyProxyTrait;

    private const LAZY_OBJECT_PROPERTY_SCOPES = [];

    public function initializeLazyObject(): LoggerInterface
    {
        if ($state = $this->lazyObjectState ?? null) {
            return $state->realInstance = $state->realInstance ?? ($state->initializer)();
        }

        return $this;
    }

    public function consoleLoggerEnabled(): bool
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->consoleLoggerEnabled(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\WordPress\Logging\LoggerInterface::consoleLoggerEnabled()".'
        );
    }

    public function enableConsoleLogger(): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->enableConsoleLogger(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\WordPress\Logging\LoggerInterface::enableConsoleLogger()".'
            );
        }
    }

    public function disableConsoleLogger(): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->disableConsoleLogger(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\WordPress\Logging\LoggerInterface::disableConsoleLogger()".'
            );
        }
    }

    public function databaseLoggerEnabled(): bool
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->databaseLoggerEnabled(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\WordPress\Logging\LoggerInterface::databaseLoggerEnabled()".'
        );
    }

    public function enableDatabaseLogger(): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->enableDatabaseLogger(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\WordPress\Logging\LoggerInterface::enableDatabaseLogger()".'
            );
        }
    }

    public function disableDatabaseLogger(): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->disableDatabaseLogger(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\WordPress\Logging\LoggerInterface::disableDatabaseLogger()".'
            );
        }
    }

    /**
     * @param Stringable|string $message
     * @param mixed[] $context
     */
    public function emergency($message, $context = []): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->emergency(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException('Cannot forward abstract method "Psr\Log\LoggerInterface::emergency()".');
        }
    }

    /**
     * @param Stringable|string $message
     * @param mixed[] $context
     */
    public function alert($message, $context = []): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->alert(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException('Cannot forward abstract method "Psr\Log\LoggerInterface::alert()".');
        }
    }

    /**
     * @param Stringable|string $message
     * @param mixed[] $context
     */
    public function critical($message, $context = []): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->critical(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException('Cannot forward abstract method "Psr\Log\LoggerInterface::critical()".');
        }
    }

    /**
     * @param Stringable|string $message
     * @param mixed[] $context
     */
    public function error($message, $context = []): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->error(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException('Cannot forward abstract method "Psr\Log\LoggerInterface::error()".');
        }
    }

    /**
     * @param Stringable|string $message
     * @param mixed[] $context
     */
    public function warning($message, $context = []): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->warning(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException('Cannot forward abstract method "Psr\Log\LoggerInterface::warning()".');
        }
    }

    /**
     * @param Stringable|string $message
     * @param mixed[] $context
     */
    public function notice($message, $context = []): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->notice(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException('Cannot forward abstract method "Psr\Log\LoggerInterface::notice()".');
        }
    }

    /**
     * @param Stringable|string $message
     * @param mixed[] $context
     */
    public function info($message, $context = []): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->info(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException('Cannot forward abstract method "Psr\Log\LoggerInterface::info()".');
        }
    }

    /**
     * @param Stringable|string $message
     * @param mixed[] $context
     */
    public function debug($message, $context = []): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->debug(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException('Cannot forward abstract method "Psr\Log\LoggerInterface::debug()".');
        }
    }

    /**
     * @param Stringable|string $message
     * @param mixed[] $context
     */
    public function log($level, $message, $context = []): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->log(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException('Cannot forward abstract method "Psr\Log\LoggerInterface::log()".');
        }
    }

    /**
     * @param mixed[] $context
     */
    public function changeContext($context): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->changeContext(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\WordPress\Logging\Contextable::changeContext()".'
            );
        }
    }
}
// Help opcache.preload discover always-needed symbols
class_exists(Hydrator::class);
class_exists(LazyObjectRegistry::class);
class_exists(LazyObjectState::class);
if (!\class_exists('Staatic\Vendor\LoggerProxy58fa090', \false)) {
    \class_alias(__NAMESPACE__ . '\LoggerProxy58fa090', 'Staatic\Vendor\LoggerProxy58fa090', \false);
}
class ResourceRepositoryInterfaceProxyAe4180b implements ResourceRepositoryInterface, LazyObjectInterface
{
    use LazyProxyTrait;

    private const LAZY_OBJECT_PROPERTY_SCOPES = [];

    public function initializeLazyObject(): ResourceRepositoryInterface
    {
        if ($state = $this->lazyObjectState ?? null) {
            return $state->realInstance = $state->realInstance ?? ($state->initializer)();
        }

        return $this;
    }

    /**
     * @param Resource $resource
     */
    public function write($resource): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->write(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\Framework\ResourceRepository\ResourceRepositoryInterface::write()".'
            );
        }
    }

    /**
     * @param string $sha1
     */
    public function find($sha1): ?Resource
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->find(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Framework\ResourceRepository\ResourceRepositoryInterface::find()".'
        );
    }

    /**
     * @param string $sha1
     */
    public function delete($sha1): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->delete(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\Framework\ResourceRepository\ResourceRepositoryInterface::delete()".'
            );
        }
    }
}
// Help opcache.preload discover always-needed symbols
class_exists(Hydrator::class);
class_exists(LazyObjectRegistry::class);
class_exists(LazyObjectState::class);
if (!\class_exists('Staatic\Vendor\ResourceRepositoryInterfaceProxyAe4180b', \false)) {
    \class_alias(
        __NAMESPACE__ . '\ResourceRepositoryInterfaceProxyAe4180b',
        'Staatic\Vendor\ResourceRepositoryInterfaceProxyAe4180b',
        \false
    );
}
class ResultRepositoryProxyE12a645 implements ResultRepositoryInterface, LazyObjectInterface
{
    use LazyProxyTrait;

    private const LAZY_OBJECT_PROPERTY_SCOPES = [];

    public function initializeLazyObject(): ResultRepositoryInterface
    {
        if ($state = $this->lazyObjectState ?? null) {
            return $state->realInstance = $state->realInstance ?? ($state->initializer)();
        }

        return $this;
    }

    public function nextId(): string
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->nextId(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Framework\ResultRepository\ResultRepositoryInterface::nextId()".'
        );
    }

    /**
     * @param Result $result
     */
    public function add($result): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->add(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\Framework\ResultRepository\ResultRepositoryInterface::add()".'
            );
        }
    }

    /**
     * @param Result $result
     */
    public function update($result): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->update(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\Framework\ResultRepository\ResultRepositoryInterface::update()".'
            );
        }
    }

    /**
     * @param Result $result
     */
    public function delete($result): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->delete(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\Framework\ResultRepository\ResultRepositoryInterface::delete()".'
            );
        }
    }

    /**
     * @param string $sourceBuildId
     * @param string $targetBuildId
     */
    public function mergeBuildResults($sourceBuildId, $targetBuildId): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->mergeBuildResults(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\Framework\ResultRepository\ResultRepositoryInterface::mergeBuildResults()".'
            );
        }
    }

    /**
     * @param string $buildId
     * @param string $deploymentId
     */
    public function scheduleForDeployment($buildId, $deploymentId): int
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->scheduleForDeployment(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Framework\ResultRepository\ResultRepositoryInterface::scheduleForDeployment()".'
        );
    }

    /**
     * @param Result $result
     * @param string $deploymentId
     */
    public function markDeployed($result, $deploymentId): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->markDeployed(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\Framework\ResultRepository\ResultRepositoryInterface::markDeployed()".'
            );
        }
    }

    /**
     * @param string $deploymentId
     * @param mixed[] $resultIds
     */
    public function markManyDeployed($deploymentId, $resultIds): void
    {
        if (isset($this->lazyObjectState)) {
            ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->markManyDeployed(
                ...\func_get_args()
            );
        } else {
            throw new BadMethodCallException(
                'Cannot forward abstract method "Staatic\Framework\ResultRepository\ResultRepositoryInterface::markManyDeployed()".'
            );
        }
    }

    /**
     * @param string $resultId
     */
    public function find($resultId): ?Result
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->find(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Framework\ResultRepository\ResultRepositoryInterface::find()".'
        );
    }

    public function findAll(): Generator
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->findAll(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Framework\ResultRepository\ResultRepositoryInterface::findAll()".'
        );
    }

    /**
     * @param string $buildId
     */
    public function findByBuildId($buildId): Generator
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->findByBuildId(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Framework\ResultRepository\ResultRepositoryInterface::findByBuildId()".'
        );
    }

    /**
     * @param string $buildId
     */
    public function findByBuildIdWithRedirectUrl($buildId): array
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->findByBuildIdWithRedirectUrl(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Framework\ResultRepository\ResultRepositoryInterface::findByBuildIdWithRedirectUrl()".'
        );
    }

    /**
     * @param string $buildId
     * @param string $deploymentId
     */
    public function findByBuildIdPendingDeployment($buildId, $deploymentId): Generator
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->findByBuildIdPendingDeployment(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Framework\ResultRepository\ResultRepositoryInterface::findByBuildIdPendingDeployment()".'
        );
    }

    /**
     * @param string $buildId
     * @param UriInterface $url
     */
    public function findOneByBuildIdAndUrl($buildId, $url): ?Result
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->findOneByBuildIdAndUrl(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Framework\ResultRepository\ResultRepositoryInterface::findOneByBuildIdAndUrl()".'
        );
    }

    /**
     * @param string $buildId
     * @param UriInterface $url
     */
    public function findOneByBuildIdAndUrlResolved($buildId, $url): ?Result
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->findOneByBuildIdAndUrlResolved(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Framework\ResultRepository\ResultRepositoryInterface::findOneByBuildIdAndUrlResolved()".'
        );
    }

    /**
     * @param string $buildId
     */
    public function countByBuildId($buildId): int
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->countByBuildId(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Framework\ResultRepository\ResultRepositoryInterface::countByBuildId()".'
        );
    }

    /**
     * @param string $buildId
     * @param string $deploymentId
     */
    public function countByBuildIdPendingDeployment($buildId, $deploymentId): int
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->countByBuildIdPendingDeployment(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Framework\ResultRepository\ResultRepositoryInterface::countByBuildIdPendingDeployment()".'
        );
    }
}
// Help opcache.preload discover always-needed symbols
class_exists(Hydrator::class);
class_exists(LazyObjectRegistry::class);
class_exists(LazyObjectState::class);
if (!\class_exists('Staatic\Vendor\ResultRepositoryProxyE12a645', \false)) {
    \class_alias(
        __NAMESPACE__ . '\ResultRepositoryProxyE12a645',
        'Staatic\Vendor\ResultRepositoryProxyE12a645',
        \false
    );
}
class UrlTransformerInterfaceProxy3bb5952 implements UrlTransformerInterface, LazyObjectInterface
{
    use LazyProxyTrait;

    private const LAZY_OBJECT_PROPERTY_SCOPES = [];

    public function initializeLazyObject(): UrlTransformerInterface
    {
        if ($state = $this->lazyObjectState ?? null) {
            return $state->realInstance = $state->realInstance ?? ($state->initializer)();
        }

        return $this;
    }

    /**
     * @param UriInterface $url
     * @param UriInterface|null $foundOnUrl
     * @param mixed[] $context
     */
    public function transform($url, $foundOnUrl = null, $context = []): UrlTransformation
    {
        if (isset($this->lazyObjectState)) {
            return ($this->lazyObjectState->realInstance = $this->lazyObjectState->realInstance ?? ($this->lazyObjectState->initializer)())->transform(
                ...\func_get_args()
            );
        }

        throw new BadMethodCallException(
            'Cannot forward abstract method "Staatic\Crawler\UrlTransformer\UrlTransformerInterface::transform()".'
        );
    }
}
// Help opcache.preload discover always-needed symbols
class_exists(Hydrator::class);
class_exists(LazyObjectRegistry::class);
class_exists(LazyObjectState::class);
if (!\class_exists('Staatic\Vendor\UrlTransformerInterfaceProxy3bb5952', \false)) {
    \class_alias(
        __NAMESPACE__ . '\UrlTransformerInterfaceProxy3bb5952',
        'Staatic\Vendor\UrlTransformerInterfaceProxy3bb5952',
        \false
    );
}
