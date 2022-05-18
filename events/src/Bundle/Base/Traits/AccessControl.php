<?php

namespace App\Bundle\Base\Traits;

use App\Bundle\Base\Entity\Permission;
use App\Bundle\Base\Entity\Program;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserThemesResearchers;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

trait AccessControl
{
    /**
     * @param UserInterface $user
     */
    public function isOwnerUser(UserInterface $user): bool
    {
        $this->isLogged($this->getUser());

        if ($this->getUser()->getId() !== $user->getId()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            return false;
        }

        return true;
    }

    /**
     * @param Collection $authors
     */
    public function isAuthorizedUser(Collection $authors): bool
    {
        if (empty($authors)) {
            return false;
        }

        $found = false;
        foreach ($authors as $author) {
            if (
                $author->getUserAuthor()
                && $this->getUser()
                && $author->getUserAuthor()->getId() === $this->getUser()->getId()
            ) {
                $found = true;
                break;
            }
        }

        if (! $found) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            return false;
        }

        return true;
    }

    public function isOwnerOrAdmin(?User $user): bool
    {
        $this->isLogged($this->getUser());

        if ($this->getUser()->getId() === $user->getId()) {
            return true;
        }

        if ($this->isAdmin($user) || $this->isAdminOperational($user)) {
            return true;
        }

        return false;
    }

    public function isLogged(?User $user)
    {
        if (! $user) {
            throw new AccessDeniedException();
        }
    }

    /**
     * @param Collection $panelists
     */
    public function isAuthorizedPanelist(Collection $panelists): bool
    {
        if (empty($panelists)) {
            return false;
        }

        $found = false;
        foreach ($panelists as $panelist) {
            if (
                $panelist->getPanelistId()
                && $this->getUser()
                && $panelist->getPanelistId()->getId() === $this->getUser()->getId()
            ) {
                $found = true;
                break;
            }
        }

        if (! $found) {
            return false;
        }

        return true;
    }

    /**
     * @param Collection<UserThemesResearchers> $researchers
     */
    public function isAuthorizedThemeResearcher(Collection $researchers): bool
    {
        if (0 === $researchers->count() || ! $this->getUser()) {
            return false;
        }

        $found = false;
        foreach ($researchers as $researcher) {
            if (
                $researcher->getResearcher()
                && $researcher->getResearcher()->getId() !== $this->getUser()->getId()
            ) {
                $found = true;
                break;
            }
        }

        return $found;
    }

    public function isDivisionCoordinator(?User $user): bool
    {
        return $user && $user->getUserDivisionCoordinator()->count() > 0;
    }

    public function isDivisionCommittee(?User $user): bool
    {
        return $user && $user->getUserCommittees()->count() > 0;
    }

    public function isEvaluator(?User $user): bool
    {
        return $user && $user->getIndications()->count() > 0;
    }

    public function isThemeLead(?User $user): bool
    {
        return $user && $user->getUserThemesResearchers()->count() > 0;
    }

    public function isAdmin(?User $user): bool
    {
        return $user && in_array(Permission::ROLE_ADMIN, $user->getRoles());
    }

    public function isAdminOperational(?User $user): bool
    {
        return $user && in_array(Permission::ROLE_ADMIN_OPERATIONAL, $user->getRoles());
    }

    public function isUser(?User $user): bool
    {
        return $this->isAdminOperational($user);
    }

    public function isAssociatedIndividual(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->getAssociations(['showOnlyActive' => true])->count() > 0;
    }

    public function isAssociatedPPG(?User $user): bool
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

    public function isAssociatedProgram(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->getAssociatedProgram()
            && $user->getAssociatedProgram()->getStatus() === Program::PROGRAM_STATUS_ENABLED;
    }

    public function isAssociated(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->isAssociatedIndividual($user)
            || $this->isAssociatedPPG($user)
            || $this->isAssociatedProgram($user);
    }

    private function getPermissionsArray(): array
    {
        $user = $this->getUser();
        assert($user instanceof User);

        return [
            'isAdmin' => $this->isAdmin($user),
            'isAdminOperational' => $this->isAdminOperational($user),
            'isDivisionCoordinator' => $this->isDivisionCoordinator($user),
            'isDivisionCommittee' => $this->isDivisionCommittee($user),
        ];
    }
}
