<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\SystemEvaluationConfig as Entity;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Repository\SystemEvaluationConfigRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SystemEvaluationConfig
 *
 * @package App\Bundle\Base\Services
 */
class SystemEvaluationConfig extends ServiceBase implements ServiceInterface
{
    /**
     * @var $systemEvaluationConfigRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SystemEvaluationConfig constructor.
     *
     * @param SystemEvaluationConfigRepository $systemEvaluationConfigRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        SystemEvaluationConfigRepository $systemEvaluationConfigRepository,
        EntityManagerInterface           $entityManager
    )
    {
        $this->repository = $systemEvaluationConfigRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Edition $edition
     *
     * @return Entity|null
     */
    public function get(Edition $edition)
    {
        return $this->repository->findOneBy(['edition' => $edition, 'deletedAt' => null], ['createdAt' => 'desc']);
    }

    public function getArray(Edition $edition): array
    {
        $config = $this->get($edition);

        if (! $config) {
            return [];
        }

        return [
            'articeSubmissionAvaliable' => $config->getArticeSubmissionAvaliable(),
            'evaluateArticleAvaliable' => $config->getEvaluateArticleAvaliable(),
            'resultsAvaliable' => $config->getResultsAvaliable(),
            'articeFree' => $config->getArticeFree(),
            'automaticCertiticates' => $config->getAutomaticCertiticates(),
            'freeCertiticates' => $config->getFreeCertiticates(),
            'ensalementGeneral' => $config->getEnsalementGeneral(),
            'ensalementPriority' => $config->getEnsalementPriority(),
            'freeSections' => $config->getFreeSections(),
            'freeSignup' => $config->getFreeSignup(),
            'panelSubmissionAvailable' => $config->getPanelSubmissionAvailable(),
            'thesisSubmissionAvailable' => $config->getThesisSubmissionAvailable(),
            'detailedSchedulingAvailable' => $config->getDetailedSchedulingAvailable(),
        ];
    }

    /**
     * @param Edition $edition
     *
     * @return bool
     */
    public function articleSubmissionAvailable(Edition $edition)
    {
        $data = $this->get($edition);

        if ($data) {
            return $data->getArticeSubmissionAvaliable() === Entity::ENABLE;
        }

        return false;
    }

    /**
     * @param Edition $edition
     *
     * @return bool
     */
    public function evaluateArticleAvailable(Edition $edition)
    {
        $data = $this->get($edition);

        if ($data) {
            return $data->getEvaluateArticleAvaliable() === Entity::ENABLE;
        }

        return false;
    }

    /**
     * @param Edition $edition
     *
     * @return bool
     */
    public function resultsAvailable(Edition $edition)
    {
        $data = $this->get($edition);

        if ($data) {
            return $data->getResultsAvaliable() === Entity::ENABLE;
        }

        return false;
    }

    /**
     * @param Edition $edition
     *
     * @return bool
     */
    public function panelSubmissionAvailable(Edition $edition)
    {
        $data = $this->get($edition);

        if ($data) {
            return $data->getPanelSubmissionAvailable() === Entity::ENABLE;
        }

        return false;
    }

    /**
     * @param Edition $edition
     *
     * @return bool
     */
    public function articleFree(Edition $edition)
    {
        $data = $this->get($edition);

        if ($data) {
            return $data->getArticeFree() === Entity::ENABLE;
        }

        return false;
    }

    /**
     * @param Edition $edition
     *
     * @return bool
     */
    public function automaticCertiticates(Edition $edition)
    {
        $data = $this->get($edition);

        if ($data) {
            return $data->getAutomaticCertiticates() === Entity::ENABLE;
        }

        return false;
    }

    /**
     * @param Edition $edition
     *
     * @return bool
     */
    public function freeCertificates(Edition $edition)
    {
        $data = $this->get($edition);

        if ($data) {
            return $data->getFreeCertiticates() === Entity::ENABLE;
        }

        return false;
    }

    /**
     * @param Edition $edition
     *
     * @return bool
     */
    public function ensalementGeneralFlag(Edition $edition)
    {
        $data = $this->get($edition);

        if ($data) {
            return $data->getEnsalementGeneral() === Entity::ENABLE;
        }

        return false;
    }

    /**
     * @param Edition $edition
     *
     * @return bool
     */
    public function ensalementPriorityFlag(Edition $edition)
    {
        $data = $this->get($edition);

        if ($data) {
            return $data->getEnsalementPriority() === Entity::ENABLE;
        }

        return false;
    }

    /**
     * @param Edition $edition
     *
     * @return bool
     */
    public function freeSections(Edition $edition)
    {
        $data = $this->get($edition);

        if ($data) {
            return $data->getFreeSections() === Entity::ENABLE;
        }

        return false;
    }

    /**
     * @param Edition $edition
     *
     * @return bool
     */
    public function freeSignup(Edition $edition)
    {
        $data = $this->get($edition);

        if ($data) {
            return $data->getFreeSignup() === Entity::ENABLE;
        }

        return false;
    }

    /**
     * @param int $articleFree
     * @param int $resultsAvailable
     * @param int $evaluateArticleAvailable
     * @param int $articleSubmissionAvailable
     * @param string|null $ip
     * @param Edition|null $edition
     * @param User|null $user
     * @param int $automaticCertiticates
     * @param int $freeCertiticates
     * @param int $ensalementGeneral
     * @param int $ensalementPriority
     * @param int $freeSections
     * @param int $freeSignup
     * @param int $panelSubmissionAvailable
     * @param int $thesisSubmissionAvailable
     * @param int $detailedSchedulingAvailable
     *
     * @return bool
     */
    public function register(
        int      $articleFree = Entity::DISABLE,
        int      $resultsAvailable = Entity::DISABLE,
        int      $evaluateArticleAvailable = Entity::DISABLE,
        int      $articleSubmissionAvailable = Entity::DISABLE,
        ?string  $ip = null,
        ?Edition $edition = null,
        ?User    $user = null,
        int      $automaticCertiticates = Entity::DISABLE,
        int      $freeCertiticates = Entity::DISABLE,
        int      $ensalementGeneral = Entity::DISABLE,
        int      $ensalementPriority = Entity::DISABLE,
        int      $freeSections = Entity::DISABLE,
        int      $freeSignup = Entity::DISABLE,
        int      $panelSubmissionAvailable = Entity::DISABLE,
        int      $thesisSubmissionAvailable = Entity::DISABLE,
        int      $detailedSchedulingAvailable = Entity::DISABLE
    )
    {
        try {
            $systemEvaluationConfig = new Entity();
            $systemEvaluationConfig->setCreatedAt(new \DateTime());
            $systemEvaluationConfig->setArticeFree($articleFree);
            $systemEvaluationConfig->setEvaluateArticleAvaliable($evaluateArticleAvailable);
            $systemEvaluationConfig->setIp($ip);
            $systemEvaluationConfig->setEdition($edition);
            $systemEvaluationConfig->setResultsAvaliable($resultsAvailable);
            $systemEvaluationConfig->setArticeSubmissionAvaliable($articleSubmissionAvailable);
            $systemEvaluationConfig->setPanelSubmissionAvailable($panelSubmissionAvailable);
            $systemEvaluationConfig->setUser($user);
            $systemEvaluationConfig->setAutomaticCertiticates($automaticCertiticates);
            $systemEvaluationConfig->setFreeCertiticates($freeCertiticates);
            $systemEvaluationConfig->setEnsalementGeneral($ensalementGeneral);
            $systemEvaluationConfig->setEnsalementPriority($ensalementPriority);
            $systemEvaluationConfig->setFreeSections($freeSections);
            $systemEvaluationConfig->setFreeSignup($freeSignup);
            $systemEvaluationConfig->setThesisSubmissionAvailable($thesisSubmissionAvailable);
            $systemEvaluationConfig->setDetailedSchedulingAvailable($detailedSchedulingAvailable);

            $this->entityManager->persist($systemEvaluationConfig);

            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
