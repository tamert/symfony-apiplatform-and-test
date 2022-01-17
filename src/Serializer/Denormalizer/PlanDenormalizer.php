<?php


namespace App\Serializer\Denormalizer;

use App\Entity\Plan;
use App\Entity\User;
use App\Traits\CheckAuthTrait;
use ApiPlatform\Core\Validator\Exception\ValidationException;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\HttpFoundation\Response;

class PlanDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use CheckAuthTrait;
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'USER_DENORMALIZER_ALREADY_CALLED';

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {

        if (array_key_exists("item_operation_name", $context) and $context["item_operation_name"] == "put") {

            // note: I don`t prefer to control pending status because it`s necessary
            if (!array_key_exists("status", $data))
                throw new ValidationException('status is not defined', Response::HTTP_UNPROCESSABLE_ENTITY);

            $user = $this->check();

            if ($user instanceof User) {

                $data["resolvedBy"] = ['id' => $user->getId()];
            }
        }

        $context[self::ALREADY_CALLED] = true;
        return $this->denormalizer->denormalize($data, Plan::class, $format, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }
        return Plan::class === $type;
    }


}
