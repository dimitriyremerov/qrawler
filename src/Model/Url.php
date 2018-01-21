<?php

namespace Qrawler\Model;

use Qrawler\Model\Base\Url as BaseUrl;

/**
 * Skeleton subclass for representing a row from the 'urls' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Url extends BaseUrl
{
    public function __construct(string $urlVal = null, Result $result = null)
    {
        if (isset($urlVal)) {
            $this->setUrl($urlVal);
        }
        if (isset($result)) {
            $this->setResult($result);
        }
        parent::__construct();
    }

}
