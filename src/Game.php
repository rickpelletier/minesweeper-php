<?php

require_once("Board.php");
require_once("Cell.php");

/**
 * Class Game
 *
 * Represents the main "game" loop and associated input/output mechanisms
 *
 */
class Game {

    /**
     * @var Board
     */
    private $board;

    /**
     * @param Board $board
     */
    public function __construct(\Board $board) {
        $this->board = $board;
    }

    /**
     * Starts the game loop
     */
    public function start() {
        $this->printInstructions();
        while(1) {
            $this->_gameLoop();
        }
    }

    /**
     * The main game loop
     */
    private function _gameLoop() {
        // print the current board
        $this->printBoard($this->board);

        // get user input
        echo "Please enter coordinates [row,column](\"quit\" to quit): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);

        // strip all whitespace from the input string
        $line = preg_replace('/\s+/', '', $line);

        $line = trim($line);
        if ($line == "quit") {
            exit("Thank you for playing PHP Minesweeper\n");
        }
        if ($line == "reset") {
            $this->board->reset();
            return;
        }

        // convert to an array to grab the x and y values
        $coords = explode(",", $line);

        // if coords are not valid, print error message
        if (count($coords) != 2) {
            echo "Please enter valid coordinates.\n";
            return;
        }
        $column = $coords[0];
        $row = $coords[1];

        // check for invalid coordinate inputs
        if ($column < 1 || $column > count($this->board->getData())) {
            echo "Please enter a column in range 1-".count($this->board->getData())."\n";
            return;
        }
        if ($row < 1 || $row > count($this->board->getData()[0])) {
            echo "Please enter a row in range 1-".count($this->board->getData()[0])."\n";
            return;
        }

        // select the cell at the given coordinates
        echo "Selecting cell at :" . $column . "," . $row . "\n";
        $this->select($column, $row);
        if ($this->checkForWin()) {
            echo "\n\n";
            echo "**---------------------**\n";
            echo "**     YOU WON!!       **\n";
            echo "**---------------------**\n";
            $this->printBoard($this->board, true);
            exit();
        };
    }

    /**
     * Exits the program with a "Game Over" message
     */
    private function lose() {
        echo "Game Over!\n";
        $this->printBoard($this->board, true);
        exit();
    }

    /**
     * Queries the Board object to determine if all squares have been turned visible.
     *
     * @return bool
     */
    private function checkForWin() {
        $boardData = $this->board->getdata();
        foreach($boardData as $row) {
            foreach($row as $cell) {
                //if all cells except mines are visible, the game has been won
                if ($cell->isVisible == false && $cell->value != -1) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Passes the user input to the board object to execute the select
     * @param $row
     * @param $column
     * @throws Exception
     */
    private function select($row, $column) {
        $cell = $this->board->getCell($row, $column);

        // if the selected cell is a mine, the game is over
        if ($cell->value == -1) {
            $this->lose();
        }

        // tell the board to select the cell
        $this->board->select($cell);

    }

    /**
     * Prints the instructions to the stdout stream
     */
    private function printInstructions() {
        echo "\nPHP Minesweeper!\n";
        echo "To win the game, make all of the cells which are NOT mines visible.\nUnlike in the Windows game, there is no marking of suspected mines.\n";
        echo "Commands: \n";
        echo "  \"quit\" will exit the game.\n";
        echo "  \"reset\" will reset the current game board.\n";
    }

    /**
     * Prints the board to stdout stream
     * @param \Board $board
     * @param bool $showAll
     */
    public static function printBoard($board, $showAll = false) {
        echo "\n";
        foreach($board->getData() as $row) {
            foreach($row as $cell) {
                if ($cell->isVisible || $showAll) {
                    if ($cell->value == -1){
                        echo " X ";
                    } else {
                        echo " " . $cell->value . " ";
                    }
                } else {
                    echo " - ";
                }
            }
            echo "\n";
        }
        echo "\n";
    }
}