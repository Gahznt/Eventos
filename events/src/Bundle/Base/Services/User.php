<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;
use App\Bundle\Base\Entity\Permission;
use App\Bundle\Base\Entity\Program;
use App\Bundle\Base\Entity\User as Entity;
use App\Bundle\Base\Entity\UserAssociation;
use App\Bundle\Base\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class User extends ServiceBase implements ServiceInterface
{
    private UserRepository $repository;

    /**
     * @var string|\Stringable|\Symfony\Component\Security\Core\User\UserInterface
     */
    private $user;

    public function __construct(UserRepository $repository, TokenStorageInterface $tokenStorage)
    {
        $this->repository = $repository;
        $this->user = $tokenStorage->getToken()->getUser();
    }

    public function getUserByCountry(int $countryId, string $cpf): ?array
    {
        $data = $this->repository->findByCountry($countryId, $cpf);

        if (isset($data[0])) {
            return $data[0];
        }

        return $this->repository->findByCountry($countryId, $cpf);
    }

    public function getUserById($id): ?Entity
    {
        return $this->repository->find($id);
    }

    public function getUserLoggedAssociationTypes(): array
    {
        if ($this->user->getRecordType() == 1)
            return UserAssociation::USER_ASSOCIATIONS_TYPE_ENTERPRISE;

        return UserAssociation::USER_ASSOCIATIONS_TYPE;
    }

    public function isDivisionCoordinator(?Entity $user): bool
    {
        return $user && $user->getUserDivisionCoordinator()->count() > 0;
    }

    public function isDivisionCommittee(?Entity $user): bool
    {
        return $user && $user->getUserCommittees()->count() > 0;
    }

    public function isEvaluator(?Entity $user): bool
    {
        return $user && $user->getIndications()->count() > 0;
    }

    public function isThemeLead(?Entity $user): bool
    {
        return $user && $user->getUserThemesResearchers()->count() > 0;
    }

    public function isAdmin(?Entity $user): bool
    {
        return $user && in_array(Permission::ROLE_ADMIN, $user->getRoles());
    }

    public function isAdminOperational(?Entity $user): bool
    {
        return $user && in_array(Permission::ROLE_ADMIN_OPERATIONAL, $user->getRoles());
    }

    public function isUser(?Entity $user): bool
    {
        return $this->isAdminOperational($user);
    }

    public function isAssociatedIndividual(?Entity $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->getAssociations(['showOnlyActive' => true])->count() > 0;
    }

    public function isAssociatedPPG(?Entity $user): bool
    {
        if (! $user || ! $user->getInstitutionsPrograms()) {
            return false;
        }

        if (
            $user->getInstitutionsPrograms()->getInstitutionFirstId()
            && $user->getInstitutionsPrograms()->getInstitutionFirstId()->getStatus()
            && $user->getInstitutionsPrograms()->getProgramFirstId()
            && $user->getInstitutionsPrograms()->getProgramFirstId()->getStatus()
            && $user->getInstitutionsPrograms()->getProgramFirstId()->getPaid()
        ) {
            return true;
        }

        if (
            $user->getInstitutionsPrograms()->getInstitutionSecondId()
            && $user->getInstitutionsPrograms()->getInstitutionSecondId()->getStatus()
            && $user->getInstitutionsPrograms()->getProgramSecondId()
            && $user->getInstitutionsPrograms()->getProgramSecondId()->getStatus()
            && $user->getInstitutionsPrograms()->getProgramSecondId()->getPaid()
        ) {
            return true;
        }

        return false;
    }

    public function isAssociatedProgram(?Entity $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->getAssociatedProgram()
            && $user->getAssociatedProgram()->getStatus() === Program::PROGRAM_STATUS_ENABLED;
    }

    public function isAssociated(?Entity $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->isAssociatedIndividual($user)
            || $this->isAssociatedPPG($user)
            || $this->isAssociatedProgram($user);
    }
}
