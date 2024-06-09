<?php

// Assuming autoloading is properly set up, either manually or via Composer
require_once 'vendor/autoload.php';

use Controller\GameController;
use Controller\BoardController;
use Model\GameRepository;
use Model\MoveRepository;

// Create the database connection
$dbConnection = new mysqli('db', 'root', '', 'hive');

// Create the repositories
$gameRepository = new GameRepository($dbConnection);
$moveRepository = new MoveRepository($dbConnection);

$boardController = new BoardController(); // Make sure it receives whatever dependencies it needs
$gameController = new GameController($gameRepository, $moveRepository, $boardController);

// Simple routing logic
// Determine action based on either PATH_INFO, QUERY_STRING, or a specific parameter
$action = isset($_GET['action']) ? $_GET['action'] : 'home';

switch ($action) {
    case 'start':
        $gameController->initGame();
        break;
    case 'move':
        // Ensure move data is passed correctly
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $gameController->move();
        }
        break;
    case 'restart':
        $gameController->restart();
        break;
    default:
        // Handle default case, such as showing the home page or game board
        $gameController->initGame();
        break;
}

// Further actions and routing as necessary
