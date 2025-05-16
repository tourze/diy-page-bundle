<?php

namespace DiyPageBundle\Entity;

use AntdCpBundle\Builder\Field\DynamicFieldSet;
use DiyPageBundle\Repository\BlockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\BatchDeletable;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\CurdAction;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Event\BeforeCreate;
use Tourze\EasyAdmin\Attribute\Event\BeforeEdit;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use Tourze\EcolBundle\Attribute\Expression;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Arrays\ArraySorter;
use Yiisoft\Json\Json;

#[AsPermission(title: '广告位')]
#[Deletable]
#[Editable]
#[Creatable]
#[BatchDeletable]
#[ORM\Entity(repositoryClass: BlockRepository::class)]
#[ORM\Table(name: 'diy_page_block', options: ['comment' => '广告位'])]
class Block implements \Stringable, AdminArrayInterface
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }
    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    #[BoolColumn]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[ListColumn(order: 97)]
    #[FormField(order: 97)]
    private ?bool $valid = false;

    #[FormField]
    #[Filterable]
    #[ListColumn(sorter: true)]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '标题'])]
    private ?string $title = null;

    #[FormField(span: 12)]
    #[Filterable]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => '唯一标志'])]
    private ?string $code = null;

    #[FormField(span: 12)]
    #[Filterable]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(length: 40, nullable: true, options: ['comment' => '类型ID'])]
    private ?string $typeId = null;

    #[FormField(span: 6)]
    #[Groups(['restful_read'])]
    #[ListColumn(sorter: true)]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '排序'])]
    private ?int $sortNumber = null;

    /**
     * @var Collection<Element>
     */
    #[Ignore]
    #[CurdAction(label: '图片管理', drawerWidth: '80%')]
    #[ORM\OneToMany(mappedBy: 'block', targetEntity: Element::class, fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $elements;

    #[Expression]
    #[FormField]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '显示规则'])]
    private ?string $showExpression = null;

    #[Groups(['admin_curd'])]
    #[ListColumn(title: '开始时间')]
    #[FormField(title: '开始时间', span: 8)]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, length: 30, nullable: true, options: ['comment' => '开始时间'])]
    private ?\DateTimeInterface $beginTime = null;

    #[Groups(['admin_curd'])]
    #[ListColumn(title: '结束时间')]
    #[FormField(title: '结束时间', span: 8)]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, length: 30, nullable: true, options: ['comment' => '结束时间'])]
    private ?\DateTimeInterface $endTime = null;

    #[Ignore]
    #[ORM\ManyToMany(targetEntity: Page::class, mappedBy: 'blocks', fetch: 'EXTRA_LAZY')]
    private Collection $pages;

    /**
     * 有些地方也叫核心属性.
     *
     * @DynamicFieldSet
     *
     * @var Collection<BlockAttribute>
     */
    #[FormField(title: '关键属性')]
    #[Groups(['restful_read'])]
    #[ORM\OneToMany(mappedBy: 'block', targetEntity: BlockAttribute::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $attributes;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
        $this->pages = new ArrayCollection();
        $this->attributes = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getTitle()}({$this->getCode()})";
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): self
    {
        $this->sortNumber = $sortNumber;

        return $this;
    }

    /**
     * @return Collection<int, Element>
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(Element $element): self
    {
        if (!$this->elements->contains($element)) {
            $this->elements[] = $element;
            $element->setBlock($this);
        }

        return $this;
    }

    public function removeElement(Element $element): self
    {
        if ($this->elements->removeElement($element)) {
            // set the owning side to null (unless already changed)
            if ($element->getBlock() === $this) {
                $element->setBlock(null);
            }
        }

        return $this;
    }

    /**
     * @return array|Element[]
     */
    public function getValidElements(): array
    {
        $elements = $this->getElements()
            ->filter(fn (Element $element) => (bool) $element->isValid())
            ->toArray();
        ArraySorter::multisort($elements, [
            fn (Element $element) => $element->getSortNumber(),
            fn (Element $element) => $element->getId(),
        ], [
            SORT_DESC,
            SORT_DESC,
        ]);

        return $elements;
    }

    #[AlertConfig]
    public function getAlterConfig()
    {
        $configText = $_ENV['diy_page_block_alert_config'] ?? '';
        if ($configText) {
            try {
                return Json::decode($configText);
            } catch (\Throwable $exception) {
            }
        }

        return [
        ];
    }

    #[ListColumn(title: '图片数量')]
    public function getElementCount(): int
    {
        return count($this->getValidElements());
    }

    public function getShowExpression(): ?string
    {
        return $this->showExpression;
    }

    public function setShowExpression(?string $showExpression): self
    {
        $this->showExpression = $showExpression;

        return $this;
    }

    /**
     * @return Collection<int, Page>
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Page $page): self
    {
        if (!$this->pages->contains($page)) {
            $this->pages->add($page);
            $page->addBlock($this);
        }

        return $this;
    }

    public function removePage(Page $page): self
    {
        if ($this->pages->removeElement($page)) {
            $page->removeBlock($this);
        }

        return $this;
    }

    public function getTypeId(): ?string
    {
        return $this->typeId;
    }

    public function setTypeId(?string $typeId): self
    {
        $this->typeId = $typeId;

        return $this;
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
     * @return Collection<int, BlockAttribute>
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function addAttribute(BlockAttribute $attribute): self
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes[] = $attribute;
            $attribute->setBlock($this);
        }

        return $this;
    }

    public function removeAttribute(BlockAttribute $attribute): self
    {
        if ($this->attributes->removeElement($attribute)) {
            // set the owning side to null (unless already changed)
            if ($attribute->getBlock() === $this) {
                $attribute->setBlock(null);
            }
        }

        return $this;
    }

    #[BeforeCreate]
    public function createBefore(array $form, BlockRepository $blockRepository)
    {
        $code = ArrayHelper::getValue($form, 'code');
        $model = $blockRepository->findOneBy(['code' => $code]);
        if ($model) {
            throw new ApiException('唯一标志已存在~');
        }
    }

    #[BeforeEdit]
    public function editBefore(array $form, array $record, BlockRepository $blockRepository)
    {
        $newCode = ArrayHelper::getValue($form, 'code');
        $oldCode = ArrayHelper::getValue($record, 'code');
        if ($oldCode != $newCode) {
            $model = $blockRepository->findOneBy(['code' => $newCode]);
            if ($model) {
                throw new ApiException('唯一标志已存在~');
            }
        }
    }

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
            'attributes' => $this->getAttributes(),
        ];
    }
}
