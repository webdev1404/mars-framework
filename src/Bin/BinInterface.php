<?php

namespace Mars\Bin;

interface BinInterface
{
    /**
     * Executes the command
     * @param string $command The command to execute
     */
    public function execute(string $command);
}