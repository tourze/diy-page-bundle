<?php

namespace DiyPageBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Table(name: 'diy_page_block_attribute', options: ['comment' => '装修组件属性'])]
#[ORM\UniqueConstraint(name: 'diy_page_block_attribute_idx_uniq', columns: ['block_id', 'name'])]
#[ORM\Entity]
class BlockAttribute implements \Stringable, AdminArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    use SnowflakeKeyAware;
    use IpTraceableAware;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Block::class, inversedBy: 'attributes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Block $block = null;

    #[Groups(groups: ['restful_read'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 30)]
    #[ORM\Column(type: Types::STRING, length: 30, options: ['comment' => '属性名'])]
    private ?string $name = null;

    #[Groups(groups: ['restful_read'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '属性值'])]
    private ?string $value = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'value' => $this->getValue(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'value' => $this->getValue(),
        ];
    }

    public function getBlock(): ?Block
    {
        return $this->block;
    }

    public function setBlock(?Block $block): void
    {
        $this->block = $block;
    }

    public function __toString(): string
    {
        return "{$this->getName()}: {$this->getValue()}";
    }
}
