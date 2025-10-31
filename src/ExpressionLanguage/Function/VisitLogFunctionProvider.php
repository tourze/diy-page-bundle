<?php

namespace DiyPageBundle\ExpressionLanguage\Function;

use Carbon\CarbonImmutable;
use DiyPageBundle\Entity\Element;
use DiyPageBundle\Repository\VisitLogRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * 广告访问相关函数
 */
#[AutoconfigureTag(name: 'ecol.function.provider')]
#[WithMonologChannel(channel: 'diy_page')]
class VisitLogFunctionProvider implements ExpressionFunctionProviderInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly VisitLogRepository $visitLogRepository,
    ) {
    }

    /**
     * @return array<ExpressionFunction>
     */
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('getDiyPageElementTodayVisitCount', fn (...$args) => sprintf('\%s(%s)', 'getDiyPageElementTodayVisitCount', implode(', ', is_array($args) ? $args : [])), function ($values, ...$args) {
                $this->logger->debug('getDiyPageElementTodayVisitCount', [
                    'values' => $values,
                    'args' => $args,
                ]);

                if (!is_array($values)) {
                    return 0;
                }

                /** @var array<string, mixed> $values */
                $user = $args[0] ?? null;
                $element = $args[1] ?? null;

                if (!$user instanceof UserInterface) {
                    $user = null;
                }

                if (!$element instanceof Element) {
                    $element = null;
                }

                return $this->getDiyPageElementTodayVisitCount($values, $user, $element);
            }),
        ];
    }

    /**
     * 获取指定用户指定广告的今天访问次数，使用例子： getDiyPageElementVisitCount(user, element)
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    /**
     * @param array<string, mixed> $values
     */
    public function getDiyPageElementTodayVisitCount(array $values, ?UserInterface $user = null, ?Element $element = null): int
    {
        if (null === $user || null === $element) {
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
            ->getSingleScalarResult()
        ;
        $c = intval($c);
        $this->logger->info("消费者[{$user->getUserIdentifier()}]已经看了广告[{$element->getId()}] {$c} 次", [
            'user' => $user,
            'element' => $element,
        ]);

        return $c;
    }
}
