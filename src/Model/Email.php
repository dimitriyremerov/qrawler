<?php

namespace Qrawler\Model;

use Qrawler\Model\Base\Email as BaseEmail;

/**
 * Skeleton subclass for representing a row from the 'emails' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Email extends BaseEmail
{
    public function __construct(string $emailVal = null, Url $url = null, Result $result = null)
    {
        if (isset($emailVal)) {
            $this->setEmail($emailVal);
        }
        if (isset($url)) {
            $this->setUrl($url);
        }
        if (isset($result)) {
            $this->setResult($result);
        }
        parent::__construct();
    }
}
