<?php

require_once(__DIR__."/../src/Cell.php");

class CellTest extends PHPUnit_Framework_TestCase {

    public function setUp() {

    }

    public function testConstructorNonIntCheck() {
        $value = "abc";
        $this->setExpectedException("Exception", "Cell value not an integer: " . $value);
        new \Cell($value);
    }

    public function testConstructorInvalidValue() {
        $value = -125;
        $this->setExpectedException("Exception", "Invalid cell value: " . $value);
        new \Cell($value);
    }

    // this is a sort of weak test, but in case anyone messes with the id logic, this will hopefully let them know that they
    // have caused a collision
    public function testUniqueId() {
        $cell = new \Cell(0);
        $cell2 = new \Cell(0);
        $this->assertNotEquals($cell->id, $cell2->id);
    }

}