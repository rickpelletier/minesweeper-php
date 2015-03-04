<?php

require_once(__DIR__."/../src/Cell.php");
require_once(__DIR__."/../src/Board.php");
require_once(__DIR__."/../src/Game.php");

class BoardTest extends PHPUnit_Framework_TestCase {

    public function setUp() {

    }

    private function generateBoardData($boardSize) {
        // bootstrap some data
        $data = array();
        for($column = 0; $column < $boardSize; $column++) {
            $data[$column] = array();
            for ($row = 0; $row < $boardSize; $row++) {
                $data[$column][$row] = new \Cell(0);
            }
        }
        return $data;
    }

    public function testCalculateValuesAllZeros() {
        $boardSize = 4;
        $data = $this->generateBoardData($boardSize);

        $board = new \Board($data);
        $board->calculateValues();

        // confirm that all values are still 0
        for($column = 1; $column <= $boardSize; $column++) {
            $data[$column] = array();
            for ($row = 1; $row <= $boardSize; $row++) {
                $cell = $board->getCell($column, $row);
                $this->assertEquals($cell->value, 0);
            }
        }
    }

    public function testGetCell() {
        $boardSize = 6;
        $data = $this->generateBoardData($boardSize);
        $board = new \Board($data);

        $data[1][4] = new \Cell(-1);

        $board = new \Board($data);
        $this->assertEquals(-1, $board->getCell(2, 5)->value);

    }

    public function testGetCellInvalidColumnTooLarge() {
        $this->setExpectedException("Exception", "Invalid coordinate value: 2,5");
        // bootstrap a data file
        $boardSize = 3;
        $data = $this->generateBoardData($boardSize);
        $board = new \Board($data);
        $this->assertEquals(-1, $board->getCell(2, 5)->value);
    }
    public function testGetCellInvalidColumnTooSmall() {
        $this->setExpectedException("Exception", "Invalid coordinate value: 2,-1");
        // bootstrap a data file
        $boardSize = 3;
        $data = $this->generateBoardData($boardSize);
        $board = new \Board($data);
        $this->assertEquals(-1, $board->getCell(2, -1)->value);
    }
    public function testGetCellInvalidRowTooLarge() {
        $this->setExpectedException("Exception", "Invalid coordinate value: 5,1");
        // bootstrap a data file
        $boardSize = 3;
        $data = $this->generateBoardData($boardSize);
        $board = new \Board($data);
        $this->assertEquals(-1, $board->getCell(5, 1)->value);
    }
    public function testGetCellInvalidRowTooSmall() {
        $this->setExpectedException("Exception", "Invalid coordinate value: -1,5");
        // bootstrap a data file
        $boardSize = 3;
        $data = $this->generateBoardData($boardSize);
        $board = new \Board($data);
        $this->assertEquals(-1, $board->getCell(-1, 5)->value);
    }

    public function testGetCoords() {
        $row = 2;
        $column = 5;

        // bootstrap a data file
        $boardSize = 6;
        $data = $this->generateBoardData($boardSize);
        $board = new \Board($data);
        $cell = $board->getCell($row, $column);
        $cell->value = 347;

        $this->assertEquals(347, $board->getCell($row, $column)->value);

    }

    public function testCalculateValuesOneMine() {
        $boardSize = 4;
        $data = $this->generateBoardData($boardSize);
        $board = new \Board($data);

        // row 3, column 2
        $mineCell = $board->getCell(3, 2);
        $mineCell->value = -1;

        $board->calculateValues();

        $expectedBoardData = array();
        $expectedBoardData[] = [0, 0, 0, 0];
        $expectedBoardData[] = [1, 1, 1, 0];
        $expectedBoardData[] = [1,-1, 1, 0];
        $expectedBoardData[] = [1, 1, 1, 0];

        $this->confirmBoard($expectedBoardData, $board);

    }

    public function testCalculateValuesManyMines() {
        $boardSize = 6;
        $data = $this->generateBoardData($boardSize);
        $board = new \Board($data);

        $mineCell = $board->getCell(2, 2);
        $mineCell->value = -1;

        $mineCell = $board->getCell(3, 4);
        $mineCell->value = -1;

        $mineCell = $board->getCell(3, 5);
        $mineCell->value = -1;

        $mineCell = $board->getCell(6, 6);
        $mineCell->value = -1;

        $mineCell = $board->getCell(5, 4);
        $mineCell->value = -1;

        $board->calculateValues();

        $expectedBoardData = array();
        $expectedBoardData[] = [1, 1, 1, 0, 0, 0];
        $expectedBoardData[] = [1,-1, 2, 2, 2, 1];
        $expectedBoardData[] = [1, 1, 2,-1,-1, 1];
        $expectedBoardData[] = [0, 0, 2, 3, 3, 1];
        $expectedBoardData[] = [0, 0, 1,-1, 2, 1];
        $expectedBoardData[] = [0, 0, 1, 1, 2,-1];

        $this->confirmBoard($expectedBoardData, $board);

    }

    public function testGetAdjacentCells() {
        $boardSize = 6;
        $data = $this->generateBoardData($boardSize);
        $board = new \Board($data);

        //one row up
        $expectedAdjacentCellIds[] = $board->getCell(2, 3);
        $expectedAdjacentCellIds[] = $board->getCell(2, 4);
        $expectedAdjacentCellIds[] = $board->getCell(2, 5);

        $expectedAdjacentCellIds[] = $board->getCell(3, 3);
        // selected cell
        $cell = $board->getCell(3, 4);
        $expectedAdjacentCellIds[] = $board->getCell(3, 5);

        // one row down
        $expectedAdjacentCellIds[] = $board->getCell(4, 3);
        $expectedAdjacentCellIds[] = $board->getCell(4, 4);
        $expectedAdjacentCellIds[] = $board->getCell(4, 5);

        $adjacentCells = $board->getAdjacentCells($cell);

        $this->assertCount(8, $adjacentCells);

        //loop over the returned cells and check that the ids match
        for($i = 0; $i < 8; $i++) {
            $this->assertEquals($expectedAdjacentCellIds[$i]->id, $adjacentCells[$i]->id);
        }

    }

    public function testGetAdjacentCellsInUpperLeftCorner() {
        $boardSize = 6;
        $data = $this->generateBoardData($boardSize);
        $board = new \Board($data);

        // selected cell
        $cell = $board->getCell(1, 1);
        $expectedAdjacentCellIds[] = $board->getCell(1, 2);

        // one row down
        $expectedAdjacentCellIds[] = $board->getCell(2, 1);
        $expectedAdjacentCellIds[] = $board->getCell(2, 2);

        $adjacentCells = $board->getAdjacentCells($cell);

        $this->assertCount(3, $adjacentCells);

        //loop over the returned cells and check that the ids match
        for($i = 0; $i < 3; $i++) {
            $this->assertEquals($expectedAdjacentCellIds[$i], $adjacentCells[$i]);
        }

    }

    public function testGetAdjacentCellsInLowerLeftCorner() {
        $boardSize = 6;
        $data = $this->generateBoardData($boardSize);
        $board = new \Board($data);

        // one row up
        $expectedAdjacentCellIds[] = $board->getCell(5, 1);
        $expectedAdjacentCellIds[] = $board->getCell(5, 2);

        // selected cell
        $cell = $board->getCell(6, 1);
        $expectedAdjacentCellIds[] = $board->getCell(6, 2);


        $adjacentCells = $board->getAdjacentCells($cell);

        $this->assertCount(3, $adjacentCells);

        //loop over the returned cells and check that the ids match
        for($i = 0; $i < 3; $i++) {
            $this->assertEquals($expectedAdjacentCellIds[$i], $adjacentCells[$i]);
        }

    }

    public function testGetAdjacentCellsInUpperRightCorner() {
        $boardSize = 6;
        $data = $this->generateBoardData($boardSize);
        $board = new \Board($data);

        // selected cell
        $expectedAdjacentCellIds[] = $board->getCell(1, 5);
        $cell = $board->getCell(1, 6);

        // one row down
        $expectedAdjacentCellIds[] = $board->getCell(2, 5);
        $expectedAdjacentCellIds[] = $board->getCell(2, 6);

        $adjacentCells = $board->getAdjacentCells($cell);

        $this->assertCount(3, $adjacentCells);

        //loop over the returned cells and check that the ids match
        for($i = 0; $i < 3; $i++) {
            $this->assertEquals($expectedAdjacentCellIds[$i], $adjacentCells[$i]);
        }

    }

    public function testGetAdjacentCellsInLowerRightCorner() {
        // bootstrap a data file
        $boardSize = 6;
        $data = $this->generateBoardData($boardSize);
        $board = new \Board($data);

        // one row up
        $expectedAdjacentCellIds[] = $board->getCell(5, 5);
        $expectedAdjacentCellIds[] = $board->getCell(5, 6);

        // selected cell
        $expectedAdjacentCellIds[] = $board->getCell(6, 5);
        $cell = $board->getCell(6, 6);

        $adjacentCells = $board->getAdjacentCells($cell);

        $this->assertCount(3, $adjacentCells);

        //loop over the returned cells and check that the ids match
        for($i = 0; $i < 3; $i++) {
            $this->assertEquals($expectedAdjacentCellIds[$i], $adjacentCells[$i]);
        }

    }

    public function testIsCardinalAdjacent() {
        // bootstrap a data file
        $boardSize = 6;
        $data = $this->generateBoardData($boardSize);
        $board = new \Board($data);

        $cell1 = $board->getCell(4, 3);

        // positive tests for north, east, south, west
        $cell2 = $board->getCell(4, 2);
        $this->assertTrue($board->isCardinalAdjacent($cell1, $cell2));

        $cell2 = $board->getCell(4, 4);
        $this->assertTrue($board->isCardinalAdjacent($cell1, $cell2));

        $cell2 = $board->getCell(3, 3);
        $this->assertTrue($board->isCardinalAdjacent($cell1, $cell2));

        $cell2 = $board->getCell(5, 3);
        $this->assertTrue($board->isCardinalAdjacent($cell1, $cell2));

        // negative tests for diagonals
        $cell2 = $board->getCell(3, 2);
        $this->assertFalse($board->isCardinalAdjacent($cell1, $cell2));

        $cell2 = $board->getCell(3, 4);
        $this->assertFalse($board->isCardinalAdjacent($cell1, $cell2));

        $cell2 = $board->getCell(5, 2);
        $this->assertFalse($board->isCardinalAdjacent($cell1, $cell2));

        $cell2 = $board->getCell(5, 4);
        $this->assertFalse($board->isCardinalAdjacent($cell1, $cell2));

    }

    public function testSelect() {
        $boardSize = 10;
        $data = $this->generateBoardData($boardSize);
        $board = new \Board($data);

        $mineCell = $board->getCell(1, 1);
        $mineCell->value = -1;
        $mineCell = $board->getCell(1, 4);
        $mineCell->value = -1;
        $mineCell = $board->getCell(4, 1);
        $mineCell->value = -1;
        $mineCell = $board->getCell(4, 10);
        $mineCell->value = -1;
        $mineCell = $board->getCell(5, 7);
        $mineCell->value = -1;
        $mineCell = $board->getCell(6, 1);
        $mineCell->value = -1;
        $mineCell = $board->getCell(7, 5);
        $mineCell->value = -1;
        $mineCell = $board->getCell(9, 2);
        $mineCell->value = -1;
        $mineCell = $board->getCell(10, 6);
        $mineCell->value = -1;
        $mineCell = $board->getCell(10, 10);
        $mineCell->value = -1;

        //double check that the board looks like we expect
        $expectedBoardData = array();
        $expectedBoardData[] = array(-1, 0, 0,-1, 0,   0, 0, 0, 0, 0);
        $expectedBoardData[] = array( 0, 0, 0, 0, 0,   0, 0, 0, 0, 0);
        $expectedBoardData[] = array( 0, 0, 0, 0, 0,   0, 0, 0, 0, 0);
        $expectedBoardData[] = array(-1, 0, 0, 0, 0,   0, 0, 0, 0,-1);
        $expectedBoardData[] = array( 0, 0, 0, 0, 0,   0,-1, 0, 0, 0);

        $expectedBoardData[] = array(-1, 0, 0, 0, 0,   0, 0, 0, 0, 0);
        $expectedBoardData[] = array( 0, 0, 0, 0,-1,   0, 0, 0, 0, 0);
        $expectedBoardData[] = array( 0, 0, 0, 0, 0,   0, 0, 0, 0, 0);
        $expectedBoardData[] = array( 0,-1, 0, 0, 0,   0, 0, 0, 0, 0);
        $expectedBoardData[] = array( 0, 0, 0, 0, 0,  -1, 0, 0, 0,-1);

        $this->confirmBoard($expectedBoardData, $board);

        $board->calculateValues();

        //double check that the board looks like we expect
        $expectedBoardData = array();
        $expectedBoardData[] = array(-1, 1, 1,-1, 1,   0, 0, 0, 0, 0);
        $expectedBoardData[] = array( 1, 1, 1, 1, 1,   0, 0, 0, 0, 0);
        $expectedBoardData[] = array( 1, 1, 0, 0, 0,   0, 0, 0, 1, 1);
        $expectedBoardData[] = array(-1, 1, 0, 0, 0,   1, 1, 1, 1,-1);
        $expectedBoardData[] = array( 2, 2, 0, 0, 0,   1,-1, 1, 1, 1);

        $expectedBoardData[] = array(-1, 1, 0, 1, 1,   2, 1, 1, 0, 0);
        $expectedBoardData[] = array( 1, 1, 0, 1,-1,   1, 0, 0, 0, 0);
        $expectedBoardData[] = array( 1, 1, 1, 1, 1,   1, 0, 0, 0, 0);
        $expectedBoardData[] = array( 1,-1, 1, 0, 1,   1, 1, 0, 1, 1);
        $expectedBoardData[] = array( 1, 1, 1, 0, 1,  -1, 1, 0, 1,-1);

        $this->confirmBoard($expectedBoardData, $board);

        $board->select($board->getCell(1, 10));

        $expectedBoardData = array();
        $expectedBoardData[] = array( 0, 0, 0, 0, 1,   1, 1, 1, 1, 1);
        $expectedBoardData[] = array( 0, 1, 1, 1, 1,   1, 1, 1, 1, 1);
        $expectedBoardData[] = array( 0, 1, 1, 1, 1,   1, 1, 1, 1, 1);
        $expectedBoardData[] = array( 0, 1, 1, 1, 1,   1, 1, 1, 1, 0);
        $expectedBoardData[] = array( 0, 1, 1, 1, 1,   1, 0, 0, 0, 0);

        $expectedBoardData[] = array( 0, 1, 1, 1, 1,   1, 0, 0, 0, 0);
        $expectedBoardData[] = array( 0, 1, 1, 1, 0,   0, 0, 0, 0, 0);
        $expectedBoardData[] = array( 0, 1, 1, 1, 0,   0, 0, 0, 0, 0);
        $expectedBoardData[] = array( 0, 0, 0, 0, 0,   0, 0, 0, 0, 0);
        $expectedBoardData[] = array( 0, 0, 0, 0, 0,   0, 0, 0, 0, 0);

        $this->confirmBoard($expectedBoardData, $board, "visibility");

    }

    public function testSelectSmall() {
        $boardSize = 3;
        $data = $this->generateBoardData($boardSize);
        $board = new \Board($data);

        $mineCell = $board->getCell(3, 3);
        $mineCell->value = -1;

        //double check that the board looks like we expect
        $expectedBoardData = array();
        $expectedBoardData[] = array( 0, 0, 0);
        $expectedBoardData[] = array( 0, 0, 0);
        $expectedBoardData[] = array( 0, 0,-1);

        $this->confirmBoard($expectedBoardData, $board);

        $board->calculateValues();

        //double check that the board looks like we expect
        $expectedBoardData = array();
        $expectedBoardData[] = array( 0, 0, 0);
        $expectedBoardData[] = array( 0, 1, 1);
        $expectedBoardData[] = array( 0, 1,-1);

        $this->confirmBoard($expectedBoardData, $board);

        $board->select($board->getCell(1, 1));

        $expectedBoardData = array();
        $expectedBoardData[] = array( 1, 1, 1);
        $expectedBoardData[] = array( 1, 1, 1);
        $expectedBoardData[] = array( 1, 1, 0);

        $this->confirmBoard($expectedBoardData, $board, "visibility");

    }


    private function confirmBoard($expectedBoardData, $board, $mode = "values") {
        for($row = 1; $row <= count($expectedBoardData); $row++) {
            for($column = 1; $column <= count($expectedBoardData[0]); $column++) {
                if ($mode == "values") {
                    $this->assertEquals($expectedBoardData[$row-1][$column-1], $board->getCell($row, $column)->value);
                } else if($mode == "visibility") {
                    $this->assertEquals($expectedBoardData[$row-1][$column-1], $board->getCell($row, $column)->isVisible);
                }
            }
        }
    }

}