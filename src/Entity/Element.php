<?php

namespace DiyPageBundle\Entity;

use AntdCpBundle\Builder\Field\BraftEditor;
use DiyPageBundle\Repository\ElementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
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
use Tourze\EasyAdmin\Attribute\Action\CurdAction;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\CopyColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Column\PictureColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Field\ImagePickerField;
use Tourze\EasyAdmin\Attribute\Field\RichTextField;
use Tourze\EasyAdmin\Attribute\Field\SelectField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use Tourze\EcolBundle\Attribute\Expression;

#[AsPermission(title: '图片')]
#[Deletable]
#[Editable]
#[Creatable]
#[BatchDeletable]
#[ORM\Entity(repositoryClass: ElementRepository::class)]
#[ORM\Table(name: 'diy_page_element', options: ['comment' => '图片'])]
class Element implements \Stringable, ApiArrayInterface, AdminArrayInterface
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

    #[CopyColumn]
    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Block::class, inversedBy: 'elements')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Block $block;

    #[CopyColumn(suffix: true)]
    #[FormField]
    #[Filterable]
    #[ListColumn(sorter: true)]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '标题'])]
    private ?string $title = null;

    #[CopyColumn(suffix: true)]
    #[FormField]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '副标题'])]
    private ?string $subtitle = null;

    /**
     * @BraftEditor()
     */
    #[RichTextField]
    #[CopyColumn]
    #[FormField]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    private ?string $description = null;

    #[ImagePickerField]
    #[PictureColumn]
    #[CopyColumn]
    #[FormField(span: 6)]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '图片1'])]
    private ?string $thumb1 = null;

    #[ImagePickerField]
    #[PictureColumn]
    #[CopyColumn]
    #[FormField(span: 18)]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '图片2'])]
    private ?string $thumb2 = null;

    #[CopyColumn]
    #[FormField]
    #[ListColumn(width: 320)]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '跳转的url'])]
    private ?string $path = null;

    #[CopyColumn]
    #[Groups(['restful_read'])]
    #[FormField]
    #[ListColumn]
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => 'appId'])]
    private ?string $appId = null;

    #[CopyColumn]
    #[FormField(span: 6)]
    #[ListColumn(sorter: true)]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '排序'])]
    private ?int $sortNumber = null;

    /**
     * 如果有配置，则只有满足指定标签条件，我们才显示这个广告
     * 具体开发时，选项数据可以自由添加，具体做法是先一个继承了 SelectDataFetcher 的服务，然后为这个服务加一个标签 diy-page.tag.provider 就可以自动合并数据到下面去了
     * 具体可以参考 \AiChongHuiBundle\Provider\TagProvider.
     */
    #[CopyColumn]
    #[SelectField(targetEntity: 'diy-page.tag.fetcher', mode: 'multiple')]
    #[FormField]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '显示标签'])]
    private ?array $showTags = [];

    #[CopyColumn]
    #[FormField]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => 'Tracking'])]
    private ?string $tracking = null;

    #[CopyColumn]
    #[Expression]
    #[FormField]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '显示规则'])]
    private ?string $showExpression = null;

    #[CopyColumn]
    #[Groups(['admin_curd'])]
    #[ListColumn(title: '开始时间')]
    #[FormField(title: '开始时间', span: 8)]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, length: 30, nullable: true, options: ['comment' => '开始时间'])]
    private ?\DateTimeInterface $beginTime = null;

    #[CopyColumn]
    #[Groups(['admin_curd'])]
    #[ListColumn(title: '结束时间')]
    #[FormField(title: '结束时间', span: 8)]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, length: 30, nullable: true, options: ['comment' => '结束时间'])]
    private ?\DateTimeInterface $endTime = null;

    /**
     * @var Collection<Element>
     */
    #[CurdAction(label: 'point', drawerWidth: '80%')]
    #[ORM\OneToMany(mappedBy: 'element', targetEntity: Point::class, fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $points;

    #[CopyColumn]
    #[FormField]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '登录后是否跳到path'])]
    private ?bool $loginJumpPage = false;

    #[CopyColumn]
    #[Groups(['restful_read'])]
    #[FormField]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '模板ID'])]
    private ?array $subscribeTemplateIds = [];

    public function __construct()
    {
        $this->points = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return $this->getTitle();
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

    /**
     * @return Collection<int, Point>
     */
    public function getPoints(): Collection
    {
        return $this->points;
    }

    public function addElement(Point $point): self
    {
        if (!$this->points->contains($point)) {
            $this->points[] = $point;
            $point->setElement($this);
        }

        return $this;
    }

    public function removeElement(Point $point): self
    {
        if ($this->points->removeElement($point)) {
            // set the owning side to null (unless already changed)
            if ($point->getElement() === $this) {
                $point->setElement(null);
            }
        }

        return $this;
    }

    public function getBlock(): Block
    {
        return $this->block;
    }

    public function setBlock(Block $block): self
    {
        $this->block = $block;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getThumb1(): ?string
    {
        return $this->thumb1;
    }

    public function setThumb1(?string $thumb1): self
    {
        $this->thumb1 = $thumb1;

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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getThumb2(): ?string
    {
        return $this->thumb2;
    }

    public function setThumb2(?string $thumb2): self
    {
        $this->thumb2 = $thumb2;

        return $this;
    }

    public function getShowTags(): ?array
    {
        return $this->showTags;
    }

    public function setShowTags(?array $showTags): self
    {
        $this->showTags = $showTags;

        return $this;
    }

    public function getTracking(): ?string
    {
        return $this->tracking;
    }

    public function setTracking(?string $tracking): self
    {
        $this->tracking = $tracking;

        return $this;
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

    public function getAppId(): ?string
    {
        return $this->appId;
    }

    public function setAppId(?string $appId): self
    {
        $this->appId = $appId;

        return $this;
    }

    public function isLoginJumpPage(): ?bool
    {
        return $this->loginJumpPage;
    }

    public function setLoginJumpPage(bool $loginJumpPage): self
    {
        $this->loginJumpPage = $loginJumpPage;

        return $this;
    }

    public function getSubscribeTemplateIds(): ?array
    {
        return $this->subscribeTemplateIds;
    }

    public function setSubscribeTemplateIds(?array $subscribeTemplateIds): self
    {
        $this->subscribeTemplateIds = [];
        foreach ($subscribeTemplateIds as $templateId) {
            $this->subscribeTemplateIds[] = trim($templateId);
        }

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): void
    {
        $this->subtitle = $subtitle;
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

    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'title' => $this->getTitle(),
            'subtitle' => $this->getSubtitle(),
            'description' => $this->getDescription(),
            'thumb1' => $this->getThumb1(),
            'thumb2' => $this->getThumb2(),
            'path' => $this->getPath(),
            'appId' => $this->getAppId(),
            'tracking' => $this->getTracking(),
            'loginJumpPage' => $this->isLoginJumpPage(),
            'subscribeTemplateIds' => $this->getSubscribeTemplateIds(),
            'sortNumber' => $this->getSortNumber(),
            'beginTime' => $this->getBeginTime()?->format('Y-m-d H:i:s'),
            'endTime' => $this->getEndTime()?->format('Y-m-d H:i:s'),
            'showTags' => $this->getShowTags(),
            'valid' => $this->isValid(),
        ];
    }

    public function retrieveAdminArray(): array
    {
        return $this->retrieveApiArray();
    }
}
