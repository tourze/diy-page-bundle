<?php

namespace DiyPageBundle\Event;

use DiyPageBundle\Entity\Element;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * 元素格式化事件
 */
final class ElementDataFormatEvent extends Event
{
    /**
     * @var array<string, mixed>|null
     */
    private ?array $result = [];

    private Element $element;

    /**
     * @return array<string, mixed>|null
     */
    public function getResult(): ?array
    {
        return $this->result;
    }

    /**
     * @param array<string, mixed>|null $result
     */
    public function setResult(?array $result): void
    {
        $this->result = $result;
    }

    public function getElement(): Element
    {
        return $this->element;
    }

    public function setElement(Element $element): void
    {
        $this->element = $element;
    }
}
