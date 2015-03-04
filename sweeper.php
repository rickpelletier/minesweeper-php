<?php

require_once("src/Cell.php");
require_once("src/Board.php");
require_once("src/Game.php");

function generateBoardData($boardSize) {
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

function generateMines($board) {
    $maxMines = rand(2, count($board->getData())*2);
    // randomly seed some mines
    for($i = 0; $i < $maxMines; $i++) {
        $cell = $board->getCell(rand(1, count($board->getData())), rand(1, count($board->getData()[0])));
        $cell->value = -1;
    }
}

$boardSize = 10;
$data = generateBoardData($boardSize);
$board = new \Board($data);
generateMines($board);
$board->calculateValues();

$game = new \Game($board);
$game->start();
