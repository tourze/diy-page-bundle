<?php

namespace DiyPageBundle\Event;

use DiyPageBundle\Entity\Block;
use Symfony\Contracts\EventDispatcher\Event;

class BlockDataFormatEvent extends Event
{
    private Block $block;

    private ?array $result = null;

    public function getBlock(): Block
    {
        return $this->block;
    }

    public function setBlock(Block $block): void
    {
        $this->block = $block;
    }

    public function getResult(): ?array
    {
        return $this->result;
    }

    public function setResult(array $result): void
    {
        $this->result = $result;
    }
}
