<?php

use PHPUnit\Framework\TestCase;
use Controller\BoardController;

class BoardControllerTest extends TestCase
{
    public function testIsNeighbour()
    {
        // Create an instance of the BoardController
        $boardController = new BoardController();

        // Assert that two neighbouring positions are recognised as such
        $this->assertTrue($boardController->isNeighbour('0,0', '0,1'));

        // Assert that two non-neighbouring positions are recognised as such
        $this->assertFalse($boardController->isNeighbour('0,0', '1,1'));
    }

    public function testHasNeighbour()
    {
        // Create an instance of the BoardController
        $boardController = new BoardController();

        // Define a sample board state
        $board = [
            '0,0' => [['0', 'Q']],
            '0,1' => [['1', 'Q']],
        ];

        // Assert that a position with a neighbour has a neighbour
        $this->assertTrue($boardController->hasNeighbour('0,0', $board));

        // Assert that a position without a neighbour does not have a neighbour
        $this->assertFalse($boardController->hasNeighbour('1,1', $board));
    }

    public function testNeighboursAreSameColor()
    {
        // Create an instance of the BoardController
        $boardController = new BoardController();

        // Define a sample board state
        $board = [
            '0,0' => [['0', 'Q']],
            '0,1' => [['0', 'B']],
            '1,0' => [['1', 'Q']],
        ];

        // Assert that a position with neighbours of the same color has neighbours of the same color
        $this->assertTrue($boardController->neighboursAreSameColor('0', '0,0', $board));

        // Assert that a position with neighbours of a different color does not have neighbours of the same color
        $this->assertFalse($boardController->neighboursAreSameColor('0', '1,0', $board));
    }

    public function testValidateMove()
    {
        // Create an instance of the BoardController
        $boardController = new BoardController();

        // Define a sample board state
        $board = [
            '0,0' => [['0', 'Q']],
            '0,1' => [['1', 'Q']],
        ];

        // Call the validateMove() method with a valid move
        $result = $boardController->validateMove('0,0', '0,1', $board, 0);

        // Assert that the move is valid
        $this->assertTrue($result);

        // Call the validateMove() method with an invalid move
        $result = $boardController->validateMove('0,0', '0,1', $board, 1);

        // Assert that the move is invalid
        $this->assertFalse($result);
    }

    public function testSlide() {
        // Create an instance of the BoardController
        $boardController = new BoardController();

        // Define a sample board state
        $board = [
            '0,0' => [['0', 'Q']],
            '0,1' => [['1', 'Q']],
        ];

        // Call the slide() method with a valid slide
        $result = $boardController->slide($board, '0,0', '0,1');

        // Assert that the slide is valid
        $this->assertTrue($result);

        // Call the slide() method with an invalid slide
        $result = $boardController->slide($board, '0,0', '1,1');

        // Assert that the slide is invalid
        $this->assertFalse($result);
    }
}