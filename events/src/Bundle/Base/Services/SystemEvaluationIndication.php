<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\SystemEvaluationIndications;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserAcademics;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Repository\SystemEvaluationIndicationsRepository;
use App\Bundle\Base\Repository\UserArticlesRepository;
use App\Bundle\Base\Repository\UserRepository;
use App\Bundle\Base\Repository\UserThemesDetailsRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SystemEvaluationIndication
 *
 * @package App\Bundle\Base\Services
 */
class SystemEvaluationIndication extends ServiceBase implements ServiceInterface
{

    /**
     * @var UserArticlesRepository
     */
    private $articlesRepository;

    /**
     * @var SystemEvaluationIndicationsRepository
     */
    private $repository;

    /**
     * @var UserThemesDetailsRepository
     */
    private $userThemesDetailsRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SystemEvaluationIndication constructor.
     *
     * @param SystemEvaluationIndicationsRepository $systemEvaluationIndicationsRepository
     * @param UserArticlesRepository $userArticlesRepository
     * @param UserThemesDetailsRepository $userThemesDetailsRepository
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        SystemEvaluationIndicationsRepository $systemEvaluationIndicationsRepository,
        UserArticlesRepository                $userArticlesRepository,
        UserThemesDetailsRepository           $userThemesDetailsRepository,
        UserRepository                        $userRepository,
        EntityManagerInterface                $entityManager
    )
    {
        $this->repository = $systemEvaluationIndicationsRepository;
        $this->articlesRepository = $userArticlesRepository;
        $this->userThemesDetailsRepository = $userThemesDetailsRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $articles
     * @param bool $splited
     *
     * @return array|int
     */
    public function getCountByArticle($articles, $splited = true)
    {
        if ($splited) {
            $result = [];
        } else {
            $result = 0;
        }

        if (! empty($articles)) {
            if ($splited) {
                foreach ($articles as $article) {
                    $result[$article->getId()] = $this->repository->count(['userArticles' => $article]);
                }
            } else {
                foreach ($articles as $article) {
                    $result += $this->repository->count(['userArticles' => $article]);
                }
            }
        }

        return $result;
    }

    /**
     * @param $articles
     * @param bool $splited
     *
     * @return array|int
     */
    public function getCountValidByArticle($articles, $splited = true)
    {
        if ($splited) {
            $result = [];
        } else {
            $result = 0;
        }

        if (! empty($articles)) {
            if ($splited) {
                foreach ($articles as $article) {
                    $result[$article->getId()] = $this->repository->count(['valid' => 1, 'userArticles' => $article]);
                }
            } else {
                foreach ($articles as $article) {
                    $result += $this->repository->count(['valid' => 1, 'userArticles' => $article]);
                }
            }
        }

        return $result;
    }

    /**
     * @param Edition $edition
     * @param User $user
     *
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountByUser(Edition $edition, User $user)
    {
        $result = (int)$this->repository->getCountByEdition($edition, $user);

        return $result;
    }

    public function calculateIndications(
        Edition       $edition,
        ?UserArticles $userArticles,
                      $division,
                      $theme,
                      $level,
                      $search
    )
    {
        // Autores do artigo devem ser retirados da lista
        $ignoreUser = [];
        foreach ($userArticles->getUserArticlesAuthors() as $author) {
            $ignoreUser[] = $author->getUserAuthorId()->getId();
        }

        $listUsers = [];

        // busca usuários já indicados
        // é importante que eles sempre apareçam no topo da lista
        $currentIndications = $this->repository->findBy(['userArticles' => $userArticles]);
        if (! empty($currentIndications)) {
            foreach ($currentIndications as $currentIndication) {
                if (! array_key_exists($currentIndication->getId(), $listUsers)) {
                    $listUsers[$currentIndication->getUserEvaluator()->getId()] = $currentIndication->getUserEvaluator();
                }
            }
        }

        // busca usuários por critérios específicos
        // identifier
        // name
        // phone
        // email
        if (! empty($search)) {
            $users = $this->userRepository->findByTeste([
                'ignoreUser' => $ignoreUser,
                'search' => $search,
            ]);

            if (! empty($users)) {
                foreach ($users as $user) {
                    if (! array_key_exists($user->getId(), $listUsers)) {
                        $listUsers[$user->getId()] = $user;
                    }
                }
            }
        }

        // busca pelos critérios do formulário
        if (! empty($division) || ! empty($theme) || ! empty($level)) {
            $criteria = [
                'ignoreUser' => $ignoreUser,
            ];

            if (! empty($division)) {
                $criteria['division'] = $division;
            }
            if (! empty($theme)) {
                $criteria['theme'] = $theme;
            }
            if (! empty($level)) {
                $criteria['level'] = $level;
            }

            $users = $this->userRepository->findByTeste($criteria);

            if (! empty($users)) {
                foreach ($users as $user) {
                    if (! array_key_exists($user->getId(), $listUsers)) {
                        $listUsers[$user->getId()] = $user;
                    }
                }
            }
        }

        if ($userArticles) {
            $users = $this->userRepository->findByTeste([
                'ignoreUser' => $ignoreUser,
                'division' => $userArticles->getDivisionId()->getId(),
                'theme' => $userArticles->getUserThemes()->getId(),
            ]);

            if (! empty($users)) {
                foreach ($users as $user) {
                    if (! array_key_exists($user->getId(), $listUsers)) {
                        $listUsers[$user->getId()] = $user;
                    }
                }
            }

            $users = $this->userRepository->findByTeste([
                'ignoreUser' => $ignoreUser,
                'division' => $userArticles->getDivisionId()->getId(),
                'theme' => $userArticles->getUserThemes()->getId(),
            ]);

            if (! empty($users)) {
                foreach ($users as $user) {
                    if (! array_key_exists($user->getId(), $listUsers)) {
                        $listUsers[$user->getId()] = $user;
                    }
                }
            }
        }

        return $listUsers;
    }


    /**
     * @param UserArticles|null $userArticles
     * @param array $indications
     * @param array $savedListObject
     *
     * @return bool
     */
    public function register(?UserArticles $userArticles, array $indications = [])
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(SystemEvaluationIndications::class, 'sei');
        $qb->andWhere($qb->expr()->eq('sei.userArticles', $userArticles->getId()));
        $qb->andWhere($qb->expr()->notIn('sei.userEvaluator', [-1 => 0] + $indications));
        $qb->getQuery()->execute();

        try {
            if (! empty($indications)) {
                $er = $this->entityManager->getRepository(SystemEvaluationIndications::class);
                foreach ($indications as $indication) {
                    $userEvaluator = $er->findOneBy([
                        'userArticles' => $userArticles->getId(),
                        'userEvaluator' => $indication,
                    ]);

                    // já está gravado no banco de dados, vai para o próximo
                    if ($userEvaluator) {
                        continue;
                    }

                    // usuário não existe, vai para o próximo
                    $userEvaluator = $this->userRepository->find($indication);
                    if (! $userEvaluator) {
                        continue;
                    }

                    $entity = new SystemEvaluationIndications();
                    $entity->setCreatedAt(new \DateTime());
                    $entity->setUserArticles($userArticles);
                    $entity->setUserEvaluator($userEvaluator);

                    $this->entityManager->persist($entity);
                    $this->entityManager->flush();
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $user
     *
     * @return float|int
     */
    public function graduationPoints($user)
    {
        $points = 0;

        /** @var UserAcademics $academic */
        foreach ($user->getAcademics() as $academic) {
            if (
                $academic->getLevel() == User::USER_LEVEL_MASTER
                && $academic->getStatus() == User::USER_ACADEMIC_STATUS_DONE
                // && ! is_null($academic->getStartDate())
                // && ! is_null($academic->getEndDate())
            ) {
                $points = $points + 3;
            }

            if (
                $academic->getLevel() == User::USER_LEVEL_GRADUATE
                && $academic->getStatus() == User::USER_ACADEMIC_STATUS_DONE
                // && ! is_null($academic->getStartDate())
                // && ! is_null($academic->getEndDate())
            ) {
                $points = $points + 2;
            }

            if (
                $academic->getLevel() == User::USER_LEVEL_GRADUATE
                && $academic->getStatus() == User::USER_ACADEMIC_STATUS_PROGRESS
                // && ! is_null($academic->getStartDate())
            ) {
                $points = $points + 0.5;
            }

            if (
                $academic->getLevel() == User::USER_LEVEL_MASTER
                && $academic->getStatus() == User::USER_ACADEMIC_STATUS_PROGRESS
                // && ! is_null($academic->getStartDate())
            ) {
                $points = $points + 0.5;
            }
        }

        return $points;
    }
}
