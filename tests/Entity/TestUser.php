<?php

namespace DiyPageBundle\Tests\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * 测试用的简单 User 实现
 */
class TestUser implements UserInterface
{
    /**
     * @param array<string> $roles
     */
    public function __construct(
        private string $identifier = 'test-user',
        private array $roles = ['ROLE_USER'],
    ) {
    }

    public function getUserIdentifier(): string
    {
        return '' !== $this->identifier ? $this->identifier : 'default-user';
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
        // 测试实现不需要清除凭据
    }

    public function getUsername(): string
    {
        return $this->identifier;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getPassword(): ?string
    {
        return null;
    }
}
