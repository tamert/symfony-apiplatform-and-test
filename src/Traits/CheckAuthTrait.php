<?php


namespace App\Traits;


use App\Entity\User;
use App\Enum\UserRoles;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Security;

trait CheckAuthTrait
{

    /**
     * @var Security
     */
    private $security;

    /**
     * @param Security $security
     * @required
     */
    public function setSecurity(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param string $permission
     * @return false|\Symfony\Component\Security\Core\User\UserInterface|null
     */
    public function check($permission = UserRoles::MANAGER)
    {
        if ($this->security->getToken() instanceof AnonymousToken or !($this->security->getUser() instanceof User)) {
            return false;
        }

        return in_array($permission, $this->security->getUser()->getRoles()) ? $this->security->getUser() : false;
    }



}