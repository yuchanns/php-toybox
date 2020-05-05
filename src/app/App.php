<?php


namespace yuchanns\toybox\app;

use Symfony\Component\Console\Application;

class App extends Application
{
    public function __construct(string $name = 'UNKNOWN', string $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);
        $this->add(new RunCmd());
        $this->add(new InitCmd());
    }
}