<?php

namespace DiyPageBundle\Entity;

use DiyPageBundle\Repository\ElementRepository;
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
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\EcolBundle\Attribute\Expression;

/**
 * @implements AdminArrayInterface<string, mixed>
 * @implements ApiArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: ElementRepository::class)]
#[ORM\Table(name: 'diy_page_element', options: ['comment' => '图片'])]
class Element implements \Stringable, ApiArrayInterface, AdminArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    use SnowflakeKeyAware;
    use IpTraceableAware;

    #[IndexColumn]
    #[TrackColumn]
    #[Assert\NotNull]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Block::class, inversedBy: 'elements')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Block $block = null;

    #[Groups(groups: ['restful_read'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '标题'])]
    private ?string $title = null;

    #[Groups(groups: ['restful_read'])]
    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '副标题'])]
    private ?string $subtitle = null;

    #[Groups(groups: ['restful_read'])]
    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    private ?string $description = null;

    #[Groups(groups: ['restful_read'])]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '图片1'])]
    private ?string $thumb1 = null;

    #[Groups(groups: ['restful_read'])]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '图片2'])]
    private ?string $thumb2 = null;

    #[Groups(groups: ['restful_read'])]
    #[Assert\Length(max: 1000)]
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '跳转的url'])]
    private ?string $path = null;

    #[Groups(groups: ['restful_read'])]
    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => 'appId'])]
    private ?string $appId = null;

    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '排序'])]
    private ?int $sortNumber = null;

    /**
     * @var array<string>
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '显示标签'])]
    private ?array $showTags = [];

    #[Groups(groups: ['restful_read'])]
    #[Assert\Length(max: 1000)]
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => 'Tracking'])]
    private ?string $tracking = null;

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

    #[Groups(groups: ['restful_read'])]
    #[Assert\Type(type: 'bool')]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '登录后是否跳到path'])]
    private bool $loginJumpPage = false;

    /**
     * @var array<string>
     */
    #[Groups(groups: ['restful_read'])]
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '模板ID'])]
    private array $subscribeTemplateIds = [];

    /**
     * @var Collection<int, ElementAttribute>
     */
    #[ORM\OneToMany(mappedBy: 'element', targetEntity: ElementAttribute::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $attributes;

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (null === $this->getId()) {
            return '';
        }

        return $this->getTitle() ?? '';
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getBlock(): ?Block
    {
        return $this->block;
    }

    public function setBlock(?Block $block): void
    {
        $this->block = $block;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getThumb1(): ?string
    {
        return $this->thumb1;
    }

    public function setThumb1(?string $thumb1): void
    {
        $this->thumb1 = $thumb1;
    }

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): void
    {
        $this->sortNumber = $sortNumber;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    public function getThumb2(): ?string
    {
        return $this->thumb2;
    }

    public function setThumb2(?string $thumb2): void
    {
        $this->thumb2 = $thumb2;
    }

    /**
     * @return array<string>|null
     */
    public function getShowTags(): ?array
    {
        return $this->showTags;
    }

    /**
     * @param array<string>|null $showTags
     */
    public function setShowTags(?array $showTags): void
    {
        $this->showTags = $showTags;
    }

    public function getTracking(): ?string
    {
        return $this->tracking;
    }

    public function setTracking(?string $tracking): void
    {
        $this->tracking = $tracking;
    }

    public function getShowExpression(): ?string
    {
        return $this->showExpression;
    }

    public function setShowExpression(?string $showExpression): void
    {
        $this->showExpression = $showExpression;
    }

    public function getAppId(): ?string
    {
        return $this->appId;
    }

    public function setAppId(?string $appId): void
    {
        $this->appId = $appId;
    }

    public function isLoginJumpPage(): ?bool
    {
        return $this->loginJumpPage;
    }

    public function setLoginJumpPage(bool $loginJumpPage): void
    {
        $this->loginJumpPage = $loginJumpPage;
    }

    /**
     * @return array<string>
     */
    public function getSubscribeTemplateIds(): array
    {
        return $this->subscribeTemplateIds;
    }

    /**
     * @param array<string> $subscribeTemplateIds
     */
    public function setSubscribeTemplateIds(array $subscribeTemplateIds): void
    {
        $this->subscribeTemplateIds = [];
        foreach ($subscribeTemplateIds as $templateId) {
            $this->subscribeTemplateIds[] = trim($templateId);
        }
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

    /**
     * @return Collection<int, ElementAttribute>
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function addAttribute(ElementAttribute $attribute): void
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes->add($attribute);
            $attribute->setElement($this);
        }
    }

    public function removeAttribute(ElementAttribute $attribute): void
    {
        if ($this->attributes->removeElement($attribute)) {
            // set the owning side to null (unless already changed)
            if ($attribute->getElement() === $this) {
                $attribute->setElement(null);
            }
        }
    }

    /**
     * 根据属性名获取属性值
     */
    public function getAttributeValue(string $name): ?string
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getName() === $name) {
                return $attribute->getValue();
            }
        }

        return null;
    }

    /**
     * 根据属性名查找属性实体
     */
    public function findAttributeByName(string $name): ?ElementAttribute
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getName() === $name) {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * 获取头像属性值（虚拟字段，用于显示）
     */
    public function getAvatarAttribute(): string
    {
        return $this->getAttributeValue('avatar') ?? '';
    }

    /**
     * 获取作者名属性值（虚拟字段，用于显示）
     */
    public function getAuthorNameAttribute(): string
    {
        return $this->getAttributeValue('authorName') ?? '';
    }

    /**
     * 获取作者描述属性值（虚拟字段，用于显示）
     */
    public function getAuthorDescriptionAttribute(): string
    {
        return $this->getAttributeValue('authorDescription') ?? '';
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveApiArray(): array
    {
        $attributes = [];
        foreach ($this->attributes as $attribute) {
            $attributes[] = $attribute->retrieveApiArray();
        }

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
            'attributes' => $attributes,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        $attributes = [];
        foreach ($this->attributes as $attribute) {
            $attributes[] = $attribute->retrieveAdminArray();
        }

        $result = $this->retrieveApiArray();
        $result['attributes'] = $attributes;

        return $result;
    }
}
