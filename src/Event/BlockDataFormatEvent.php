<?php

namespace DiyPageBundle\Event;

use DiyPageBundle\Entity\Block;
use Symfony\Contracts\EventDispatcher\Event;

class BlockDataFormatEvent extends Event
{
    private Block $block;

    /**
     * @var array<string, mixed>|null
     */
    private ?array $result = null;

    public function getBlock(): Block
    {
        return $this->block;
    }

    public function setBlock(Block $block): void
    {
        $this->block = $block;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getResult(): ?array
    {
        return $this->result;
    }

    /**
     * @param array<string, mixed> $result
     */
    public function setResult(array $result): void
    {
        $this->result = $result;
    }
}
