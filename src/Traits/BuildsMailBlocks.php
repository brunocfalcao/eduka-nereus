<?php

namespace Eduka\Nereus\Traits;

trait BuildsMailBlocks
{
    public $blocks = [];

    /**
     * Adds a new mail template block.
     *
     * @param string $name The blade block mail view name
     * @param array|array $data Data to be used inside the mail block
     *
     * @return void
     */
    public function addBlock(string $name, array $data = [])
    {
        $this->blocks = array_merge($this->blocks, [$name => $data]);
    }
}
