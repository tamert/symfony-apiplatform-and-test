<?php


namespace App\DoctrineExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\User;
use App\Enum\UserRoles;
use App\Traits\CheckAuthTrait;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * This is sample extension. I thought to it`s may be necessary
 * Class UserExtension
 * @package App\DoctrineExtension
 */
class UserExtension implements QueryItemExtensionInterface, QueryCollectionExtensionInterface
{
    use CheckAuthTrait;

    public function filter(QueryBuilder $queryBuilder)
    {
        $user = $this->check();
        if (!($user instanceof User))
            throw new AccessDeniedException('User not found');

        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->andWhere("JSON_GET_TEXT(" . $alias . ".roles, 0)  = :role")
            ->orWhere($alias . ".id = :id")
            ->setParameter('id', $user->getId())
            ->setParameter('role', UserRoles::WORKER);
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($this->supports($resourceClass, $operationName) and !$this->check(UserRoles::ADMIN)) $this->filter($queryBuilder);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [])
    {

        if ($this->supports($resourceClass, $operationName) and $this->check(UserRoles::WORKER))
            throw new AccessDeniedException("Only admin or manager can view users.");

        if ($this->supports($resourceClass, $operationName) and !$this->check(UserRoles::ADMIN)) $this->filter($queryBuilder);
    }

    public function supports(string $resourceClass, string $operationName): bool
    {
        return $resourceClass == User::class and $operationName == 'get';
    }
}