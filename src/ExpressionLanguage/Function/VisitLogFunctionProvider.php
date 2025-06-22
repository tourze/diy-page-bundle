<?php

namespace DiyPageBundle\ExpressionLanguage\Function;

use Carbon\CarbonImmutable;
use DiyPageBundle\Entity\Element;
use DiyPageBundle\Repository\VisitLogRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * 广告访问相关函数
 */
#[AutoconfigureTag('ecol.function.provider')]
class VisitLogFunctionProvider implements ExpressionFunctionProviderInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly VisitLogRepository $visitLogRepository,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('getDiyPageElementTodayVisitCount', fn (...$args) => sprintf('\%s(%s)', 'getDiyPageElementTodayVisitCount', implode(', ', $args)), function ($values, ...$args) {
                $this->logger->debug('getDiyPageElementTodayVisitCount', [
                    'values' => $values,
                    'args' => $args,
                ]);

                return $this->getDiyPageElementTodayVisitCount($values, ...$args);
            }),
        ];
    }

    /**
     * 获取指定用户指定广告的今天访问次数，使用例子： getDiyPageElementVisitCount(user, element)
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getDiyPageElementTodayVisitCount(array $values, ?UserInterface $user = null, ?Element $element = null): int
    {
        if ($user === null) {
            return 0;
        }

        $startTime = CarbonImmutable::now()->startOfDay();
        $endTime = $startTime->endOfDay();

        $c = $this->visitLogRepository->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.user = :user AND a.element = :element AND (a.createTime BETWEEN :start AND :end)')
            ->setParameter('user', $user)
            ->setParameter('element', $element)
            ->setParameter('start', $startTime)
            ->setParameter('end', $endTime)
            ->getQuery()
            ->getSingleScalarResult();
        $c = intval($c);
        $this->logger->info("消费者[{$user->getUserIdentifier()}]已经看了广告[{$element->getId()}] {$c} 次", [
            'user' => $user,
            'element' => $element,
        ]);

        return $c;
    }
}
