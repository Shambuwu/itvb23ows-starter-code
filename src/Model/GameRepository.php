<?php

namespace Model;

use mysqli;

class GameRepository
{
    private $dbConnection;

    public function __construct(mysqli $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function getGameById($id)
    {
        $stmt = $this->dbConnection->prepare("SELECT * FROM games WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return new Game($result['id'], $result['player'], json_decode($result['board'], true), json_decode($result['hand'], true));
    }

    public function saveGame(Game $game)
    {
        $player = $game->getPlayer();
        $board = json_encode($game->getBoard());
        $hand = json_encode($game->getHand());

        $stmt = $this->dbConnection->prepare("INSERT INTO games (player, board, hand) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $player, $board, $hand);
        $stmt->execute();
        $game->setId($this->dbConnection->insert_id);
    }

    public function deleteGame($gameId)
    {
        $stmt = $this->dbConnection->prepare("DELETE FROM games WHERE id = ?");
        $stmt->bind_param("i", $gameId);
        $stmt->execute();
    }
}