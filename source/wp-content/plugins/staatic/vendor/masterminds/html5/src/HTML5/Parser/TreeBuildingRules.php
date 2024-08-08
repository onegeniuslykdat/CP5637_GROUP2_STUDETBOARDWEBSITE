<?php

namespace Staatic\Vendor\Masterminds\HTML5\Parser;

class TreeBuildingRules
{
    protected static $tags = array('li' => 1, 'dd' => 1, 'dt' => 1, 'rt' => 1, 'rp' => 1, 'tr' => 1, 'th' => 1, 'td' => 1, 'thead' => 1, 'tfoot' => 1, 'tbody' => 1, 'table' => 1, 'optgroup' => 1, 'option' => 1);
    public function hasRules($tagname)
    {
        return isset(static::$tags[$tagname]);
    }
    public function evaluate($new, $current)
    {
        switch ($new->tagName) {
            case 'li':
                return $this->handleLI($new, $current);
            case 'dt':
            case 'dd':
                return $this->handleDT($new, $current);
            case 'rt':
            case 'rp':
                return $this->handleRT($new, $current);
            case 'optgroup':
                return $this->closeIfCurrentMatches($new, $current, array('optgroup'));
            case 'option':
                return $this->closeIfCurrentMatches($new, $current, array('option'));
            case 'tr':
                return $this->closeIfCurrentMatches($new, $current, array('tr'));
            case 'td':
            case 'th':
                return $this->closeIfCurrentMatches($new, $current, array('th', 'td'));
            case 'tbody':
            case 'thead':
            case 'tfoot':
            case 'table':
                return $this->closeIfCurrentMatches($new, $current, array('thead', 'tfoot', 'tbody'));
        }
        return $current;
    }
    protected function handleLI($ele, $current)
    {
        return $this->closeIfCurrentMatches($ele, $current, array('li'));
    }
    protected function handleDT($ele, $current)
    {
        return $this->closeIfCurrentMatches($ele, $current, array('dt', 'dd'));
    }
    protected function handleRT($ele, $current)
    {
        return $this->closeIfCurrentMatches($ele, $current, array('rt', 'rp'));
    }
    protected function closeIfCurrentMatches($ele, $current, $match)
    {
        if (in_array($current->tagName, $match, \true)) {
            $current->parentNode->appendChild($ele);
        } else {
            $current->appendChild($ele);
        }
        return $ele;
    }
}
