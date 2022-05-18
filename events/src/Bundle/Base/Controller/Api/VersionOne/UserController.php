<?php


namespace App\Bundle\Base\Controller\Api\VersionOne;

use App\Bundle\Base\Contracts\ApiControllerBase as Controller;
use App\Bundle\Base\Entity\Permission;
use App\Bundle\Base\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("users")
 */
class UserController extends Controller
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route(
     *     path         = "/search",
     *     name         = "user_search",
     *     methods      = {"GET"}
     * )
     */
    public function searchUser(Request $request): Response
    {
        if (! $this->getUser()) {
            $this->denyAccessUnlessGranted(Permission::ROLE_ADMIN_OPERATIONAL);
        }

        $search = $request->get('search');

        $users = $this->userRepository->findByFilters(compact('search'));

        $data = [];

        foreach ($users as $user) {
            array_push($data, [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'text' => $user->getName(),
            ]);
        }

        return $this->responseJson($data);
    }

    /**
     * @Route(
     *     path         = "/search_by_document",
     *     name         = "user_search_by_document",
     *     methods      = {"GET"}
     * )
     */
    public function searchByDocument(Request $request): Response
    {
        if (! $this->getUser()) {
            return new Response('', 404);
        }

        $search = $request->get('search', '');

        $users = $this->userRepository->findByDocument($search);

        if (0 === count($users)) {
            return $this->responseJson([]);
        }

        $data = [];

        foreach ($users as $user) {
            if (! $user) {
                continue;
            }

            $institutionsPrograms = (string)$user->getInstitutionsPrograms();
            if ('' !== $institutionsPrograms) {
                $institutionsPrograms = implode('<br/>', explode(') - (', rtrim(ltrim($institutionsPrograms, '('), ')')));
            }

            array_push($data, [
                'id' => $user->getId(),
                'text' => $user->getName(),
                'institutionsPrograms' => $institutionsPrograms,
            ]);
        }

        return $this->responseJson($data);
    }

    /**
     * @Route(
     *     path         = "/search_by_identifier",
     *     name         = "search_by_identifier",
     *     methods      = {"GET"}
     * )
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function searchByIdentifier(Request $request, TranslatorInterface $translator): Response
    {
        $identifier = $request->get('identifier', 'email');
        $value = $request->get('value', '');

        $data = $this->userRepository->findByIdentifier($identifier, $value);

        if (count($data) === 1) {
            return $this->responseJson($data[0]);
        }

        if (count($data) > 1) {
            return $this->responseJson([
                'error' => $translator->trans('Não foi possível realizar a pesquisa pelo campo e-mail, escolha outro campo para realizar a busca.'),
            ]);
        }

        return $this->responseJson([
            'error' => $translator->trans('Não encontrado.'),
        ]);
    }
}
