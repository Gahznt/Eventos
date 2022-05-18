<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Repository\UserArticlesRepository;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\UserArticles as Entity;
use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;

/**
 * Class UserArticles
 * @package App\Bundle\Base\Services
 */
class UserArticles extends ServiceBase implements ServiceInterface
{
    /**
     * @var UserArticlesRepository
     */
    private $repository;

    /**
     * UserArticles constructor.
     * @param UserArticlesRepository $userArticlesRepository
     */
    public function __construct(UserArticlesRepository $userArticlesRepository)
    {
        $this->repository = $userArticlesRepository;
    }

    public function getCountByEdition(Edition $edition)
    {
        return $this->repository->count(['editionId' => $edition]);
    }

    public function getApproved(Edition $edition)
    {
        return $this->repository->count(['editionId' => $edition, 'status' => Entity::ARTICLE_EVALUATION_STATUS_APPROVED]);
    }

    public function getByDivision(int $division): array
    {
        return $this->repository->getByDivision($division);
    }
}