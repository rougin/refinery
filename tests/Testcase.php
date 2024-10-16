<?php

namespace Rougin\Refinery;

use LegacyPHPUnit\TestCase as Legacy;

/**
 * @codeCoverageIgnore
 *
 * @package Refinery
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Testcase extends Legacy
{
    /** @phpstan-ignore-next-line */
    public function setExpectedException($exception)
    {
        if (method_exists($this, 'expectException'))
        {
            $this->expectException($exception);

            return;
        }

        /** @phpstan-ignore-next-line */
        parent::setExpectedException($exception);
    }
}
