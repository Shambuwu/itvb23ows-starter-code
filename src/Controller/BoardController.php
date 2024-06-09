<?php

namespace Controller;

class BoardController
{
    private $offsets = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];

    public function isNeighbour($a, $b) {
        $a = explode(',', $a);
        $b = explode(',', $b);
        if ($a[0] == $b[0] && abs($a[1] - $b[1]) == 1) return true;
        if ($a[1] == $b[1] && abs($a[0] - $b[0]) == 1) return true;
        if ($a[0] + $a[1] == $b[0] + $b[1]) return true;
        return false;
    }

    public function hasNeighbour($a, $board) {
        foreach (array_keys($board) as $b) {
            if ($this->isNeighbour($a, $b)) return true;
        }
        return false;
    }

    public function neighboursAreSameColor($player, $a, $board) {
        foreach ($board as $b => $st) {
            if (!$st) continue;
            $c = $st[count($st) - 1][0];
            if ($c != $player && $this->isNeighbour($a, $b)) return false;
        }
        return true;
    }

    public function len($tile) {
        return $tile ? count($tile) : 0;
    }

    public function slide($board, $from, $to) {
        if (!$this->hasNeighbour($to, $board)) return false;
        if (!$this->isNeighbour($from, $to)) return false;
        $b = explode(',', $to);
        $common = [];
        foreach ($this->offsets as $pq) {
            $p = $b[0] + $pq[0];
            $q = $b[1] + $pq[1];
            if ($this->isNeighbour($from, $p.",".$q)) $common[] = $p.",".$q;
        }
        if (!$board[$common[0]] && !$board[$common[1]] && !$board[$from] && !$board[$to]) return false;
        return $this->len($board[$common[0]]) <= $this->len($board[$from]) ||
            $this->len($board[$common[1]]) <= $this->len($board[$from]);
    }

    public function validateMove($piece, $to, $gameState)
    {
        $player = $gameState['game']->getPlayer();
        $board = $gameState['game']->getBoard();
        $hand = $gameState['game']->getHand()[$player];

        if (!$hand[$piece]) {
            return "Player does not have tile";
        } elseif (isset($board[$to])) {
            return 'Board position is not empty';
        } elseif (count($board) && !$this->hasNeighbour($to, $board)) {
            return "Board position has no neighbour";
        } elseif (array_sum($hand) < 11 && !$this->neighboursAreSameColor($player, $to, $board)) {
            return "Board position has opposing neighbour";
        } elseif (array_sum($hand) <= 8 && $hand['Q']) {
            return 'Must play queen bee';
        } else {
            return null;
        }
    }
    // Add any additional utility methods needed.
}
