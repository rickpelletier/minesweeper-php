<?php

require_once("Cell.php");
/**
 * Class Board
 *
 * Hold the board data and provides cell operations
 *
 */
class Board {
    /**
     * @var mixed 2-dimensional array of Cell objects
     */
    private $_data;

    /**
     * @param string $data JSON string representing the game board
     * @throws Exception
     */
    public function __construct($data)
    {
        $this->_data = $data;
    }

    public function getData() {
        return $this->_data;
    }

    /**
     * @param $column column number
     * @param $row row number
     * @return mixed
     * @throws Exception
     */
    public function getCell($row, $column) {
        if ($column < 1 || $row < 1 || $column > count($this->_data) + 1 || $row > count($this->_data[0])) {
            throw new \Exception("Invalid coordinate value: ". $row.",".$column);
        }
        return $this->_data[$row-1][$column-1];
    }

    /**
     * Counterpart to getCell
     * @param Cell $needle
     * @return array
     */
    public function getCoords(\Cell $needle) {
        for($row = 0; $row < count($this->_data); $row++) {
            for($column = 0; $column < count($this->_data[0]); $column++) {
                $cell = $this->_data[$row][$column];
                if ($cell->id == $needle->id) {
                    // the game coords are 1-indexed, not 0-indexed
                    return array($row+1, $column+1);
                }
            }
        }
    }

    /**
     * Updates the value property of each cell based on how many mines they are
     * adjacent to
     *
     * @throws Exception
     */
    public function calculateValues() {
        // go through all of the cells and calculate the values
        for($row = 1; $row <= count($this->_data); $row++) {
            for($column = 1; $column <= count($this->_data[0]); $column++) {
                $cell = $this->getCell($row, $column);
                // if this cell contains a mine, just move on
                if ($cell->value == -1) {
                    continue;
                }
                $adjacentCells = $this->getAdjacentCells($cell);
                foreach($adjacentCells as $adjacentCell) {
                    if ($adjacentCell->value == -1) {
                        $cell->value++;
                    }
                }
            }
        }
    }

    /**
     * Gets the surrounding cells from the indicated cell.  Will return an array with somewhere between 3
     * (if the indicated cell is in a corner) cells, to 5 cells if the indicated cell is on the board edge
     * to 8 cells if the cell is in the middle of the board somewhere.
     *
     * @param \Cell $cell
     * @return array
     */
    public function getAdjacentCells($cell) {
        $coords = $this->getCoords($cell);
        $column = $coords[0];
        $row = $coords[1];
        for ($i = $column - 1; $i <= $column + 1; $i++) {
            // if the x row does not exist, skip it
            // ie the row at -1 or the row at max + 1
            if (!isset($this->_data[$i-1])) {
                continue;
            }
            for ($j = $row - 1; $j <= $row + 1; $j++) {
                // if we are looking at the current cell, skip it
                if ($i == $column && $j == $row) {
                    continue;
                }
                // if the y column does not exist, skip it
                // ie the column at -1 or the column at max + 1
                if (!isset($this->_data[$i-1][$j-1])) {
                    continue;
                }
                $retVal[] = $this->_data[$i-1][$j-1];
            }
        }
        return $retVal;
    }

    /**
     * Checks to see if the two cells are on the same row or on the same column (north/south or east/west)
     *
     * @param \Cell $cell1
     * @param \Cell $cell2
     * @return bool
     */
    public function isCardinalAdjacent($cell1, $cell2) {
        $coords1 = $this->getCoords($cell1);
        $coords2 = $this->getCoords($cell2);
        $column1 = $coords1[0];
        $row1 = $coords1[1];
        $column2 = $coords2[0];
        $row2 = $coords2[1];
        // if either the x coords or the y coords are the same, these cells are "cardinally adjacent"
        return ($column1 == $column2 || $row1 == $row2);
    }

    /**
     * Executes the selection logic, recursively spreading to all adjacent zeros and turning all cells
     * adjacent to a selected cell visible (unless they are mines).
     *
     * @param \Cell $cell
     */
    public function select($cell) {
        // remember which cells have been selected already
        $this->_selected[] = $cell->id;

        // if the cell is already visible and is not a 0, return
        if ($cell->isVisible && $cell->value != 0) {
            return;
        }

        // if its not a mine, set the selected cell to visible
        $cell->isVisible = true;

        // get all of the adjacent cells
        $adjacentCells = $this->getAdjacentCells($cell);

        // loop over each adjacent cell
        for($i = 0; $i < count($adjacentCells); $i++) {
            $adjacentCell = $adjacentCells[$i];
            // if the cell was already selected skip it
            if (!in_array($adjacentCell->id, $this->_selected)) {
                // if its not a mine, show it
                if ($adjacentCell->value != -1) {
                    $adjacentCell->isVisible = true;
                }

                // if the cell is "cardinally adjacent" AND its value is also 0, recursively select it
                if ($this->isCardinalAdjacent($cell, $adjacentCell) && $adjacentCell->value == 0) {
                    $this->select($adjacentCell);
                }
            }
        }
    }

    /**
     * Resets the visibilty of all cells to 0
     *
     * @throws Exception
     */
    public function reset() {
        // go through all of the cells and calculate the values
        for($row = 1; $row <= count($this->_data); $row++) {
            for($column = 1; $column <= count($this->_data[0]); $column++) {
                $cell = $this->getCell($row, $column);
                $cell->isVisible = false;
                $this->_selected = array();
            }
        }
    }


}