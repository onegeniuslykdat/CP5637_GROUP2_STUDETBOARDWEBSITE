<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid;

use Staatic\Vendor\Ramsey\Uuid\Builder\FallbackBuilder;
use Staatic\Vendor\Ramsey\Uuid\Builder\UuidBuilderInterface;
use Staatic\Vendor\Ramsey\Uuid\Codec\CodecInterface;
use Staatic\Vendor\Ramsey\Uuid\Codec\GuidStringCodec;
use Staatic\Vendor\Ramsey\Uuid\Codec\StringCodec;
use Staatic\Vendor\Ramsey\Uuid\Converter\Number\GenericNumberConverter;
use Staatic\Vendor\Ramsey\Uuid\Converter\NumberConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Converter\Time\GenericTimeConverter;
use Staatic\Vendor\Ramsey\Uuid\Converter\Time\PhpTimeConverter;
use Staatic\Vendor\Ramsey\Uuid\Converter\TimeConverterInterface;
use Staatic\Vendor\Ramsey\Uuid\Generator\DceSecurityGenerator;
use Staatic\Vendor\Ramsey\Uuid\Generator\DceSecurityGeneratorInterface;
use Staatic\Vendor\Ramsey\Uuid\Generator\NameGeneratorFactory;
use Staatic\Vendor\Ramsey\Uuid\Generator\NameGeneratorInterface;
use Staatic\Vendor\Ramsey\Uuid\Generator\PeclUuidNameGenerator;
use Staatic\Vendor\Ramsey\Uuid\Generator\PeclUuidRandomGenerator;
use Staatic\Vendor\Ramsey\Uuid\Generator\PeclUuidTimeGenerator;
use Staatic\Vendor\Ramsey\Uuid\Generator\RandomGeneratorFactory;
use Staatic\Vendor\Ramsey\Uuid\Generator\RandomGeneratorInterface;
use Staatic\Vendor\Ramsey\Uuid\Generator\TimeGeneratorFactory;
use Staatic\Vendor\Ramsey\Uuid\Generator\TimeGeneratorInterface;
use Staatic\Vendor\Ramsey\Uuid\Generator\UnixTimeGenerator;
use Staatic\Vendor\Ramsey\Uuid\Guid\GuidBuilder;
use Staatic\Vendor\Ramsey\Uuid\Math\BrickMathCalculator;
use Staatic\Vendor\Ramsey\Uuid\Math\CalculatorInterface;
use Staatic\Vendor\Ramsey\Uuid\Nonstandard\UuidBuilder as NonstandardUuidBuilder;
use Staatic\Vendor\Ramsey\Uuid\Provider\Dce\SystemDceSecurityProvider;
use Staatic\Vendor\Ramsey\Uuid\Provider\DceSecurityProviderInterface;
use Staatic\Vendor\Ramsey\Uuid\Provider\Node\FallbackNodeProvider;
use Staatic\Vendor\Ramsey\Uuid\Provider\Node\RandomNodeProvider;
use Staatic\Vendor\Ramsey\Uuid\Provider\Node\SystemNodeProvider;
use Staatic\Vendor\Ramsey\Uuid\Provider\NodeProviderInterface;
use Staatic\Vendor\Ramsey\Uuid\Provider\Time\SystemTimeProvider;
use Staatic\Vendor\Ramsey\Uuid\Provider\TimeProviderInterface;
use Staatic\Vendor\Ramsey\Uuid\Rfc4122\UuidBuilder as Rfc4122UuidBuilder;
use Staatic\Vendor\Ramsey\Uuid\Validator\GenericValidator;
use Staatic\Vendor\Ramsey\Uuid\Validator\ValidatorInterface;
use const PHP_INT_SIZE;
class FeatureSet
{
    /**
     * @var bool
     */
    private $force32Bit = \false;
    /**
     * @var bool
     */
    private $ignoreSystemNode = \false;
    /**
     * @var bool
     */
    private $enablePecl = \false;
    /**
     * @var TimeProviderInterface|null
     */
    private $timeProvider;
    /**
     * @var CalculatorInterface
     */
    private $calculator;
    /**
     * @var CodecInterface
     */
    private $codec;
    /**
     * @var DceSecurityGeneratorInterface
     */
    private $dceSecurityGenerator;
    /**
     * @var NameGeneratorInterface
     */
    private $nameGenerator;
    /**
     * @var NodeProviderInterface
     */
    private $nodeProvider;
    /**
     * @var NumberConverterInterface
     */
    private $numberConverter;
    /**
     * @var RandomGeneratorInterface
     */
    private $randomGenerator;
    /**
     * @var TimeConverterInterface
     */
    private $timeConverter;
    /**
     * @var TimeGeneratorInterface
     */
    private $timeGenerator;
    /**
     * @var TimeGeneratorInterface
     */
    private $unixTimeGenerator;
    /**
     * @var UuidBuilderInterface
     */
    private $builder;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    public function __construct(bool $useGuids = \false, bool $force32Bit = \false, bool $forceNoBigNumber = \false, bool $ignoreSystemNode = \false, bool $enablePecl = \false)
    {
        $this->force32Bit = $force32Bit;
        $this->ignoreSystemNode = $ignoreSystemNode;
        $this->enablePecl = $enablePecl;
        $this->randomGenerator = $this->buildRandomGenerator();
        $this->setCalculator(new BrickMathCalculator());
        $this->builder = $this->buildUuidBuilder($useGuids);
        $this->codec = $this->buildCodec($useGuids);
        $this->nodeProvider = $this->buildNodeProvider();
        $this->nameGenerator = $this->buildNameGenerator();
        $this->setTimeProvider(new SystemTimeProvider());
        $this->setDceSecurityProvider(new SystemDceSecurityProvider());
        $this->validator = new GenericValidator();
        assert($this->timeProvider !== null);
        $this->unixTimeGenerator = $this->buildUnixTimeGenerator();
    }
    public function getBuilder(): UuidBuilderInterface
    {
        return $this->builder;
    }
    public function getCalculator(): CalculatorInterface
    {
        return $this->calculator;
    }
    public function getCodec(): CodecInterface
    {
        return $this->codec;
    }
    public function getDceSecurityGenerator(): DceSecurityGeneratorInterface
    {
        return $this->dceSecurityGenerator;
    }
    public function getNameGenerator(): NameGeneratorInterface
    {
        return $this->nameGenerator;
    }
    public function getNodeProvider(): NodeProviderInterface
    {
        return $this->nodeProvider;
    }
    public function getNumberConverter(): NumberConverterInterface
    {
        return $this->numberConverter;
    }
    public function getRandomGenerator(): RandomGeneratorInterface
    {
        return $this->randomGenerator;
    }
    public function getTimeConverter(): TimeConverterInterface
    {
        return $this->timeConverter;
    }
    public function getTimeGenerator(): TimeGeneratorInterface
    {
        return $this->timeGenerator;
    }
    public function getUnixTimeGenerator(): TimeGeneratorInterface
    {
        return $this->unixTimeGenerator;
    }
    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }
    /**
     * @param CalculatorInterface $calculator
     */
    public function setCalculator($calculator): void
    {
        $this->calculator = $calculator;
        $this->numberConverter = $this->buildNumberConverter($calculator);
        $this->timeConverter = $this->buildTimeConverter($calculator);
        if (isset($this->timeProvider)) {
            $this->timeGenerator = $this->buildTimeGenerator($this->timeProvider);
        }
    }
    /**
     * @param DceSecurityProviderInterface $dceSecurityProvider
     */
    public function setDceSecurityProvider($dceSecurityProvider): void
    {
        $this->dceSecurityGenerator = $this->buildDceSecurityGenerator($dceSecurityProvider);
    }
    /**
     * @param NodeProviderInterface $nodeProvider
     */
    public function setNodeProvider($nodeProvider): void
    {
        $this->nodeProvider = $nodeProvider;
        if (isset($this->timeProvider)) {
            $this->timeGenerator = $this->buildTimeGenerator($this->timeProvider);
        }
    }
    /**
     * @param TimeProviderInterface $timeProvider
     */
    public function setTimeProvider($timeProvider): void
    {
        $this->timeProvider = $timeProvider;
        $this->timeGenerator = $this->buildTimeGenerator($timeProvider);
    }
    /**
     * @param ValidatorInterface $validator
     */
    public function setValidator($validator): void
    {
        $this->validator = $validator;
    }
    private function buildCodec(bool $useGuids = \false): CodecInterface
    {
        if ($useGuids) {
            return new GuidStringCodec($this->builder);
        }
        return new StringCodec($this->builder);
    }
    private function buildDceSecurityGenerator(DceSecurityProviderInterface $dceSecurityProvider): DceSecurityGeneratorInterface
    {
        return new DceSecurityGenerator($this->numberConverter, $this->timeGenerator, $dceSecurityProvider);
    }
    private function buildNodeProvider(): NodeProviderInterface
    {
        if ($this->ignoreSystemNode) {
            return new RandomNodeProvider();
        }
        return new FallbackNodeProvider([new SystemNodeProvider(), new RandomNodeProvider()]);
    }
    private function buildNumberConverter(CalculatorInterface $calculator): NumberConverterInterface
    {
        return new GenericNumberConverter($calculator);
    }
    private function buildRandomGenerator(): RandomGeneratorInterface
    {
        if ($this->enablePecl) {
            return new PeclUuidRandomGenerator();
        }
        return (new RandomGeneratorFactory())->getGenerator();
    }
    private function buildTimeGenerator(TimeProviderInterface $timeProvider): TimeGeneratorInterface
    {
        if ($this->enablePecl) {
            return new PeclUuidTimeGenerator();
        }
        return (new TimeGeneratorFactory($this->nodeProvider, $this->timeConverter, $timeProvider))->getGenerator();
    }
    private function buildUnixTimeGenerator(): TimeGeneratorInterface
    {
        return new UnixTimeGenerator($this->randomGenerator);
    }
    private function buildNameGenerator(): NameGeneratorInterface
    {
        if ($this->enablePecl) {
            return new PeclUuidNameGenerator();
        }
        return (new NameGeneratorFactory())->getGenerator();
    }
    private function buildTimeConverter(CalculatorInterface $calculator): TimeConverterInterface
    {
        $genericConverter = new GenericTimeConverter($calculator);
        if ($this->is64BitSystem()) {
            return new PhpTimeConverter($calculator, $genericConverter);
        }
        return $genericConverter;
    }
    private function buildUuidBuilder(bool $useGuids = \false): UuidBuilderInterface
    {
        if ($useGuids) {
            return new GuidBuilder($this->numberConverter, $this->timeConverter);
        }
        return new FallbackBuilder([new Rfc4122UuidBuilder($this->numberConverter, $this->timeConverter), new NonstandardUuidBuilder($this->numberConverter, $this->timeConverter)]);
    }
    private function is64BitSystem(): bool
    {
        return PHP_INT_SIZE === 8 && !$this->force32Bit;
    }
}
