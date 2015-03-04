<?php

/**
 * Data container object to hold a single board cell.
 *
 * Class Cell
 */
class Cell {
    /**
     * Represents how many mines are adjacent to this cell.  Is also used to indicate whether the cell is a mine (-1)
     *
     * NOTE: this should probably be refactored to split the "is mine" property into a separate property
     *
     * @var int
     */
    public $value;

    /**
     * Whether or not the user can see the cell's value
     *
     * @var bool
     */
    public $isVisible = false;

    /**
     * uniqid value allowing the lookup of a particular cell object's coordinates in a given board
     * @var string
     */
    public $id;

    /**
     * @param $value
     * @throws Exception
     */
    public function __construct($value) {
        if (!is_int($value)) {
            throw new \Exception("Cell value not an integer: " . $value);
        }
        if ($value < -1 || $value > 8) {
            throw new \Exception("Invalid cell value: " . $value);
        }
        $this->value = $value;
        // uniqid only prevents collisions with microsecond precision - if this is being run on a super fast computer its
        // possible that cells would get a non-unique value... but let's deal with that when we need to scale up
        $this->id = uniqid("", true);
    }

    /**
     * Sets the visibility property to true
     */
    public function show() {
        $this->isVisible = true;
    }

}