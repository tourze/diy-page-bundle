<?php

namespace DiyPageBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;

/**
 * 专题页面访问记录表
 */
#[ORM\Table(name: 'ims_diy_special_topic_visit_entity', options: ['comment' => '访问信息日志'])]
#[ORM\Entity]
class PageVisitLog
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[Groups(['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
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

    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '专题ID'])]
    private int $topicId = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '访问次数'])]
    private int $times = 0;

    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '日期'])]
    private int $date = 0;

    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '小时'])]
    private int $hour = 0;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 20, nullable: false, options: ['default' => '', 'comment' => '平台'])]
    private string $platform = '';

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false, options: ['default' => '', 'comment' => '订阅渠道'])]
    private string $businessChannel = '';

    public function getTopicId(): int
    {
        return $this->topicId;
    }

    public function setTopicId(int $topicId): void
    {
        $this->topicId = $topicId;
    }

    public function getTimes(): int
    {
        return $this->times;
    }

    public function setTimes(int $times): void
    {
        $this->times = $times;
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function setDate(int $date): void
    {
        $this->date = $date;
    }

    public function getHour(): int
    {
        return $this->hour;
    }

    public function setHour(int $hour): void
    {
        $this->hour = $hour;
    }

    public function getPlatform(): string
    {
        return $this->platform;
    }

    public function setPlatform(string $platform): void
    {
        $this->platform = $platform;
    }

    public function getBusinessChannel(): string
    {
        return $this->businessChannel;
    }

    public function setBusinessChannel(string $businessChannel): void
    {
        $this->businessChannel = $businessChannel;
    }
}
