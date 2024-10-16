<?php

namespace Rougin\Refinery;

use Rougin\Refinery\Template\Migration;

/**
 * @package Refinery
 *
 * @author  Rougin Gutib <rougingutib@gmail.com>
 */
class Parser
{
    /**
     * @var string|null
     */
    protected $column = null;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $table;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $allowed = implode('|', $this->getTypes());

        $pattern = '/(' . $allowed . ')_(.*)_table/';

        preg_match($pattern, $name, $matches);

        $this->table = $matches[2];

        $this->type = $matches[1];

        $pattern = '/(.*)_in_(.*)/';

        preg_match($pattern, $matches[2], $matches);

        if (isset($matches[0]))
        {
            $this->column = $matches[1];

            $this->table = $matches[2];
        }
    }

    /**
     * @return string|null
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return boolean
     */
    public function isCreateColumn()
    {
        return ($this->getType() === Migration::TYPE_ADD || $this->getType() === Migration::TYPE_CREATE) && $this->getColumn() !== null;
    }

    /**
     * @return boolean
     */
    public function isCreateTable()
    {
        return $this->getType() === Migration::TYPE_CREATE && $this->getColumn() === null;
    }

    /**
     * @return string[]
     */
    protected function getTypes()
    {
        $items = array(Migration::TYPE_ADD);

        $items[] = Migration::TYPE_CREATE;
        $items[] = Migration::TYPE_DELETE;
        $items[] = Migration::TYPE_MODIFY;
        $items[] = Migration::TYPE_REMOVE;
        $items[] = Migration::TYPE_UPDATE;

        return $items;
    }
}
