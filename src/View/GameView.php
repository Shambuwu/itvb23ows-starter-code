<?php

namespace View;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;


class GameView
{
    private $twig;
    private $templatePath = 'index.twig'; // Path to your Twig template

    public function __construct(\Twig\Environment $twig)
    {
        $this->twig = $twig;
        $this->twig->addExtension(new DebugExtension());
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function render($gameState)
    {
        // Assuming $gameState is an associative array with all the data needed for rendering
        echo $this->twig->render($this->templatePath, ['gameState' => $gameState]);
    }
}
