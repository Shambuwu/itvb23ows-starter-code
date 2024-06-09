<?php

namespace Model;

class Game
{
    private $id;
    private $player;
    private $board;
    private $hand;

    public function __construct($id, $player = null, $board = null, $hand = null)
    {
        $this->id = $id;
        $this->player = $player;
        $this->board = $board;
        $this->hand = $hand;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPlayer()
    {
        return $this->player;
    }

    public function getBoard()
    {
        return $this->board;
    }

    public function getHand()
    {
        return $this->hand;
    }

    public function setPlayer($player)
    {
        $this->player = $player;
    }

    public function setBoard($board)
    {
        $this->board = $board;
    }

    public function setHand($hand)
    {
        $this->hand = $hand;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}