<?php


namespace App\Controller\Action;


use App\Entity\Plan;
use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Entity\User;
use App\Repository\PlanRepository;
use Symfony\Component\Security\Core\Security;

final class CreatePlanAction
{
    /**
     * @var Security
     */
    protected $security;

    /**
     * @var PlanRepository
     */
    protected $planRepository;

    /**
     * CreatePlanAction constructor.
     * @param Security $security
     * @param PlanRepository $planRepository
     */
    public function __construct(Security $security, PlanRepository $planRepository)
    {
        $this->security = $security;
        $this->planRepository = $planRepository;
    }

    public function __invoke(Plan $data): Plan
    {

        $user = $this->security->getUser();
        if (!($user instanceof User)) {
            throw new ValidationException("user is not defined");
        }

        $year = $data->getVacationStartDate()->format("Y");
        if ($this->planRepository->countPerYear($year) >= 30) {
            throw new ValidationException("Reached the maximum number of plans per this year ".$year);
        }

        $year = $data->getVacationEndDate()->format("Y");
        if ($this->planRepository->countPerYear($year) >= 30) {
            throw new ValidationException("Reached the maximum number of plans per this year ".$year);
        }


        $data->setAuthor($user);


        return $data;
    }
}