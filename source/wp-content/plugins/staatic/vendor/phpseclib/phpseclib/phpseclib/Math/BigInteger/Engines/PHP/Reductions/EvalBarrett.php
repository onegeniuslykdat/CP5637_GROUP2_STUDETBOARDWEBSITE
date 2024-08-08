<?php

namespace Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP\Reductions;

use Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP;
use Staatic\Vendor\phpseclib3\Math\BigInteger\Engines\PHP\Base;
abstract class EvalBarrett extends Base
{
    private static $custom_reduction;
    /**
     * @param mixed[] $n
     * @param mixed[] $m
     */
    protected static function reduce($n, $m, $class)
    {
        $inline = self::$custom_reduction;
        return $inline($n);
    }
    /**
     * @param PHP $m
     */
    protected static function generateCustomReduction($m, $class)
    {
        $m_length = count($m->value);
        if ($m_length < 5) {
            $code = '
                $lhs = new ' . $class . '();
                $lhs->value = $x;
                $rhs = new ' . $class . '();
                $rhs->value = [' . implode(',', array_map(self::class . '::float2string', $m->value)) . '];
                list(, $temp) = $lhs->divide($rhs);
                return $temp->value;
            ';
            eval('$func = function ($x) { ' . $code . '};');
            self::$custom_reduction = $func;
            return $func;
        }
        $lhs = new $class();
        $lhs_value =& $lhs->value;
        $lhs_value = self::array_repeat(0, $m_length + ($m_length >> 1));
        $lhs_value[] = 1;
        $rhs = new $class();
        list($u, $m1) = $lhs->divide($m);
        if ($class::BASE != 26) {
            $u = $u->value;
        } else {
            $lhs_value = self::array_repeat(0, 2 * $m_length);
            $lhs_value[] = 1;
            $rhs = new $class();
            list($u) = $lhs->divide($m);
            $u = $u->value;
        }
        $m = $m->value;
        $m1 = $m1->value;
        $cutoff = count($m) + (count($m) >> 1);
        $code = '
            if (count($n) >= ' . 2 * count($m) . ') {
                $lhs = new ' . $class . '();
                $rhs = new ' . $class . '();
                $lhs->value = $n;
                $rhs->value = [' . implode(',', array_map(self::class . '::float2string', $m)) . '];
                list(, $temp) = $lhs->divide($rhs);
                return $temp->value;
            }

            $lsd = array_slice($n, 0, ' . $cutoff . ');
            $msd = array_slice($n, ' . $cutoff . ');';
        $code .= self::generateInlineTrim('msd');
        $code .= self::generateInlineMultiply('msd', $m1, 'temp', $class);
        $code .= self::generateInlineAdd('lsd', 'temp', 'n', $class);
        $code .= '$temp = array_slice($n, ' . (count($m) - 1) . ');';
        $code .= self::generateInlineMultiply('temp', $u, 'temp2', $class);
        $code .= self::generateInlineTrim('temp2');
        $code .= ($class::BASE == 26) ? '$temp = array_slice($temp2, ' . (count($m) + 1) . ');' : ('$temp = array_slice($temp2, ' . ((count($m) >> 1) + 1) . ');');
        $code .= self::generateInlineMultiply('temp', $m, 'temp2', $class);
        $code .= self::generateInlineTrim('temp2');
        $code .= self::generateInlineSubtract2('n', 'temp2', 'temp', $class);
        $subcode = self::generateInlineSubtract1('temp', $m, 'temp2', $class);
        $subcode .= '$temp = $temp2;';
        $code .= self::generateInlineCompare($m, 'temp', $subcode);
        $code .= 'return $temp;';
        eval('$func = function ($n) { ' . $code . '};');
        self::$custom_reduction = $func;
        return $func;
    }
    private static function generateInlineTrim($name)
    {
        return '
            for ($i = count($' . $name . ') - 1; $i >= 0; --$i) {
                if ($' . $name . '[$i]) {
                    break;
                }
                unset($' . $name . '[$i]);
            }';
    }
    private static function generateInlineMultiply($input, array $arr, $output, $class)
    {
        if (!count($arr)) {
            return 'return [];';
        }
        $regular = '
            $length = count($' . $input . ');
            if (!$length) {
                $' . $output . ' = [];
            }else{
            $' . $output . ' = array_fill(0, $length + ' . count($arr) . ', 0);
            $carry = 0;';
        for ($i = 0; $i < count($arr); $i++) {
            $regular .= '
                $subtemp = $' . $input . '[0] * ' . $arr[$i];
            $regular .= $i ? ' + $carry;' : ';';
            $regular .= '$carry = ';
            $regular .= ($class::BASE === 26) ? 'intval($subtemp / 0x4000000);' : '$subtemp >> 31;';
            $regular .= '$' . $output . '[' . $i . '] = ';
            if ($class::BASE === 26) {
                $regular .= '(int) (';
            }
            $regular .= '$subtemp - ' . $class::BASE_FULL . ' * $carry';
            $regular .= ($class::BASE === 26) ? ');' : ';';
        }
        $regular .= '$' . $output . '[' . count($arr) . '] = $carry;';
        $regular .= '
            for ($i = 1; $i < $length; ++$i) {';
        for ($j = 0; $j < count($arr); $j++) {
            $regular .= $j ? '$k++;' : '$k = $i;';
            $regular .= '
                $subtemp = $' . $output . '[$k] + $' . $input . '[$i] * ' . $arr[$j];
            $regular .= $j ? ' + $carry;' : ';';
            $regular .= '$carry = ';
            $regular .= ($class::BASE === 26) ? 'intval($subtemp / 0x4000000);' : '$subtemp >> 31;';
            $regular .= '$' . $output . '[$k] = ';
            if ($class::BASE === 26) {
                $regular .= '(int) (';
            }
            $regular .= '$subtemp - ' . $class::BASE_FULL . ' * $carry';
            $regular .= ($class::BASE === 26) ? ');' : ';';
        }
        $regular .= '$' . $output . '[++$k] = $carry; $carry = 0;';
        $regular .= '}}';
        return $regular;
    }
    private static function generateInlineAdd($x, $y, $result, $class)
    {
        $code = '
            $length = max(count($' . $x . '), count($' . $y . '));
            $' . $result . ' = array_pad($' . $x . ', $length + 1, 0);
            $_' . $y . ' = array_pad($' . $y . ', $length, 0);
            $carry = 0;
            for ($i = 0, $j = 1; $j < $length; $i+=2, $j+=2) {
                $sum = ($' . $result . '[$j] + $_' . $y . '[$j]) * ' . $class::BASE_FULL . '
                           + $' . $result . '[$i] + $_' . $y . '[$i] +
                           $carry;
                $carry = $sum >= ' . self::float2string($class::MAX_DIGIT2) . ';
                $sum = $carry ? $sum - ' . self::float2string($class::MAX_DIGIT2) . ' : $sum;';
        $code .= ($class::BASE === 26) ? '$upper = intval($sum / 0x4000000); $' . $result . '[$i] = (int) ($sum - ' . $class::BASE_FULL . ' * $upper);' : ('$upper = $sum >> 31; $' . $result . '[$i] = $sum - ' . $class::BASE_FULL . ' * $upper;');
        $code .= '
                $' . $result . '[$j] = $upper;
            }
            if ($j == $length) {
                $sum = $' . $result . '[$i] + $_' . $y . '[$i] + $carry;
                $carry = $sum >= ' . self::float2string($class::BASE_FULL) . ';
                $' . $result . '[$i] = $carry ? $sum - ' . self::float2string($class::BASE_FULL) . ' : $sum;
                ++$i;
            }
            if ($carry) {
                for (; $' . $result . '[$i] == ' . $class::MAX_DIGIT . '; ++$i) {
                    $' . $result . '[$i] = 0;
                }
                ++$' . $result . '[$i];
            }';
        $code .= self::generateInlineTrim($result);
        return $code;
    }
    private static function generateInlineSubtract2($known, $unknown, $result, $class)
    {
        $code = '
            $' . $result . ' = $' . $known . ';
            $carry = 0;
            $size = count($' . $unknown . ');
            for ($i = 0, $j = 1; $j < $size; $i+= 2, $j+= 2) {
                $sum = ($' . $known . '[$j] - $' . $unknown . '[$j]) * ' . $class::BASE_FULL . ' + $' . $known . '[$i]
                    - $' . $unknown . '[$i]
                    - $carry;
                $carry = $sum < 0;
                if ($carry) {
                    $sum+= ' . self::float2string($class::MAX_DIGIT2) . ';
                }
                $subtemp = ';
        $code .= ($class::BASE === 26) ? 'intval($sum / 0x4000000);' : '$sum >> 31;';
        $code .= '$' . $result . '[$i] = ';
        if ($class::BASE === 26) {
            $code .= '(int) (';
        }
        $code .= '$sum - ' . $class::BASE_FULL . ' * $subtemp';
        if ($class::BASE === 26) {
            $code .= ')';
        }
        $code .= ';
                $' . $result . '[$j] = $subtemp;
            }
            if ($j == $size) {
                $sum = $' . $known . '[$i] - $' . $unknown . '[$i] - $carry;
                $carry = $sum < 0;
                $' . $result . '[$i] = $carry ? $sum + ' . $class::BASE_FULL . ' : $sum;
                ++$i;
            }

            if ($carry) {
                for (; !$' . $result . '[$i]; ++$i) {
                    $' . $result . '[$i] = ' . $class::MAX_DIGIT . ';
                }
                --$' . $result . '[$i];
            }';
        $code .= self::generateInlineTrim($result);
        return $code;
    }
    private static function generateInlineSubtract1($unknown, array $known, $result, $class)
    {
        $code = '$' . $result . ' = $' . $unknown . ';';
        for ($i = 0, $j = 1; $j < count($known); $i += 2, $j += 2) {
            $code .= '$sum = $' . $unknown . '[' . $j . '] * ' . $class::BASE_FULL . ' + $' . $unknown . '[' . $i . '] - ';
            $code .= self::float2string($known[$j] * $class::BASE_FULL + $known[$i]);
            if ($i != 0) {
                $code .= ' - $carry';
            }
            $code .= ';
                if ($carry = $sum < 0) {
                    $sum+= ' . self::float2string($class::MAX_DIGIT2) . ';
                }
                $subtemp = ';
            $code .= ($class::BASE === 26) ? 'intval($sum / 0x4000000);' : '$sum >> 31;';
            $code .= '
                $' . $result . '[' . $i . '] = ';
            if ($class::BASE === 26) {
                $code .= ' (int) (';
            }
            $code .= '$sum - ' . $class::BASE_FULL . ' * $subtemp';
            if ($class::BASE === 26) {
                $code .= ')';
            }
            $code .= ';
                $' . $result . '[' . $j . '] = $subtemp;';
        }
        $code .= '$i = ' . $i . ';';
        if ($j == count($known)) {
            $code .= '
                $sum = $' . $unknown . '[' . $i . '] - ' . $known[$i] . ' - $carry;
                $carry = $sum < 0;
                $' . $result . '[' . $i . '] = $carry ? $sum + ' . $class::BASE_FULL . ' : $sum;
                ++$i;';
        }
        $code .= '
            if ($carry) {
                for (; !$' . $result . '[$i]; ++$i) {
                    $' . $result . '[$i] = ' . $class::MAX_DIGIT . ';
                }
                --$' . $result . '[$i];
            }';
        $code .= self::generateInlineTrim($result);
        return $code;
    }
    private static function generateInlineCompare(array $known, $unknown, $subcode)
    {
        $uniqid = uniqid();
        $code = 'loop_' . $uniqid . ':
            $clength = count($' . $unknown . ');
            switch (true) {
                case $clength < ' . count($known) . ':
                    goto end_' . $uniqid . ';
                case $clength > ' . count($known) . ':';
        for ($i = count($known) - 1; $i >= 0; $i--) {
            $code .= '
                case $' . $unknown . '[' . $i . '] > ' . $known[$i] . ':
                    goto subcode_' . $uniqid . ';
                case $' . $unknown . '[' . $i . '] < ' . $known[$i] . ':
                    goto end_' . $uniqid . ';';
        }
        $code .= '
                default:
                    // do subcode
            }

            subcode_' . $uniqid . ':' . $subcode . '
            goto loop_' . $uniqid . ';

            end_' . $uniqid . ':';
        return $code;
    }
    private static function float2string($num)
    {
        if (!is_float($num)) {
            return (string) $num;
        }
        if ($num < 0) {
            return '-' . self::float2string(abs($num));
        }
        $temp = '';
        while ($num) {
            $temp = fmod($num, 10) . $temp;
            $num = floor($num / 10);
        }
        return $temp;
    }
}
