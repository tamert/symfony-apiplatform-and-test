<?php


namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CurrentUserDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{

    /**
     * @var Request|null
     */
    private $request;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * CurrentUserDataProvider constructor.
     * @param RequestStack $requestStack
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        RequestStack $requestStack,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->tokenStorage = $tokenStorage;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        return $this->tokenStorage->getToken()->getUser();
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass == User::class &&
            $this->request->get('id') == "current";
    }
}