<?php

namespace DiyPageBundle\Entity;

use DiyPageBundle\Repository\BlockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\EcolBundle\Attribute\Expression;

/**
 * @implements AdminArrayInterface<string, mixed>
 * @implements ApiArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: BlockRepository::class)]
#[ORM\Table(name: 'diy_page_block', options: ['comment' => '广告位'])]
class Block implements \Stringable, AdminArrayInterface, ApiArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[IndexColumn]
    #[TrackColumn]
    #[Assert\NotNull]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[Groups(groups: ['restful_read'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '标题'])]
    private ?string $title = null;

    #[Groups(groups: ['restful_read'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => '唯一标志'])]
    private ?string $code = null;

    #[Groups(groups: ['restful_read'])]
    #[Assert\Length(max: 40)]
    #[ORM\Column(length: 40, nullable: true, options: ['comment' => '类型ID'])]
    private ?string $typeId = null;

    #[Groups(groups: ['restful_read'])]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '排序'])]
    private ?int $sortNumber = null;

    /**
     * @var Collection<int, Element>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'block', targetEntity: Element::class, fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $elements;

    #[Expression]
    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '显示规则'])]
    private ?string $showExpression = null;

    #[Groups(groups: ['admin_curd'])]
    #[Assert\Type(type: \DateTimeInterface::class)]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, length: 30, nullable: true, options: ['comment' => '开始时间'])]
    private ?\DateTimeInterface $beginTime = null;

    #[Groups(groups: ['admin_curd'])]
    #[Assert\Type(type: \DateTimeInterface::class)]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, length: 30, nullable: true, options: ['comment' => '结束时间'])]
    private ?\DateTimeInterface $endTime = null;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (null === $this->getId() || null === $this->getTitle() || null === $this->getCode()) {
            return '';
        }

        return "{$this->getTitle()}({$this->getCode()})";
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): void
    {
        $this->sortNumber = $sortNumber;
    }

    /**
     * @return Collection<int, Element>
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(Element $element): void
    {
        if (!$this->elements->contains($element)) {
            $this->elements->add($element);
            $element->setBlock($this);
        }
    }

    public function removeElement(Element $element): void
    {
        if ($this->elements->removeElement($element)) {
            // set the owning side to null (unless already changed)
            if ($element->getBlock() === $this) {
                $element->setBlock(null);
            }
        }
    }

    public function getShowExpression(): ?string
    {
        return $this->showExpression;
    }

    public function setShowExpression(?string $showExpression): void
    {
        $this->showExpression = $showExpression;
    }

    public function getTypeId(): ?string
    {
        return $this->typeId;
    }

    public function setTypeId(?string $typeId): void
    {
        $this->typeId = $typeId;
    }

    public function getBeginTime(): ?\DateTimeInterface
    {
        return $this->beginTime;
    }

    public function setBeginTime(?\DateTimeInterface $beginTime): void
    {
        $this->beginTime = $beginTime;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): void
    {
        $this->endTime = $endTime;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->isValid(),
            'title' => $this->getTitle(),
            'code' => $this->getCode(),
            'typeId' => $this->getTypeId(),
            'sortNumber' => $this->getSortNumber(),
            'beginTime' => $this->getBeginTime()?->format('Y-m-d H:i:s'),
            'endTime' => $this->getEndTime()?->format('Y-m-d H:i:s'),
            'showExpression' => $this->getShowExpression(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->isValid(),
            'title' => $this->getTitle(),
            'code' => $this->getCode(),
            'typeId' => $this->getTypeId(),
            'sortNumber' => $this->getSortNumber(),
            'beginTime' => $this->getBeginTime()?->format('Y-m-d H:i:s'),
            'endTime' => $this->getEndTime()?->format('Y-m-d H:i:s'),
            'showExpression' => $this->getShowExpression(),
        ];
    }
}
