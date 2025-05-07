<?php

namespace DiyPageBundle\Event;

use DiyPageBundle\Entity\Element;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * 元素格式化事件
 */
class ElementDataFormatEvent extends Event
{
    private ?array $result = [];

    private Element $element;

    public function getResult(): ?array
    {
        return $this->result;
    }

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
