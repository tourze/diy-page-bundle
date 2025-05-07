<?php

namespace DiyPageBundle\Entity;

use DiyPageBundle\Repository\PointRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Column\PictureColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Field\ImagePickerField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: 'Point')]
#[Editable]
#[Creatable]
#[Deletable]
#[ORM\Entity(repositoryClass: PointRepository::class)]
#[ORM\Table(name: 'diy_page_point', options: ['comment' => '匹配规则'])]
class Point
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

    #[FormField]
    #[ListColumn(width: 320)]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '标题'])]
    private ?string $title = null;

    #[ImagePickerField]
    #[PictureColumn]
    #[FormField]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '图片'])]
    private ?string $thumb = null;

    #[FormField(span: 12)]
    #[ListColumn(sorter: true)]
    #[ORM\Column(type: Types::FLOAT, nullable: true, options: ['comment' => 'x轴坐标'])]
    private ?float $xAxis = null;

    #[FormField(span: 12)]
    #[ListColumn(sorter: true)]
    #[ORM\Column(type: Types::FLOAT, nullable: true, options: ['comment' => 'y轴坐标'])]
    private ?float $yAxis = null;

    #[FormField]
    #[ListColumn(width: 320)]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '跳转的url'])]
    private ?string $path = null;

    #[FormField]
    #[ListColumn]
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => 'appId'])]
    private ?string $appId = null;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Element::class, inversedBy: 'points')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Element $element = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getElement(): ?Element
    {
        return $this->element;
    }

    public function setElement(?Element $element): void
    {
        $this->element = $element;
    }

    public function getThumb(): ?string
    {
        return $this->thumb;
    }

    public function setThumb(?string $thumb): void
    {
        $this->thumb = $thumb;
    }

    public function getXAxis(): ?float
    {
        return $this->xAxis;
    }

    public function setXAxis(?float $xAxis): void
    {
        $this->xAxis = $xAxis;
    }

    public function getYAxis(): ?float
    {
        return $this->yAxis;
    }

    public function setYAxis(?float $yAxis): void
    {
        $this->yAxis = $yAxis;
    }

    public function getAppId(): ?string
    {
        return $this->appId;
    }

    public function setAppId(?string $appId): void
    {
        $this->appId = $appId;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }
}
