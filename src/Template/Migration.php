<?php

namespace Rougin\Refinery\Template;

use Rougin\Classidy\Classidy;
use Rougin\Classidy\Method;

/**
 * @package Refinery
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Migration extends Classidy
{
    const TYPE_SEQUENCE = 0;

    const TYPE_TIMESTAMP = 1;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        // Converts the migration name into snake_case ---
        /** @var string */
        $name = preg_replace('/[\s]+/', '_', trim($name));

        $this->name = 'Migration_' . $name;
        // -----------------------------------------------
    }

    /**
     * @return self
     */
    public function init()
    {
        $this->extendsTo('Rougin\Refinery\Migration');

        $this->setUpMethod();

        $this->setDownMethod();

        return $this;
    }

    /**
     * @return void
     */
    protected function setDownMethod()
    {
        $method = new Method('down');

        $method->setReturn('void');

        $this->addMethod($method);
    }

    /**
     * @return void
     */
    protected function setUpMethod()
    {
        $method = new Method('up');

        $method->setReturn('void');

        $this->addMethod($method);
    }
}
