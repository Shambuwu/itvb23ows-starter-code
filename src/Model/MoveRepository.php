<?php

namespace Model;

use mysqli;

class MoveRepository
{
    private $dbConnection;

    public function __construct(mysqli $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function getMoveById($id)
    {
        $stmt = $this->dbConnection->prepare("SELECT * FROM moves WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return new Move($result['id'], $result['game_id'], $result['type'], $result['move_from'], $result['move_to'], $result['previous_id'], $result['state']);
    }

    public function getMovesByGameId($gameId)
    {
        $stmt = $this->dbConnection->prepare("SELECT * FROM moves WHERE game_id = ? ORDER BY id ASC");
        $stmt->bind_param("i", $gameId);
        $stmt->execute();
        $result = $stmt->get_result();
        $moves = [];
        while ($row = $result->fetch_assoc()) {
            $moves[] = new Move($row['id'], $row['game_id'], $row['type'], $row['move_from'], $row['move_to']);
        }
        return $moves;
    }

    public function recordMove(Move $move)
    {
        $stmt = $this->dbConnection->prepare("INSERT INTO moves (game_id, type, move_from, move_to) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $move->getGameId(), $move->getType(), $move->getMoveFrom(), $move->getMoveTo());
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function deleteMove(Move $move)
    {
        $stmt = $this->dbConnection->prepare("DELETE FROM moves WHERE id = ?");
        $stmt->bind_param("i", $move->getId());
        $stmt->execute();
    }
}