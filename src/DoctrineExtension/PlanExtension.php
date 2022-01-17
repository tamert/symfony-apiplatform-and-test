<?php


namespace App\DoctrineExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Plan;
use App\Entity\User;
use App\Traits\CheckAuthTrait;
use Doctrine\ORM\QueryBuilder;

/**
 * This is sample extension. I thought to it`s may be necessary
 * Class PlanExtension
 * @package App\DoctrineExtension
 */
class PlanExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    use CheckAuthTrait;

    public function filter(QueryBuilder $queryBuilder, User $user)
    {

        $alias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere($alias . ".author  = :user")
            ->setParameter('user', $user);
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($this->supports($resourceClass, $operationName)) {
            $user = $this->check('ROLE_WORKER');
            if ($user instanceof User) {
                $this->filter($queryBuilder, $user);
            }
        }
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [])
    {
        if ($this->supports($resourceClass, $operationName)) {
            $user = $this->check('ROLE_WORKER');
            if ($user instanceof User) {
                $this->filter($queryBuilder, $user);
            }
        }
    }

    public function supports(string $resourceClass, string $operationName): bool
    {
        return $resourceClass == Plan::class and $operationName == 'get';
    }
}