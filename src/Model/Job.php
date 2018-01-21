<?php

namespace Qrawler\Model;

use Qrawler\Model\Base\Job as BaseJob;

/**
 * Skeleton subclass for representing a row from the 'jobs' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Job extends BaseJob
{
    const STATUS_NEW = -1;
    const STATUS_SUCCESS = 0;
    const STATUS_ERROR = 1;
    const STATUS_IN_PROGRESS = 2;

    public function __construct(string $input = null)
    {
        if (isset($input)) {
            $this->setInput($input);
        }
        $this->setStatus(self::STATUS_NEW);
        parent::__construct();
    }
}
