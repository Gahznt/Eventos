<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;
use App\Bundle\Base\Entity\SystemEvaluation as Entity;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Repository\SystemEvaluationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;

/**
 * Class DependentExample
 *
 * @package App\Bundle\Base\Services
 */
class SystemEvaluation extends ServiceBase implements ServiceInterface
{
    /**
     * @var string
     */
    private $uploadPath = UserArticles::STATS_PATH;

    /**
     * @var Environment
     */
    private $templating;

    /**
     * @var SystemEvaluationRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SystemEvaluation constructor.
     *
     * @param SystemEvaluationRepository $systemEvaluationRepository
     * @param EntityManagerInterface $entityManager
     * @param Environment $templating
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(
        SystemEvaluationRepository $systemEvaluationRepository,
        EntityManagerInterface $entityManager,
        Environment $templating,
        ParameterBagInterface $parameterBag
    )
    {
        $this->repository = $systemEvaluationRepository;
        $this->entityManager = $entityManager;
        $this->templating = $templating;
        $this->uploadPath = str_replace('#KERNEL#', $parameterBag->get('kernel.project_dir'), $this->uploadPath);
    }

    public function getByDivision($division)
    {
        return $this->repository->findByTeste(['division' => $division]);
    }

    public function calculateCriterias(UserArticles $userArticles)
    {

        $allCriterias = $this->repository->findByTeste(['userArticles' => $userArticles]);

        $primary = null;
        $secondary = null;
        $count = 0;

        if (count($allCriterias) > 1) {
            foreach ($allCriterias as $criteria) {

                $primary .= "{$criteria->getCriteriaFinal()}";
                $listCriterias = Entity::LIST_CRITERIAS;
                $prefixCriterias = Entity::LIST_CRITERIAS_PREFIX;
                $secondary = 0;

                foreach ($listCriterias as $listCriteria) {
                    $_key = 'get' . $prefixCriterias . ucfirst($listCriteria);
                    $_value = self::convertCriteriasPoints($criteria->{$_key}());
                    if (is_int($_value)) {
                        $secondary = $secondary + $_value;
                    }
                }

                if ($count == 0) {
                    $primary .= "_";
                }

                $count++;
            }

            $primary = self::tableOfCriterias($primary);
        }

        return ['primary' => $primary, 'secondary' => number_format($secondary, 2)];
    }


    public static function convertCriteriasPoints($criteria)
    {
        $array = array_column(Entity::CRITERIA_REAL_OPTIONS, $criteria);

        if (! empty($array)) {
            return end($array);
        }

        return null;
    }

    /**
     * @param null $key
     *
     * @return array|mixed
     */
    private static function tableOfCriterias($key = null)
    {

        $values = [
            "4_4" => 10,
            "4_3" => 9,
            "4_2" => 7,
            "4_1" => 1,
            "3_3" => 8,
            "3_2" => 6,
            "3_1" => 5,
            "2_2" => 4,
            "2_1" => 3,
            "1_1" => 2,
        ];

        if ($key) {
            if (in_array($key, $values)) {
                if (isset($values[$key])) {
                    return number_format($values[$key], 2);
                } else {
                    return 0;
                }
            }
        }

        return null;
    }

    /**
     * @param $userArticles
     * @param float|null $primary
     * @param float|null $secondary
     * @param array $score
     *
     * @return bool
     * @throws \Exception
     */
    public function setStatus(
        $userArticles,
        array $score = [],
        ?float $primary = null,
        ?float $secondary = null
    )
    {

        if (is_array($userArticles)) {
            foreach ($userArticles as $article) {

                $status = UserArticles::ARTICLE_EVALUATION_STATUS_WAITING;

                if (isset($score[$article->getId()])) {
                    $_id = $article->getId();
                    $_primary = (float)$score[$_id]['primary'];
                    $_secondary = (float)$score[$_id]['secondary'];

                    if ($_primary != 0) {
                        if ($_primary >= $primary && $_secondary >= $secondary && $article->getStatus() == UserArticles::ARTICLE_EVALUATION_STATUS_WAITING) {
                            $status = UserArticles::ARTICLE_EVALUATION_STATUS_APPROVED;
                        } elseif ($article->getStatus() == UserArticles::ARTICLE_EVALUATION_STATUS_WAITING) {
                            $status = UserArticles::ARTICLE_EVALUATION_STATUS_REPROVED;
                        }
                    }
                }

                try {
                    $article->setStatus($status);
                    $this->entityManager->persist($article);
                    $this->entityManager->flush();
                } catch (\Exception $e) {
                    return false;
                }
            }

            return true;
        } else {
            if (! $userArticles instanceof UserArticles) {
                throw new \Exception('userArticles is not valid');
            }

            $status = UserArticles::ARTICLE_EVALUATION_STATUS_REPROVED;

            if (isset($score[$userArticles->getId()])) {
                $_id = $userArticles->getId();
                $_primary = (float)$score[$_id]['primary'];
                $_secondary = (float)$score[$_id]['secondary'];

                if ($_primary >= $primary && $_secondary >= $secondary) {
                    $status = UserArticles::ARTICLE_EVALUATION_STATUS_APPROVED;
                }
            }

            try {
                $this->setStatus($status);
                $this->entityManager->persist($userArticles);
                $this->entityManager->flush();

                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
    }

    /**
     * @param UserArticles $id
     * @param Pdf $snappy
     *
     * @return PdfResponse|JsonResponse
     */
    public function generateArticleStatusPDF(UserArticles $id, Pdf $snappy)
    {
        $date = new \DateTime();
        $date = $date->getTimestamp();

        try {
            $pdfPath = $this->uploadPath . 'pdf';
            $htmlCertificate = $this->templating->render('@Base/article_submission/stats/pdf.html.twig', [
                'submission' => $id,
            ]);

            $_name = "STATS_{$id->getId()}_{$date}.pdf";
            $pdfFullPath = $pdfPath . "/" . $_name;

            $snappy->generateFromHtml($htmlCertificate, $pdfFullPath);

            if (file_exists($pdfPath)) {
                return new PdfResponse(
                    file_get_contents($pdfFullPath), $_name
                );
            }

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage() . ' - ' . $e->getFile() . ' - ' . $e->getLine()], 500);
        }
    }
}
