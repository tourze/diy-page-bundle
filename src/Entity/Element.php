<?php

namespace DiyPageBundle\Entity;

use DiyPageBundle\Repository\ElementRepository;
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
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\EasyAdmin\Attribute\Column\CopyColumn;
use Tourze\EasyAdmin\Attribute\Column\PictureColumn;
use Tourze\EasyAdmin\Attribute\Field\ImagePickerField;
use Tourze\EasyAdmin\Attribute\Field\RichTextField;
use Tourze\EcolBundle\Attribute\Expression;

#[ORM\Entity(repositoryClass: ElementRepository::class)]
#[ORM\Table(name: 'diy_page_element', options: ['comment' => '图片'])]
class Element implements \Stringable, ApiArrayInterface, AdminArrayInterface
{
    use TimestampableAware;
    use BlameableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;


    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[CopyColumn]
    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Block::class, inversedBy: 'elements')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Block $block = null;

    #[CopyColumn(suffix: true)]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '标题'])]
    private ?string $title = null;

    #[CopyColumn(suffix: true)]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '副标题'])]
    private ?string $subtitle = null;

    #[RichTextField]
    #[CopyColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    private ?string $description = null;

    #[ImagePickerField]
    #[PictureColumn]
    #[CopyColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '图片1'])]
    private ?string $thumb1 = null;

    #[ImagePickerField]
    #[PictureColumn]
    #[CopyColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '图片2'])]
    private ?string $thumb2 = null;

    #[CopyColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '跳转的url'])]
    private ?string $path = null;

    #[CopyColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => 'appId'])]
    private ?string $appId = null;

    #[CopyColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '排序'])]
    private ?int $sortNumber = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '显示标签'])]
    private ?array $showTags = [];

    #[CopyColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => 'Tracking'])]
    private ?string $tracking = null;

    #[CopyColumn]
    #[Expression]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '显示规则'])]
    private ?string $showExpression = null;

    #[CopyColumn]
    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, length: 30, nullable: true, options: ['comment' => '开始时间'])]
    private ?\DateTimeInterface $beginTime = null;

    #[CopyColumn]
    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, length: 30, nullable: true, options: ['comment' => '结束时间'])]
    private ?\DateTimeInterface $endTime = null;

    #[CopyColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '登录后是否跳到path'])]
    private bool $loginJumpPage = false;

    #[CopyColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '模板ID'])]
    private array $subscribeTemplateIds = [];

    public function __toString(): string
    {
        if ($this->getId() === null) {
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

    public function getBlock(): Block
    {
        return $this->block;
    }

    public function setBlock(?Block $block): self
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

    public function getSubscribeTemplateIds(): array
    {
        return $this->subscribeTemplateIds;
    }

    public function setSubscribeTemplateIds(array $subscribeTemplateIds): self
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
