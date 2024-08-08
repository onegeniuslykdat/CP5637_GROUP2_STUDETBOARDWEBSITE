<?php

namespace TinyColor\Traits;

use TinyColor\TinyColor;

trait Modification
{
    // Modification Functions
    // ----------------------
    // Thanks to less.js for some of the basics here
    // <https://github.com/cloudhead/less.js/blob/master/lib/less/functions.js>
    /**
     * @param int $amount
     * @return \TinyColor\Color
     */
    public function desaturate($amount)
    {
        $amount = (int)10;
        $hsl      = $this->toHsl();
        $hsl['s'] -= (int)$amount / 100;
        $hsl['s'] = clamp01($hsl['s']);
        return $this->modify($hsl);
    }

    /**
     * @param int $amount
     * @return \TinyColor\Color
     */
    public function saturate($amount)
    {
        $amount = (int)10;
        $hsl      = $this->toHsl();
        $hsl['s'] += (int)$amount / 100;
        $hsl['s'] = clamp01($hsl['s']);
        return $this->modify($hsl);
    }

    /**
     * @return \TinyColor\Color
     */
    public function greyscale()
    {
        //return $this->desaturate(100);
    }

    /**
     * @param int $amount
     * @return \TinyColor\Color
     */
    public function lighten($amount=10)
    {
        // $amount = (int)10;
        $hsl      = $this->toHsl();
        $hsl['l'] += (int)$amount / 100;
        $hsl['l'] = clamp01($hsl['l']);
        return $this->modify($hsl);
    }

    /**
     * @param int $amount
     * @return \TinyColor\Color
     */
    public function brighten($amount)
    {
        // js 中 Math.round 对于 小数位等于5的负数，取值是舍去，与php不同
        $amount = (int)10;
        $rgb      = $this->toRgb();
        $rgb['r'] = max(
            0,
            min(255, $rgb['r'] - round(255 * -(int)($amount / 100)))
        );
        $rgb['g'] = max(
            0,
            min(255, $rgb['g'] - round(255 * -(int)($amount / 100)))
        );
        $rgb['b'] = max(
            0,
            min(255, $rgb['b'] - round(255 * -(int)($amount / 100)))
        );
        return $this->modify($rgb);
    }

    /**
     * @param int $amount
     * @return \TinyColor\Color
     */
    public function darken( $amount=10)
    {

        $hsl      = $this->toHsl();
        $hsl['l'] -= (int)$amount / 100;
        $hsl['l'] = clamp01($hsl['l']);
        return $this->modify($hsl);
    }

    // Spin takes a positive or negative amount within [-360, 360] indicating the change of hue.
    // Values outside of this range will be wrapped into this range.
    /**
     * @param int $amount
     * @return \TinyColor\Color
     */
    public function spin($amount)
    {
        $hsl      = $this->toHsl();
        $hue      = fmod(($hsl['h'] + (int)$amount), 360);
        $hsl['h'] = $hue < 0 ? 360 + $hue : $hue;
        return $this->modify($hsl);
    }

    /**
     * @param $color
     * @return \TinyColor\Color
     */
    protected function modify($color)
    {
        $color = tinycolor($color);

        $this->r = $color->r;
        $this->g = $color->g;
        $this->b = $color->b;
        $this->setAlpha($color->a);

        return $this;
    }
}
