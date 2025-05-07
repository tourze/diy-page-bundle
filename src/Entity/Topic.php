<?php

namespace DiyPageBundle\Entity;

use DiyPageBundle\Repository\TopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use Yiisoft\Arrays\ArraySorter;

#[AsPermission(title: '专题')]
#[Editable]
#[Creatable]
#[Deletable]
#[ORM\Entity(repositoryClass: TopicRepository::class)]
#[ORM\Table(name: 'diy_page_topic', options: ['comment' => '专题'])]
class Topic implements ApiArrayInterface, \Stringable, AdminArrayInterface
{
    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
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
    #[Groups(['restful_read', 'admin_curd', 'recursive_view', 'api_tree'])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[BoolColumn]
    #[IndexColumn]
    #[TrackColumn]
    #[Groups(['admin_curd', 'restful_read', 'restful_read', 'restful_write'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[ListColumn(order: 97)]
    #[FormField(order: 97)]
    private ?bool $valid = false;

    #[FormField]
    #[ListColumn(width: 320)]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '标题'])]
    private ?string $title = null;

    #[FormField(span: 12)]
    #[Groups(['restful_read'])]
    #[ListColumn(sorter: true)]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '排序', 'default' => 0])]
    private ?int $sortNumber = null;

    #[BoolColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否推荐', 'default' => false])]
    #[ListColumn(order: 98)]
    private ?bool $recommend = false;

    #[Ignore]
    #[ORM\ManyToMany(targetEntity: Page::class, inversedBy: 'topics', fetch: 'EXTRA_LAZY')]
    //    #[ORM\JoinTable(name: 'page_topic')]
    private Collection $pages;

    #[FormField(title: '推荐页面')]
    #[ListColumn(title: '推荐页面')]
    #[ORM\ManyToOne(targetEntity: Page::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Page $recommendPage = null;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }

    public function __toString()
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getTitle()}";
    }

    public function getId(): ?string
    {
        return $this->id;
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
     * @return Collection<int, Page>
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Page $page): self
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
            $page->addTopic($this);
        }

        return $this;
    }

    public function removePage(Page $page): self
    {
        if ($this->pages->removeElement($page)) {
            $page->removeTopic($this);
        }

        return $this;
    }

    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'recommend' => $this->getRecommend(),
        ];
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): void
    {
        $this->sortNumber = $sortNumber;
    }

    public function getRecommend(): ?bool
    {
        return $this->recommend;
    }

    public function setRecommend(?bool $recommend): void
    {
        $this->recommend = $recommend;
    }

    public function getRecommendPage(): ?Page
    {
        return $this->recommendPage;
    }

    public function setRecommendPage(?Page $recommendPage): void
    {
        $this->recommendPage = $recommendPage;
    }

    /**
     * @return array|Page[]
     */
    public function getValidPages(): array
    {
        $pages = $this->getPages()
            ->filter(fn (Page $page) => (bool) $page->isValid())
            ->toArray();
        ArraySorter::multisort($pages, [
            fn (Page $page) => $page->getSortNumber(),
            fn (Page $page) => $page->getId(),
        ], [
            SORT_DESC,
            SORT_DESC,
        ]);

        return $pages;
    }

    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'recommend' => $this->getRecommend(),
            'sortNumber' => $this->getSortNumber(),
            'pages' => $this->getPages(),
            'valid' => $this->isValid(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }
}
