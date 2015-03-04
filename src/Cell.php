<?php

class Cell {
    // this should probably be refactored to split the "is mine" property into a separate property
    public $value;
    public $isVisible = false;
    public $id;
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
    public function show() {
        $this->isVisible = true;
    }

}