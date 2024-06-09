<?php

use PHPUnit\Framework\TestCase;
use Controller\GameController;
use Model\GameRepository;
use Model\MoveRepository;
use Model\Game;
use Model\Move;
use Controller\BoardController;

class GameControllerTest extends TestCase
{
    public function testInitGame()
    {
        // Create a mock for the GameRepository
        $gameRepository = $this->createMock(GameRepository::class);
        $gameRepository->method('getGameById')
            ->willReturn(new Game(1, 0, [], [["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]]));
        $gameRepository->method('getMovesByGameId')
            ->willReturn([]);

        // Create a mock for the MoveRepository
        $moveRepository = $this->createMock(MoveRepository::class);

        // Create a mock for the BoardController
        $boardController = $this->createMock(BoardController::class);

        // Create an instance of the GameController
        $gameController = new GameController($gameRepository, $moveRepository, $boardController);

        // Call the initGame() method
        $gameController->initGame();

        // Assert that the game state was correctly initialized
        $this->assertEquals(1, $gameController->getGameId());
    }

    public function testPlay()
    {
        // Create a mock for the GameRepository
        $gameRepository = $this->createMock(GameRepository::class);
        $gameRepository->method('getGameById')
            ->willReturn(new Game(1, 0, [], [["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]]));
        $gameRepository->method('saveGame')
            ->willReturn(true);

        // Create a mock for the MoveRepository
        $moveRepository = $this->createMock(MoveRepository::class);
        $moveRepository->method('recordMove')
            ->willReturn(true);

        // Create a mock for the BoardController
        $boardController = $this->createMock(BoardController::class);

        // Create an instance of the GameController
        $gameController = new GameController($gameRepository, $moveRepository, $boardController);

        // Mock the POST superglobal
        $_POST['piece'] = 'Q';
        $_POST['to'] = '0,0';

        // Call the play() method
        $gameController->play();

        // Assert that the game state was correctly updated
        $this->assertEquals(1, $gameController->getGameId());
    }

    public function testMove() {
// Create a mock for the GameRepository
        $gameRepository = $this->createMock(GameRepository::class);
        $gameRepository->method('getGameById')
            ->willReturn(new Game(1, 0, [], [["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]]));
        $gameRepository->method('saveGame')
            ->willReturn(true);

        // Create a mock for the MoveRepository
        $moveRepository = $this->createMock(MoveRepository::class);
        $moveRepository->method('recordMove')
            ->willReturn(true);

        // Create a mock for the BoardController
        $boardController = $this->createMock(BoardController::class);

        // Create an instance of the GameController
        $gameController = new GameController($gameRepository, $moveRepository, $boardController);

        // Mock the POST superglobal
        $_POST['piece'] = 'Q';
        $_POST['to'] = '0,0';

        // Call the move() method
        $gameController->move();

        // Assert that the game state was correctly updated
        $this->assertEquals(1, $gameController->getGameId());
    }

    public function testRestart() {
        // Create a mock for the GameRepository
        $gameRepository = $this->createMock(GameRepository::class);
        $gameRepository->method('getGameById')
            ->willReturn(new Game(1, 0, [], [["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]]));
        $gameRepository->method('deleteGame')
            ->willReturn(true);

        // Create a mock for the MoveRepository
        $moveRepository = $this->createMock(MoveRepository::class);
        $moveRepository->method('getMovesByGameId')
            ->willReturn([]);

        // Create a mock for the BoardController
        $boardController = $this->createMock(BoardController::class);

        // Create an instance of the GameController
        $gameController = new GameController($gameRepository, $moveRepository, $boardController);

        // Call the restart() method
        $gameController->restart();

        // Assert that the game state was correctly reset
        $this->assertEquals(0, $gameController->getGameId());
    }
}