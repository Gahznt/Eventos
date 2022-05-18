<?php

namespace App\Bundle\Base\Controller\Api\VersionOne;

use App\Bundle\Base\Contracts\ApiControllerBase as Controller;
use App\Bundle\Base\Entity\DivisionCoordinator;
use App\Bundle\Base\Entity\UserCommittee;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Bundle\Base\Repository\DivisionCoordinatorRepository;
use App\Bundle\Base\Repository\UserCommitteeRepository;

/**
 * 
 * @Route("permissions")
 *
 * Class PermissionController
 * @package App\Bundle\Base\Controller\Site
 */
class PermissionController extends Controller
{
    private DivisionCoordinatorRepository $divisionCoordinatorRepository;
    private UserCommitteeRepository $userCommitteeRepository;

    /**
     * PermissionController constructor.
     * @param DivisionCoordinatorRepository $divisionCoordinatorRepository
     * @param UserCommitteeRepository $userCommitteeRepository
     */
    public function __construct(
        DivisionCoordinatorRepository $divisionCoordinatorRepository,
        UserCommitteeRepository $userCommitteeRepository
    )
    {
        $this->divisionCoordinatorRepository = $divisionCoordinatorRepository;
        $this->userCommitteeRepository = $userCommitteeRepository;
    }

    /**
     * @Route(
     *     path         = "/division_coordinator_by_edition",
     *     name         = "division_coordinator_by_edition",
     *     methods      = {"GET"}
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDivisionCoordinator(Request $request): JsonResponse
    {
        $edition = $request->get('editionId');

        $divisionCoordinators =  $this->divisionCoordinatorRepository->findBy([
            'edition' => $edition,
            'deletedAt' => null,
        ]);

        $result = [];
        foreach ($divisionCoordinators as $divisionCoordinator) {
            $result[] = [
                'id' => $divisionCoordinator->getId(),
                'coordinatorName' => $divisionCoordinator->getCoordinator()->getName(),
                'editionName' => $divisionCoordinator->getEdition()->getNamePortuguese(),
                'divisionName' => $divisionCoordinator->getDivision()->getName(),
            ];
        }

        return $this->responseJson($result);
    }

    /**
     * @Route(
     *     path         = "/permission_committee_by_edition",
     *     name         = "permission_committee_by_edition",
     *     methods      = {"GET"}
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPermissionCommittee(Request $request): JsonResponse
    {
        $edition = $request->get('editionId');

        $permissionCommittee =  $this->userCommitteeRepository->findBy([
            'edition' => $edition,
            'deletedAt' => null,
        ]);

        $result = [];
        foreach ($permissionCommittee as $userCommittee) {
            $result[] = [
                'id' => $userCommittee->getId(),
                'userName' => $userCommittee->getUser()->getName(),
                'editionName' => $userCommittee->getEdition()->getNamePortuguese(),
                'divisionName' => $userCommittee->getDivision()->getName(),
            ];
        }

        return $this->responseJson($result);
    }

    /**
     * @Route("/{id}", name="division_coordinator_delete", methods={"DELETE"})
     *
     * @param DivisionCoordinator $id
     * @return JsonResponse
     */
    public function divisionCoordinatorDelete(DivisionCoordinator $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $id->setDeletedAt(new \DateTime());
        $entityManager->persist($id);
        $entityManager->flush();

        return $this->responseJson(['success' => 'Permissão deletada']);
    }

    /**
     * @Route("/{id}/committee", name="permission_committee_delete", methods={"DELETE"})
     *
     * @param UserCommittee $id
     * @return JsonResponse
     */
    public function permissionCommitteeDelete(UserCommittee $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $id->setDeletedAt(new \DateTime());
        $entityManager->persist($id);
        $entityManager->flush();

        return $this->responseJson(['success' => 'Permissão deletada']);
    }
}