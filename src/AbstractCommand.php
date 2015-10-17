<?php

namespace Rougin\Refinery;

use Rougin\Describe\Describe;
use Symfony\Component\Console\Command\Command;
use Twig_Environment;
use CI_Controller;

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
    protected $codeigniter;
    protected $describe;
    protected $renderer;

    /**
     * @param Instance         $codeigniter
     * @param Describe         $describe
     * @param Twig_Environment $renderer
     */
    public function __construct(
        CI_Controller $codeigniter,
        Describe $describe,
        Twig_Environment $renderer
    ) {
        parent::__construct();

        $this->codeigniter = $codeigniter;
        $this->describe = $describe;
        $this->renderer = $renderer;
    }
}
