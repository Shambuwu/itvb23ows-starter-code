<?php

namespace Controller;

use Model\GameRepository;
use Model\MoveRepository;
use Model\Game;
use Model\Move;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use View\GameView;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class GameController
{
    private $gameRepository;
    private $moveRepository;
    private $boardController;
    private $twig;
    private $loader;
    private $gameId;

    public function __construct(GameRepository $gameRepository, MoveRepository $moveRepository, BoardController $board)
    {
        $this->gameRepository = $gameRepository;
        $this->moveRepository = $moveRepository;
        $this->boardController = $board;
        $this->loader = new FilesystemLoader('./Template');
        $this->twig = new Environment($this->loader);

        // Check if a gameId is provided
        if (isset($_GET['gameId'])) {
            $this->gameId = $_GET['gameId'];
        } else {
            // If no gameId is provided, create a new game and redirect to it
            $game = new Game(null); // Pass null as the id
            $game->setPlayer(0); // Set a default player
            $game->setBoard([]); // Set an empty board
            $game->setHand([
                0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3],
                1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]
            ]);
            $this->gameRepository->saveGame($game);
            $this->gameId = $game->getId(); // Get the id assigned by the database
            header("Location: index.php?gameId=" . $this->gameId);
            exit();
        }
    }

    public function initGame()
    {
        // Fetch the game state
        $game = $this->gameRepository->getGameById($this->gameId);
        $moves = $this->moveRepository->getMovesByGameId($this->gameId);

        // Construct the game state
        $gameState = [
            'game' => $game,
            'moves' => $moves,
        ];

        // Load the view with game state
        $this->loadView($gameState);
    }

    public function play()
    {
        // Check if the request method is POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $piece = $_POST['piece'];
            $to = $_POST['to'];

            // Fetch the game state
            $game = $this->gameRepository->getGameById($this->gameId);
            $moves = $this->moveRepository->getMovesByGameId($this->gameId);

            // Construct the game state
            $gameState = [
                'game' => $game,
                'moves' => $moves
            ];

            // Fetch the player, board, and hand from the game state
            $player = $gameState['game']->getPlayer();
            $board = $gameState['game']->getBoard();
            $hand = $gameState['game']->getHand()[$player];

            // Validate the move
            if (!$hand[$piece]) {
                $this->redirectToError("Player does not have tile");
            } elseif (isset($board[$to])) {
                $this->redirectToError('Board position is not empty');
            } elseif (count($board) && !$this->boardController->hasNeighbour($to, $board)) {
                $this->redirectToError("Board position has no neighbour");
            } elseif (array_sum($hand) < 11 && !$this->boardController->neighboursAreSameColor($player, $to, $board)) {
                $this->redirectToError("Board position has opposing neighbour");
            } elseif (array_sum($hand) <= 8 && $hand['Q']) {
                $this->redirectToError('Must play queen bee');
            } else {
                // If the move is valid, update the game state and record the move
                $gameState['game']->updateBoard($to, [[$player, $piece]]);
                $gameState['game']->updateHand($player, $piece, -1);
                $gameState['game']->setPlayer(1 - $player);

                $move = new Move($this->gameId, 'play', $piece, $to);
                $this->moveRepository->recordMove($move);

                // Save the updated game state
                $this->gameRepository->saveGame($gameState['game']);
            }

            // Redirect to the game view or refresh
            header('Location: index.php');
            exit();
        }
    }

    public function restart()
    {
        // Create a new game with the predefined initial state
        $game = new Game();
        $game->setBoard([]);
        $game->setHand([
            0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3],
            1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]
        ]);
        $game->setPlayer(0);

        // Save the game to the database
        $this->gameRepository->saveGame($game);

        // Update the current game ID
        $this->gameId = $game->getId();

        // Redirect to the game view, optionally with the new game ID
        header("Location: index.php?gameId=" . $this->gameId);
        exit();
    }

    public function move()
    {
        // Check if the request method is POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $from = $_POST['from'];
            $to = $_POST['to'];

            // Fetch the game state
            $game = $this->gameRepository->getGameById($this->gameId);
            $moves = $this->moveRepository->getMovesByGameId($this->gameId);

            // Construct the game state
            $gameState = [
                'game' => $game,
                'moves' => $moves
            ];

            // Fetch the player, board, and hand from the game state
            $player = $gameState['game']->getPlayer();
            $board = $gameState['game']->getBoard();
            $hand = $gameState['game']->getHand()[$player];

            // Validate the move
            if (!isset($board[$from])) {
                $this->redirectToError('Board position is empty');
            } elseif ($board[$from][count($board[$from])-1][0] != $player) {
                $this->redirectToError("Tile is not owned by player");
            } elseif ($hand['Q']) {
                $this->redirectToError("Queen bee is not played");
            } else {
                $tile = array_pop($board[$from]);
                if (!$this->boardController->hasNeighbour($to, $board)) {
                    $this->redirectToError("Move would split hive");
                } else {
                    $all = array_keys($board);
                    $queue = [array_shift($all)];
                    while ($queue) {
                        $next = explode(',', array_shift($queue));
                        foreach ($GLOBALS['OFFSETS'] as $pq) {
                            list($p, $q) = $pq;
                            $p += $next[0];
                            $q += $next[1];
                            if (in_array("$p,$q", $all)) {
                                $queue[] = "$p,$q";
                                $all = array_diff($all, ["$p,$q"]);
                            }
                        }
                    }
                    if ($all) {
                        $this->redirectToError("Move would split hive");
                    } else {
                        if ($from == $to) $this->redirectToError('Tile must move');
                        elseif (isset($board[$to]) && $tile[1] != "B") $this->redirectToError('Tile not empty');
                        elseif ($tile[1] == "Q" || $tile[1] == "B") {
                            if (!$this->boardController->slide($board, $from, $to))
                                $this->redirectToError('Tile must slide');
                        }
                    }
                }
            }

            // If the move is valid, update the game state and record the move
            if (!isset($_SESSION['error'])) {
                $gameState['game']->updateBoard($to, $board[$to]);
                $gameState['game']->setPlayer(1 - $player);

                $move = new Move($this->gameId, 'move', $from, $to);
                $this->moveRepository->recordMove($move);

                // Save the updated game state
                $this->gameRepository->saveGame($gameState['game']);
            }

            // Redirect to the game view or refresh
            header('Location: index.php?gameId=' . $this->gameId);
            exit();
        }
    }

    public function pass()
    {
        // Fetch the game state
        $game = $this->gameRepository->getGameById($this->gameId);
        $moves = $this->moveRepository->getMovesByGameId($this->gameId);

        // Construct the game state
        $gameState = [
            'game' => $game,
            'moves' => $moves
        ];

        // Fetch the player from the game state
        $player = $gameState['game']->getPlayer();

        // Record the pass move
        $move = new Move($this->gameId, 'pass', null, null);
        $this->moveRepository->recordMove($move);

        // Switch the player
        $gameState['game']->setPlayer(1 - $player);

        // Save the updated game state
        $this->gameRepository->saveGame($gameState['game']);

        // Redirect to the game view or refresh
        header('Location: index.php?gameId=' . $this->gameId);
        exit();
    }

    public function undoMove()
    {
        // Fetch the last move
        $lastMove = $this->moveRepository->getMoveById($this->gameId);

        // Fetch the previous move
        $previousMove = $this->moveRepository->getMoveById($lastMove->getPreviousId());

        // Set the last move to the previous move
        $this->gameId = $previousMove->getId();

        // Set the game state to the state of the previous move
        $this->gameRepository->getGameById($this->gameId)->setState($previousMove->getState());

        // Save the updated game state
        $this->gameRepository->saveGame($this->gameRepository->getGameById($this->gameId));

        // Redirect to the game view or refresh
        header('Location: index.php?gameId=' . $this->gameId);
        exit();
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    private function loadView($gameState)
    {
        $view = new GameView($this->twig);

        $view->render($gameState);
    }

    private function redirectToError($errorMessage)
    {
        // Optionally, save the error message to session or another error handling mechanism
        // Redirect to an error page or back to the game with an error message
        header('Location: error.php?error=' . urlencode($errorMessage));
        exit();
    }
}