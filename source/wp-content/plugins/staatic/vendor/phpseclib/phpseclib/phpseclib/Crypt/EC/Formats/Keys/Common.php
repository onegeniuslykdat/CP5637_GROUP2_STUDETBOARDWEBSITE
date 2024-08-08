<?php

namespace Staatic\Vendor\phpseclib3\Crypt\EC\Formats\Keys;

use RuntimeException;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\Characteristic_two;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\Pentanomial;
use UnexpectedValueException;
use ReflectionClass;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps\ECParameters;
use DirectoryIterator;
use Staatic\Vendor\phpseclib3\File\ASN1\Element;
use Staatic\Vendor\phpseclib3\Common\Functions\Strings;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Base as BaseCurve;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Binary as BinaryCurve;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\Prime as PrimeCurve;
use Staatic\Vendor\phpseclib3\Crypt\EC\BaseCurves\TwistedEdwards as TwistedEdwardsCurve;
use Staatic\Vendor\phpseclib3\Exception\UnsupportedCurveException;
use Staatic\Vendor\phpseclib3\File\ASN1;
use Staatic\Vendor\phpseclib3\File\ASN1\Maps;
use Staatic\Vendor\phpseclib3\Math\BigInteger;
trait Common
{
    private static $curveOIDs = [];
    protected static $childOIDsLoaded = \false;
    private static $useNamedCurves = \true;
    private static function initialize_static_variables()
    {
        if (empty(self::$curveOIDs)) {
            self::$curveOIDs = ['prime192v1' => '1.2.840.10045.3.1.1', 'prime192v2' => '1.2.840.10045.3.1.2', 'prime192v3' => '1.2.840.10045.3.1.3', 'prime239v1' => '1.2.840.10045.3.1.4', 'prime239v2' => '1.2.840.10045.3.1.5', 'prime239v3' => '1.2.840.10045.3.1.6', 'prime256v1' => '1.2.840.10045.3.1.7', 'nistp256' => '1.2.840.10045.3.1.7', 'nistp384' => '1.3.132.0.34', 'nistp521' => '1.3.132.0.35', 'nistk163' => '1.3.132.0.1', 'nistp192' => '1.2.840.10045.3.1.1', 'nistp224' => '1.3.132.0.33', 'nistk233' => '1.3.132.0.26', 'nistb233' => '1.3.132.0.27', 'nistk283' => '1.3.132.0.16', 'nistk409' => '1.3.132.0.36', 'nistb409' => '1.3.132.0.37', 'nistt571' => '1.3.132.0.38', 'secp192r1' => '1.2.840.10045.3.1.1', 'sect163k1' => '1.3.132.0.1', 'sect163r2' => '1.3.132.0.15', 'secp224r1' => '1.3.132.0.33', 'sect233k1' => '1.3.132.0.26', 'sect233r1' => '1.3.132.0.27', 'secp256r1' => '1.2.840.10045.3.1.7', 'sect283k1' => '1.3.132.0.16', 'sect283r1' => '1.3.132.0.17', 'secp384r1' => '1.3.132.0.34', 'sect409k1' => '1.3.132.0.36', 'sect409r1' => '1.3.132.0.37', 'secp521r1' => '1.3.132.0.35', 'sect571k1' => '1.3.132.0.38', 'sect571r1' => '1.3.132.0.39', 'secp112r1' => '1.3.132.0.6', 'secp112r2' => '1.3.132.0.7', 'secp128r1' => '1.3.132.0.28', 'secp128r2' => '1.3.132.0.29', 'secp160k1' => '1.3.132.0.9', 'secp160r1' => '1.3.132.0.8', 'secp160r2' => '1.3.132.0.30', 'secp192k1' => '1.3.132.0.31', 'secp224k1' => '1.3.132.0.32', 'secp256k1' => '1.3.132.0.10', 'sect113r1' => '1.3.132.0.4', 'sect113r2' => '1.3.132.0.5', 'sect131r1' => '1.3.132.0.22', 'sect131r2' => '1.3.132.0.23', 'sect163r1' => '1.3.132.0.2', 'sect193r1' => '1.3.132.0.24', 'sect193r2' => '1.3.132.0.25', 'sect239k1' => '1.3.132.0.3', 'brainpoolP160r1' => '1.3.36.3.3.2.8.1.1.1', 'brainpoolP160t1' => '1.3.36.3.3.2.8.1.1.2', 'brainpoolP192r1' => '1.3.36.3.3.2.8.1.1.3', 'brainpoolP192t1' => '1.3.36.3.3.2.8.1.1.4', 'brainpoolP224r1' => '1.3.36.3.3.2.8.1.1.5', 'brainpoolP224t1' => '1.3.36.3.3.2.8.1.1.6', 'brainpoolP256r1' => '1.3.36.3.3.2.8.1.1.7', 'brainpoolP256t1' => '1.3.36.3.3.2.8.1.1.8', 'brainpoolP320r1' => '1.3.36.3.3.2.8.1.1.9', 'brainpoolP320t1' => '1.3.36.3.3.2.8.1.1.10', 'brainpoolP384r1' => '1.3.36.3.3.2.8.1.1.11', 'brainpoolP384t1' => '1.3.36.3.3.2.8.1.1.12', 'brainpoolP512r1' => '1.3.36.3.3.2.8.1.1.13', 'brainpoolP512t1' => '1.3.36.3.3.2.8.1.1.14'];
            ASN1::loadOIDs(['prime-field' => '1.2.840.10045.1.1', 'characteristic-two-field' => '1.2.840.10045.1.2', 'characteristic-two-basis' => '1.2.840.10045.1.2.3', 'gnBasis' => '1.2.840.10045.1.2.3.1', 'tpBasis' => '1.2.840.10045.1.2.3.2', 'ppBasis' => '1.2.840.10045.1.2.3.3'] + self::$curveOIDs);
        }
    }
    /**
     * @param BaseCurve $curve
     */
    public static function setImplicitCurve($curve)
    {
        self::$implicitCurve = $curve;
    }
    /**
     * @param mixed[] $params
     */
    protected static function loadCurveByParam($params)
    {
        if (count($params) > 1) {
            throw new RuntimeException('No parameters are present');
        }
        if (isset($params['namedCurve'])) {
            $curve = 'Staatic\Vendor\phpseclib3\Crypt\EC\Curves\\' . $params['namedCurve'];
            if (!class_exists($curve)) {
                throw new UnsupportedCurveException('Named Curve of ' . $params['namedCurve'] . ' is not supported');
            }
            return new $curve();
        }
        if (isset($params['implicitCurve'])) {
            if (!isset(self::$implicitCurve)) {
                throw new RuntimeException('Implicit curves can be provided by calling setImplicitCurve');
            }
            return self::$implicitCurve;
        }
        if (isset($params['specifiedCurve'])) {
            $data = $params['specifiedCurve'];
            switch ($data['fieldID']['fieldType']) {
                case 'prime-field':
                    $curve = new PrimeCurve();
                    $curve->setModulo($data['fieldID']['parameters']);
                    $curve->setCoefficients(new BigInteger($data['curve']['a'], 256), new BigInteger($data['curve']['b'], 256));
                    $point = self::extractPoint("\x00" . $data['base'], $curve);
                    $curve->setBasePoint(...$point);
                    $curve->setOrder($data['order']);
                    return $curve;
                case 'characteristic-two-field':
                    $curve = new BinaryCurve();
                    $params = ASN1::decodeBER($data['fieldID']['parameters']);
                    $params = ASN1::asn1map($params[0], Characteristic_two::MAP);
                    $modulo = [(int) $params['m']->toString()];
                    switch ($params['basis']) {
                        case 'tpBasis':
                            $modulo[] = (int) $params['parameters']->toString();
                            break;
                        case 'ppBasis':
                            $temp = ASN1::decodeBER($params['parameters']);
                            $temp = ASN1::asn1map($temp[0], Pentanomial::MAP);
                            $modulo[] = (int) $temp['k3']->toString();
                            $modulo[] = (int) $temp['k2']->toString();
                            $modulo[] = (int) $temp['k1']->toString();
                    }
                    $modulo[] = 0;
                    $curve->setModulo(...$modulo);
                    $len = ceil($modulo[0] / 8);
                    $curve->setCoefficients(Strings::bin2hex($data['curve']['a']), Strings::bin2hex($data['curve']['b']));
                    $point = self::extractPoint("\x00" . $data['base'], $curve);
                    $curve->setBasePoint(...$point);
                    $curve->setOrder($data['order']);
                    return $curve;
                default:
                    throw new UnsupportedCurveException('Field Type of ' . $data['fieldID']['fieldType'] . ' is not supported');
            }
        }
        throw new RuntimeException('No valid parameters are present');
    }
    /**
     * @param BaseCurve $curve
     */
    public static function extractPoint($str, $curve)
    {
        if ($curve instanceof TwistedEdwardsCurve) {
            $y = $str;
            $y = strrev($y);
            $sign = (bool) (ord($y[0]) & 0x80);
            $y[0] = $y[0] & chr(0x7f);
            $y = new BigInteger($y, 256);
            if ($y->compare($curve->getModulo()) >= 0) {
                throw new RuntimeException('The Y coordinate should not be >= the modulo');
            }
            $point = $curve->recoverX($y, $sign);
            if (!$curve->verifyPoint($point)) {
                throw new RuntimeException('Unable to verify that point exists on curve');
            }
            return $point;
        }
        if (($val = Strings::shift($str)) != "\x00") {
            throw new UnexpectedValueException('extractPoint expects the first byte to be null - not ' . Strings::bin2hex($val));
        }
        if ($str == "\x00") {
            return [];
        }
        $keylen = strlen($str);
        $order = $curve->getLengthInBytes();
        if ($keylen == $order + 1) {
            return $curve->derivePoint($str);
        }
        if ($keylen == 2 * $order + 1) {
            preg_match("#(.)(.{{$order}})(.{{$order}})#s", $str, $matches);
            list(, $w, $x, $y) = $matches;
            if ($w != "\x04") {
                throw new UnexpectedValueException('The first byte of an uncompressed point should be 04 - not ' . Strings::bin2hex($val));
            }
            $point = [$curve->convertInteger(new BigInteger($x, 256)), $curve->convertInteger(new BigInteger($y, 256))];
            if (!$curve->verifyPoint($point)) {
                throw new RuntimeException('Unable to verify that point exists on curve');
            }
            return $point;
        }
        throw new UnexpectedValueException('The string representation of the points is not of an appropriate length');
    }
    /**
     * @param mixed $curve
     */
    private static function encodeParameters($curve, $returnArray = \false, array $options = [])
    {
        $useNamedCurves = isset($options['namedCurve']) ? $options['namedCurve'] : self::$useNamedCurves;
        $reflect = new ReflectionClass($curve);
        $name = $reflect->getShortName();
        if ($useNamedCurves) {
            if (isset(self::$curveOIDs[$name])) {
                if ($reflect->isFinal()) {
                    $reflect = $reflect->getParentClass();
                    $name = $reflect->getShortName();
                }
                return $returnArray ? ['namedCurve' => $name] : ASN1::encodeDER(['namedCurve' => $name], ECParameters::MAP);
            }
            foreach (new DirectoryIterator(__DIR__ . '/../../Curves/') as $file) {
                if ($file->getExtension() != 'php') {
                    continue;
                }
                $testName = $file->getBasename('.php');
                $class = 'Staatic\Vendor\phpseclib3\Crypt\EC\Curves\\' . $testName;
                $reflect = new ReflectionClass($class);
                if ($reflect->isFinal()) {
                    continue;
                }
                $candidate = new $class();
                switch ($name) {
                    case 'Prime':
                        if (!$candidate instanceof PrimeCurve) {
                            break;
                        }
                        if (!$candidate->getModulo()->equals($curve->getModulo())) {
                            break;
                        }
                        if ($candidate->getA()->toBytes() != $curve->getA()->toBytes()) {
                            break;
                        }
                        if ($candidate->getB()->toBytes() != $curve->getB()->toBytes()) {
                            break;
                        }
                        list($candidateX, $candidateY) = $candidate->getBasePoint();
                        list($curveX, $curveY) = $curve->getBasePoint();
                        if ($candidateX->toBytes() != $curveX->toBytes()) {
                            break;
                        }
                        if ($candidateY->toBytes() != $curveY->toBytes()) {
                            break;
                        }
                        return $returnArray ? ['namedCurve' => $testName] : ASN1::encodeDER(['namedCurve' => $testName], ECParameters::MAP);
                    case 'Binary':
                        if (!$candidate instanceof BinaryCurve) {
                            break;
                        }
                        if ($candidate->getModulo() != $curve->getModulo()) {
                            break;
                        }
                        if ($candidate->getA()->toBytes() != $curve->getA()->toBytes()) {
                            break;
                        }
                        if ($candidate->getB()->toBytes() != $curve->getB()->toBytes()) {
                            break;
                        }
                        list($candidateX, $candidateY) = $candidate->getBasePoint();
                        list($curveX, $curveY) = $curve->getBasePoint();
                        if ($candidateX->toBytes() != $curveX->toBytes()) {
                            break;
                        }
                        if ($candidateY->toBytes() != $curveY->toBytes()) {
                            break;
                        }
                        return $returnArray ? ['namedCurve' => $testName] : ASN1::encodeDER(['namedCurve' => $testName], ECParameters::MAP);
                }
            }
        }
        $order = $curve->getOrder();
        if (!$order) {
            throw new RuntimeException('Specified Curves need the order to be specified');
        }
        $point = $curve->getBasePoint();
        $x = $point[0]->toBytes();
        $y = $point[1]->toBytes();
        if ($curve instanceof PrimeCurve) {
            $data = ['version' => 'ecdpVer1', 'fieldID' => ['fieldType' => 'prime-field', 'parameters' => $curve->getModulo()], 'curve' => ['a' => $curve->getA()->toBytes(), 'b' => $curve->getB()->toBytes()], 'base' => "\x04" . $x . $y, 'order' => $order];
            return $returnArray ? ['specifiedCurve' => $data] : ASN1::encodeDER(['specifiedCurve' => $data], ECParameters::MAP);
        }
        if ($curve instanceof BinaryCurve) {
            $modulo = $curve->getModulo();
            $basis = count($modulo);
            $m = array_shift($modulo);
            array_pop($modulo);
            switch ($basis) {
                case 3:
                    $basis = 'tpBasis';
                    $modulo = new BigInteger($modulo[0]);
                    break;
                case 5:
                    $basis = 'ppBasis';
                    $modulo = ['k1' => new BigInteger($modulo[2]), 'k2' => new BigInteger($modulo[1]), 'k3' => new BigInteger($modulo[0])];
                    $modulo = ASN1::encodeDER($modulo, Pentanomial::MAP);
                    $modulo = new Element($modulo);
            }
            $params = ASN1::encodeDER(['m' => new BigInteger($m), 'basis' => $basis, 'parameters' => $modulo], Characteristic_two::MAP);
            $params = new Element($params);
            $a = ltrim($curve->getA()->toBytes(), "\x00");
            if (!strlen($a)) {
                $a = "\x00";
            }
            $b = ltrim($curve->getB()->toBytes(), "\x00");
            if (!strlen($b)) {
                $b = "\x00";
            }
            $data = ['version' => 'ecdpVer1', 'fieldID' => ['fieldType' => 'characteristic-two-field', 'parameters' => $params], 'curve' => ['a' => $a, 'b' => $b], 'base' => "\x04" . $x . $y, 'order' => $order];
            return $returnArray ? ['specifiedCurve' => $data] : ASN1::encodeDER(['specifiedCurve' => $data], ECParameters::MAP);
        }
        throw new UnsupportedCurveException('Curve cannot be serialized');
    }
    public static function useSpecifiedCurve()
    {
        self::$useNamedCurves = \false;
    }
    public static function useNamedCurve()
    {
        self::$useNamedCurves = \true;
    }
}
