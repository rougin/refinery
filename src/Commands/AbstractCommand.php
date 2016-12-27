<?php

namespace Rougin\Refinery\Commands;

use Rougin\Describe\Describe;
use League\Flysystem\Filesystem;

/**
 * Abstract Command
 *
 * @package Refinery
 * @author  Rougin Royce Gutib <rougingutib@gmail.com>
 */
abstract class AbstractCommand extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \CI_Controller
     */
    protected $codeigniter;

    /**
     * @var \Rougin\Describe\Describe
     */
    protected $describe;

    /**
     * @var \League\Flysystem\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Twig_Environment
     */
    protected $renderer;

    /**
     * @param \Rougin\Describe\Describe    $describe
     * @param \League\Flysystem\Filesystem $filesystem
     * @param \Twig_Environment            $renderer
     */
    public function __construct(\CI_Controller $codeigniter, Describe $describe, Filesystem $filesystem, \Twig_Environment $renderer)
    {
        parent::__construct();

        $this->codeigniter = $codeigniter;
        $this->describe    = $describe;
        $this->filesystem  = $filesystem;
        $this->renderer    = $renderer;
    }
}