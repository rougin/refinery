<?php

namespace Rougin\Refinery;

use Twig_Environment;
use Rougin\Describe\Describe;
use Symfony\Component\Console\Command\Command;

/**
 * Abstract Command
 *
 * Extends the Symfony\Console\Command class with Twig's renderer,
 * CodeIgniter's instance and Describe.
 * 
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
abstract class AbstractCommand extends Command
{
    /**
     * @var \Rougin\Describe\Describe
     */
    protected $describe;

    /**
     * @var \Twig_Environment
     */
    protected $renderer;

    /**
     * @param \Rougin\Describe\Describe $describe
     * @param \Twig_Environment         $renderer
     */
    public function __construct(Describe $describe, Twig_Environment $renderer)
    {
        parent::__construct();

        $this->describe = $describe;
        $this->renderer = $renderer;
    }
}
