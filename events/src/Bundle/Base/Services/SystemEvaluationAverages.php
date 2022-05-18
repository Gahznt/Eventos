<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Entity\SystemEvaluationAverages as Entity;
use App\Bundle\Base\Entity\SystemEvaluationAveragesArticles;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Repository\SystemEvaluationAveragesRepository;
use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class DependentExample
 * @package App\Bundle\Base\Services
 */
class SystemEvaluationAverages extends ServiceBase implements ServiceInterface
{
    /**
     * @var SystemEvaluation
     */
    private $systemEvaluationService;

    /**
     * @var SystemEvaluationAveragesRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SystemEvaluationAverages constructor.
     * @param SystemEvaluationAveragesRepository $systemEvaluationAveragesRepository
     * @param SystemEvaluation $systemEvaluation
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        SystemEvaluationAveragesRepository $systemEvaluationAveragesRepository,
        SystemEvaluation $systemEvaluation,
        EntityManagerInterface $entityManager
    ) {
        $this->repository = $systemEvaluationAveragesRepository;
        $this->entityManager = $entityManager;
        $this->systemEvaluationService = $systemEvaluation;
    }

    /**
     * @param Division $division
     * @param Edition $edition
     * @param User $user
     * @param float $primary
     * @param float $secondary
     * @param array $articles
     * @param array $score
     * @return bool
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function registerWithArticles(
        Division $division,
        Edition $edition,
        User $user,
        float $primary,
        float $secondary,
        array $articles = [],
        array $score = []
    )
    {
        $this->entityManager->beginTransaction();

        try {

            $this->systemEvaluationService->setStatus($articles, $score, $primary, $secondary);

            $model = new Entity();
            $model->setCreatedAt(new \DateTime());
            $model->setDivision($division);
            $model->setEdition($edition);
            $model->setUser($user);
            $model->setPrimary($primary);
            $model->setSecondary($secondary);

            if (!empty($articles)) {
                foreach ($articles as $article) {
                    $_model = new SystemEvaluationAveragesArticles();
                    $_model->setUserArticles($article);
                    $model->addUserArticle($_model);
                }
            }

            $this->entityManager->persist($model);
            $this->entityManager->flush();

            $this->entityManager->getConnection()->commit();

            return true;
        }catch (\Exception $e){

            $this->entityManager->getConnection()->rollBack();
            return false;
        }
    }
}