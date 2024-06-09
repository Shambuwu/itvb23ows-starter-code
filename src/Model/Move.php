<?php

namespace Model;

class Move
{
    private $id;
    private $gameId;
    private $type;
    private $moveFrom;
    private $moveTo;

    public function __construct($id, $gameId, $type, $moveFrom, $moveTo)
    {
        $this->id = $id;
        $this->gameId = $gameId;
        $this->type = $type;
        $this->moveFrom = $moveFrom;
        $this->moveTo = $moveTo;
    }
}