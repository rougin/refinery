<?php

namespace Rougin\Refinery\Commands;

use CI_Controller;
use Twig_Environment;
use Symfony\Component\Console\Command\Command;

use Rougin\Describe\Describe;

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
     * @var \CI_Controller
     */
    protected $codeigniter;

    /**
     * @param \CI_Controller            $controller
     * @param \Rougin\Describe\Describe $describe
     * @param \Twig_Environment         $renderer
     */
    public function __construct(CI_Controller $codeigniter, Describe $describe, Twig_Environment $renderer)
    {
        parent::__construct();

        $this->codeigniter = $codeigniter;
        $this->describe = $describe;
        $this->renderer = $renderer;
    }

    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * @return bool
     */
    public function isEnabled()
    {
        $migrations = glob(APPPATH . 'migrations/*.php');

        return count($migrations) > 0;
    }
}
