<?php

namespace Rougin\Refinery;

class RefineryTest extends \Rougin\Refinery\TestCase
{
    /**
     * Tests if the initial commands exists.
     *
     * @return void
     */
    public function testCommandsExist()
    {
        $this->setDefaults();

        $application = $this->getApplication();

        $this->assertTrue($application->has('create'));
    }
}
