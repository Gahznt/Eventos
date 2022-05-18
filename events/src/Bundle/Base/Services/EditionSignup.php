<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Repository\EditionSignupRepository;
use App\Bundle\Base\Repository\UserArticlesRepository;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;

/**
 * Class SignUp
 * @package App\Bundle\Base\Services
 */
class EditionSignup extends ServiceBase implements ServiceInterface
{
    /**
     * @var EditionSignupRepository
     */
    private $repository;

    /**
     * UserArticles constructor.
     * @param EditionSignupRepository $editionSignupRepository
     */
    public function __construct(EditionSignupRepository $editionSignupRepository)
    {
        $this->repository = $editionSignupRepository;
    }

    public function getCountByEdition(Edition $edition)
    {
        return $this->repository->count(['edition' => $edition]);
    }
}
