<?php

namespace DiyPageBundle\Entity;

use DiyPageBundle\Repository\VisitLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;
use Tourze\ScheduleEntityCleanBundle\Attribute\AsScheduleClean;

#[AsScheduleClean(expression: '14 2 * * *', defaultKeepDay: 7, keepDayEnv: 'DIY_PAGE_VISIT_LOG_PERSIST_DAY_NUM')]
#[ORM\Entity(repositoryClass: VisitLogRepository::class, readOnly: true)]
#[ORM\Table(name: 'diy_page_visit_log', options: ['comment' => '访问日志'])]
class VisitLog implements \Stringable
{
    use CreateTimeAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;


    #[ORM\ManyToOne(targetEntity: Block::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Block $block = null;

    #[ORM\ManyToOne(targetEntity: Element::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Element $element = null;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?UserInterface $user = null;

    #[CreateIpColumn]
    #[ORM\Column(length: 45, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getBlock(): ?Block
    {
        return $this->block;
    }

    public function setBlock(?Block $block): self
    {
        $this->block = $block;

        return $this;
    }

    public function getElement(): ?Element
    {
        return $this->element;
    }

    public function setElement(?Element $element): self
    {
        $this->element = $element;

        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setCreatedFromIp(?string $createdFromIp): void
    {
        $this->createdFromIp = $createdFromIp;
    }

    public function __toString(): string
    {
        return "访问记录 #{$this->getId()} - {$this->getBlock()?->getTitle()}";
    }
}
