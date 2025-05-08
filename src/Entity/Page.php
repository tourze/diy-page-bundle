<?php

namespace DiyPageBundle\Entity;

use DiyPageBundle\Repository\PageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DoctrineEnhanceBundle\Traits\SortableTrait;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\BatchDeletable;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Column\PictureColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Field\ImagePickerField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use Yiisoft\Arrays\ArraySorter;

#[AsPermission(title: '装修页')]
#[Deletable]
#[Editable]
#[Creatable]
#[BatchDeletable]
#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\Table(name: 'diy_page_page', options: ['comment' => '页面信息'])]
class Page implements \Stringable, AdminArrayInterface
{
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
    use SortableTrait;

    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

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

    #[ImagePickerField]
    #[PictureColumn]
    #[FormField]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '图片'])]
    private ?string $thumb = null;

    #[BoolColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否推荐', 'default' => false])]
    #[ListColumn(order: 98)]
    private ?bool $recommend = false;

    #[FormField]
    #[ListColumn]
    #[Filterable]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => '标志'])]
    private ?string $code = null;

    #[ImagePickerField]
    #[Groups(['restful_read'])]
    #[FormField]
    #[ORM\Column(length: 300, nullable: true, options: ['comment' => '缩略图默认态'])]
    private ?string $defaultThumb = null;

    #[ImagePickerField]
    #[Groups(['restful_read'])]
    #[FormField]
    #[ORM\Column(length: 300, nullable: true, options: ['comment' => '缩略图选中态'])]
    private ?string $activeThumb = null;

    #[Ignore]
    #[FormField(title: '广告位')]
    #[ORM\ManyToMany(targetEntity: Block::class, inversedBy: 'pages', fetch: 'EXTRA_LAZY')]
    private Collection $blocks;

    //    #[Ignore]
    #[FormField(title: '标签')]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\ManyToMany(targetEntity: PageTag::class, inversedBy: 'pages', fetch: 'EXTRA_LAZY')]
    private Collection $pageTags;

    //    #[Ignore]
    #[FormField(title: '专题', span: 24)]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\ManyToMany(targetEntity: Topic::class, inversedBy: 'pages', fetch: 'EXTRA_LAZY')]
    //    #[ORM\JoinTable(name: 'page_topic')]
    private Collection $topics;

    #[FormField(span: 9)]
    #[Groups(['restful_read'])]
    #[ListColumn(sorter: true)]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => '开始时间'])]
    private ?\DateTimeInterface $startTime = null;

    #[FormField(span: 9)]
    #[Groups(['restful_read'])]
    #[ListColumn(sorter: true)]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => '结束时间'])]
    private ?\DateTimeInterface $endTime = null;

    public function __construct()
    {
        $this->blocks = new ArrayCollection();
        $this->pageTags = new ArrayCollection();
        $this->topics = new ArrayCollection();
    }

    public function __toString()
    {
        if (!$this->getId()) {
            return '';
        }

        return "[{$this->getId()}]{$this->getTitle()}";
    }

    public function getId(): ?string
    {
        return $this->id;
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

    /**
     * @return array|Element[]
     */
    public function getValidBlocks(): array
    {
        $blocks = $this->getBlocks()
            ->filter(fn (Block $block) => (bool) $block->isValid())
            ->toArray();
        ArraySorter::multisort($blocks, [
            fn (Block $block) => $block->getSortNumber(),
            fn (Block $block) => $block->getId(),
        ], [
            SORT_DESC,
            SORT_DESC,
        ]);

        return $blocks;
    }

    /**
     * @return Collection<int, Block>
     */
    public function getBlocks(): Collection
    {
        return $this->blocks;
    }

    public function addBlock(Block $block): self
    {
        if (!$this->blocks->contains($block)) {
            $this->blocks->add($block);
        }

        return $this;
    }

    public function removeBlock(Block $block): self
    {
        $this->blocks->removeElement($block);

        return $this;
    }

    public function getDefaultThumb(): ?string
    {
        return $this->defaultThumb;
    }

    public function setDefaultThumb(?string $defaultThumb): self
    {
        $this->defaultThumb = $defaultThumb;

        return $this;
    }

    public function getActiveThumb(): ?string
    {
        return $this->activeThumb;
    }

    public function setActiveThumb(?string $activeThumb): self
    {
        $this->activeThumb = $activeThumb;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getRecommend(): ?bool
    {
        return $this->recommend;
    }

    public function setRecommend(?bool $recommend): void
    {
        $this->recommend = $recommend;
    }

    /**
     * @return Collection<int, Topic>
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): self
    {
        if (!$this->topics->contains($topic)) {
            $this->topics[] = $topic;
            $topic->addPage($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): self
    {
        if ($this->topics->removeElement($topic)) {
            $topic->removePage($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, PageTag>
     */
    public function getPageTags(): Collection
    {
        return $this->pageTags;
    }

    public function addPageTag(PageTag $pageTag): self
    {
        if (!$this->pageTags->contains($pageTag)) {
            $this->pageTags->add($pageTag);
            $pageTag->addPage($this);
        }

        return $this;
    }

    public function removePageTag(PageTag $pageTag): self
    {
        if ($this->pageTags->removeElement($pageTag)) {
            $pageTag->removePage($this);
        }

        return $this;
    }

    public function getThumb(): ?string
    {
        return $this->thumb;
    }

    public function setThumb(?string $thumb): void
    {
        $this->thumb = $thumb;
    }

    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'code' => $this->getCode(),
            'thumb' => $this->getThumb(),
            'activeThumb' => $this->getActiveThumb(),
            'defaultThumb' => $this->getDefaultThumb(),
            'pageTags' => $this->getPageTags(),
            'topics' => $this->getTopics(),
            'blocks' => $this->getBlocks(),
            'startTime' => $this->getStartTime()?->format('Y-m-d H:i:s'),
            'endTime' => $this->getEndTime()?->format('Y-m-d H:i:s'),
            'recommend' => $this->getRecommend(),
            'sortNumber' => $this->getSortNumber(),
            'valid' => $this->isValid(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }
}
