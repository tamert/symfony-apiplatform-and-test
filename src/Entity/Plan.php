<?php

namespace App\Entity;

use App\Enum\PlanStatus;
use App\Repository\PlanRepository;
use App\Controller\Action\CreatePlanAction;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Annotation\ApiFilter;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * Note : I Know there is not admin role but I can do it if you want. And I`m using to my created bundle for example ApiExceptionBundle UserBundle, RecycleBinBundle etc...
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"plan.list", "user.list"}},
 *              "enable_max_depth"=true
 *          },
 *          "post"={
 *              "security"="is_granted('ROLE_WORKER')",
 *              "security_message"="Only worker can add plan.",
 *              "controller"=CreatePlanAction::class,
 *              "denormalization_context"={"groups"={"plan.create"}},
 *              "normalization_context"={"groups"={"plan.detail", "user.list"}}
 *           }
 *     },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"plan.detail", "user.list"}},
 *              "enable_max_depth"=true
 *          },
 *         "put"={
 *              "security"="is_granted('ROLE_MANAGER', object)",
 *              "security_message"="Only manager can change plan.",
 *              "normalization_context"={"groups"={"plan.detail", "user.list"}},
 *              "denormalization_context"={"groups"={"schema"}},
 *         }
 *     },
 *     attributes={"pagination_client_enabled"=true, "normalization_context"={"groups"={"plan.list"}}}
 * )
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={"plan.status": "exact", "plan.author": "exact"}
 * )
 * @ApiFilter(OrderFilter::class, properties={"id", "vacationEndDate", "vacationStartDate", "createdAt", "status"}, arguments={"orderParameterName"="order"})
 * @ORM\Entity(repositoryClass=PlanRepository::class)
 */
class Plan implements SoftDeletableInterface, TimestampableInterface
{
    use SoftDeletableTrait;
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"plan.list", "schema", "plan.detail"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="authors")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"plan.list", "plan.detail"})
     * @MaxDepth(1)
     */
    private $author;

    /**
     * @ORM\Column(name="status", type="string")
     * @Groups({"plan.list", "schema", "plan.detail"})
     */
    private $status = PlanStatus::PENDING;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="resolvers")
     * @Groups({"plan.admin"})
     * @MaxDepth(1)
     */
    private $resolvedBy;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"plan.list", "plan.create", "plan.detail"})
     * @Assert\GreaterThan("yesterday")
     */
    private $vacationStartDate;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"plan.list", "plan.create", "plan.detail"})
     * @Assert\GreaterThan("today")
     */
    private $vacationEndDate;

    /**
     * @Groups({"plan.list", "plan.detail"})
     */
    public $requestCreatedAt;

    /**
     * @Groups({"plan.list", "plan.detail"})
     */
    public $vacationDay;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getStatus(): string
    {
        if (!in_array($this->status, PlanStatus::values())) {
            throw new \InvalidArgumentException("Invalid type");
        }
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getResolvedBy(): ?User
    {
        return $this->resolvedBy;
    }

    public function setResolvedBy(?User $resolvedBy): self
    {
        $this->resolvedBy = $resolvedBy;

        return $this;
    }

    public function getRequestCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getVacationDay(): string
    {
        return (string)$this->vacationEndDate->diff($this->vacationStartDate)->format("%a");

    }

    public function getVacationStartDate(): ?\DateTimeInterface
    {
        return $this->vacationStartDate;
    }

    public function setVacationStartDate(\DateTimeInterface $vacationStartDate): self
    {
        $this->vacationStartDate = $vacationStartDate;

        return $this;
    }

    public function getVacationEndDate(): ?\DateTimeInterface
    {
        return $this->vacationEndDate;
    }

    public function setVacationEndDate(\DateTimeInterface $vacationEndDate): self
    {
        $this->vacationEndDate = $vacationEndDate;

        return $this;
    }

    /**
     * @Assert\Callback
     * @param ExecutionContext $context
     * @param $payload
     */
    public function checkMaxTimeGreaterThanMin(ExecutionContext $context, $payload)
    {

        if ($this->vacationEndDate < $this->vacationStartDate or $this->vacationEndDate->setTime(0, 0)->diff($this->vacationStartDate->setTime(0, 0))->format("%a") == 0) {
            $context->buildViolation('End date must be at least one more day at start date')
                ->atPath('vacationEndDate')
                ->addViolation();
        }

    }
}
