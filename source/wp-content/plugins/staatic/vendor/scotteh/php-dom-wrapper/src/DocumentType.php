<?php

declare (strict_types=1);
namespace Staatic\Vendor\DOMWrap;

use DOMDocumentType;
use Staatic\Vendor\DOMWrap\Traits\NodeTrait;
use Staatic\Vendor\DOMWrap\Traits\CommonTrait;
use Staatic\Vendor\DOMWrap\Traits\TraversalTrait;
use Staatic\Vendor\DOMWrap\Traits\ManipulationTrait;
class DocumentType extends DOMDocumentType
{
    use CommonTrait;
    use NodeTrait;
    use TraversalTrait;
    use ManipulationTrait;
}
