<?php

namespace App\Bundle\Base\Controller\Site;


use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserEvaluationArticles;
use App\Bundle\Base\Services\User as UserService;
use App\Bundle\Base\Traits\AccessControl;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/migration2")
 * Class Migration2Controller
 *
 * @package App\Bundle\Base\Controller\Site
 */
class Migration2Controller extends AbstractController
{
    use AccessControl;

    /**
     * @var int[]
     */
    protected $divisionsMap = [
        'ADI' => 1,
        'APB' => 2,
        'CON' => 3,
        'EOR' => 4,
        'EPQ' => 5,
        'ESO' => 6,
        'FIN' => 7,
        'ITE' => 8,
        'GOL' => 9,
        'GPR' => 10,
        'MKT' => 11,
    ];

    /**
     * MigrationController constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $entityClass
     */
    protected function truncateTable($entityClass)
    {
        return;
        $em = $this->getDoctrine()->getManager();

        $cmd = $em->getClassMetadata($entityClass);
        $connection = $em->getConnection();
        $connection->beginTransaction();

        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $connection->query('DELETE FROM ' . $cmd->getTableName());
            // $connection->query('TRUNCATE TABLE ' . $cmd->getTableName());
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
            echo $e;
            echo $cmd->getTableName();
            exit();
        }
    }

    /**
     * @param $str
     *
     * @return false|string[]
     */
    protected function unknownToArray($str)
    {
        $str = rtrim(ltrim(trim($str), '{'), '}');

        if (substr($str, 0, 1) == '"' && stripos($str, '","') !== false) {
            $arr = explode('","', $str);
        } else {
            $arr = explode(',', $str);
        }

        if (count($arr) > 0) {
            for ($i = 0; $i < count($arr); $i++) {
                $arr[$i] = trim(rtrim(ltrim($arr[$i]), '"'), '"');
            }
        }

        return $arr;
    }

    /**
     * @var string
     */
    protected $destinationDatabase = '';

    /**
     * @return \PDO
     */
    protected function getPDO()
    {
        if (empty($this->destinationDatabase)) {
            die('destination database not found');
        }

        set_time_limit(0);
        try {
            $pdo = new \PDO('mysql:host=anpad.mysql.database.azure.com;dbname=' . $this->destinationDatabase, 'dbuser@anpad', 'BD@zure!mga19');
            $pdo->query('SET SESSION group_concat_max_len = 1024*1024*1024;');
        } catch (\Exception $e) {
            die('PDO error.');
        }

        return $pdo;
    }

    /**
     * @param $state
     *
     * @return int
     */
    protected function getUfId($stateName)
    {
        $pdo = $this->getPDO();
        //$pdo->query("use 2021_evento_ema");

        $sth = $pdo->prepare("SELECT id FROM adm_uf WHERE nome='{$stateName}'");

        $sth->execute();
        $result = $sth->fetch(\PDO::FETCH_ASSOC);

        if (empty($result)) {
            return 0;
        }

        return (int)$result['id'];
    }

    /**
     * @return int
     */
    protected function getLastId()
    {
        $pdo = $this->getPDO();
        //$pdo->query("use 2021_evento_ema");

        $sth = $pdo->prepare("SELECT MAX(id) as id FROM cad_avaliadores;");

        $sth->execute();
        $result = $sth->fetch(\PDO::FETCH_ASSOC);

        if (empty($result)) {
            return 0;
        }

        return (int)$result['id'];
    }

    /**
     * @return false|string[]
     */
    protected function getAllIds()
    {
        $pdo = $this->getPDO();
        //$pdo->query("use 2021_evento_ema");

        $sth = $pdo->prepare("SELECT GROUP_CONCAT(id) as ids FROM cad_avaliadores;");

        $sth->execute();
        $result = $sth->fetch(\PDO::FETCH_ASSOC);

        if (empty($result)) {
            return ['0'];
        }

        return explode(',', $result['ids']);
    }

    /**
     * @param $status
     *
     * @return string
     */
    protected function getAcademicStatus($status)
    {
        if (User::USER_ACADEMIC_STATUS_PROGRESS == $status) {
            return 'em_andamento';
        } elseif (User::USER_ACADEMIC_STATUS_DONE == $status) {
            return 'concluido';
        }

        return '';
    }

    /**
     * @param $table
     */
    protected function pdoTruncateTable($table)
    {
        $pdo = $this->getPDO();

        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

        $pdo->exec('DELETE FROM ' . $table);

        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * @param $table
     * @param $fields
     *
     * @return string
     */
    protected function pdoInsert($table, $fields)
    {
        $pdo = $this->getPDO();

        $params = array_fill(0, count($fields), '?');

        $sql = sprintf('INSERT INTO %s(%s) VALUES(%s)', $table, implode(', ', array_keys($fields)), implode(', ', $params));

        var_dump($sql);

        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($fields));

        return $pdo->lastInsertId();
    }

    /**
     * @param $keyword
     *
     * @return int
     */
    protected function getKeywordId($keyword)
    {
        $pdo = $this->getPDO();
        //$pdo->query("use 2021_evento_ema");

        $sth = $pdo->prepare("SELECT id FROM adm_temas_interesse_palavraschave 
                                WHERE (
                                    palavra_pt = '{$keyword}' 
                                    OR palavra_en = '{$keyword}' 
                                    OR palavra_es = '{$keyword}'
                                )");

        $sth->execute();
        $result = $sth->fetch(\PDO::FETCH_ASSOC);

        if (empty($result)) {
            return 0;
        }

        return (int)$result['id'];
    }

    /**
     * @Route("/evaluators/{event}/{division?}", name="migration2_evaluators", methods={"GET"})
     *
     * @param Request $request
     * @param UserService $userService
     * @param string $event
     * @param int|null $division
     *
     * @return Response
     */
    public function evaluators(Request $request, UserService $userService, string $event, ?int $division)
    {
        /** @var User $user */
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $userService->isAdmin($user) && ! $userService->isUser($user)) {
            return new Response('', 404);
        }

        $this->destinationDatabase = sprintf('2022_evento_%s', $event);

        /*if ('true' === $request->get('truncate')) {
            $this->pdoTruncateTable('cad_avaliador_recusa_avaliar');
            $this->pdoTruncateTable('cad_avaliadores_teorias');
            $this->pdoTruncateTable('cad_avaliadores_palavras_chave');
            $this->pdoTruncateTable('cad_avaliadores_temas');
            $this->pdoTruncateTable('cad_avaliadores_metodos');
            $this->pdoTruncateTable('cad_avaliadores');
        }*/

        $em = $this->getDoctrine()->getManager();
        $er = $em->getRepository(UserEvaluationArticles::class);
        /** @var QueryBuilder $qb */
        $qb = $er->createQueryBuilder('uea');
        if (! empty($division)) {
            $qb->andWhere($qb->expr()->eq('uea.divisionFirstId', $division));
        } else {
            $qb->andWhere($qb->expr()->isNotNull('uea.divisionFirstId'));
        }
        $qb->andWhere($qb->expr()->notIn('uea.id', $this->getAllIds()));
        $qb->addGroupBy('uea.user');
        $userEvaluationArticles = $qb->getQuery()->getResult();

        /**
         * @var int $i
         * @var UserEvaluationArticles $userEvaluationArticle
         */
        // 'cad_avaliadores';
        foreach ($userEvaluationArticles as $i => $userEvaluationArticle) {
            //$userEvaluationArticle = new UserEvaluationArticles();

            $data = [
                'id' => $userEvaluationArticle->getId(),
                'nome' => $userEvaluationArticle->getUser()->getName(),
                'documento' => $userEvaluationArticle->getUser()->getIdentifier(),
                'email' => $userEvaluationArticle->getUser()->getEmail(),
                'telefone' => $userEvaluationArticle->getUser()->getPhone(),
                'celular' => $userEvaluationArticle->getUser()->getCellphone(),
                'senha' => $userEvaluationArticle->getUser()->getPassword(),
                'adm_uf_id' => $this->getUfId($userEvaluationArticle->getUser()->getCity()->getState()->getName()),
                'status' => 1,
                'dt_validacao' => date('Y-m-d'),
                'UsuarioID' => $userEvaluationArticle->getUser()->getId(),
                'graduacaoStatus' => '',
                'mestradoStatus' => '',
                'doutoradoStatus' => '',
            ];

            if ($userEvaluationArticle->getUser()->getAcademics()->count() > 0) {
                foreach ($userEvaluationArticle->getUser()->getAcademics() as $academic) {
                    if (User::USER_LEVEL_GRADUATE == $academic->getLevel()) {
                        $data['graduacaoStatus'] = $this->getAcademicStatus($academic->getStatus());
                    }

                    if (User::USER_LEVEL_MASTER == $academic->getLevel()) {
                        $data['mestradoStatus'] = $this->getAcademicStatus($academic->getStatus());
                    }

                    if (User::USER_LEVEL_DOCTORATE == $academic->getLevel()) {
                        $data['doutoradoStatus'] = $this->getAcademicStatus($academic->getStatus());
                    }
                }
            }

            // Realiza importação somente se nível for DOUTORADO
            if (trim($data['doutoradoStatus']) != '') {

                $this->pdoInsert('cad_avaliadores', $data);

                // 'cad_avaliadores_metodos';
                if ($userEvaluationArticle->getUser()->getMethods()->count() > 0) {
                    foreach ($userEvaluationArticle->getUser()->getMethods() as $userMethod) {
                        $methodData = [
                            'cad_avaliadores_id' => $userEvaluationArticle->getId(),
                            'adm_metodos_id' => $userMethod->getId(),
                        ];

                        $this->pdoInsert('cad_avaliadores_metodos', $methodData);
                    }
                } else {
                    var_dump('No Methods Found');
                }

                if ($userEvaluationArticle->getThemeFirstId()) {
                    // 'cad_avaliadores_temas';
                    $evaluatorThemeData = [
                        // 'id',
                        'cad_avaliadores_id' => $userEvaluationArticle->getId(),
                        'adm_temas_interesse_id' => $userEvaluationArticle->getThemeFirstId()->getId(),
                    ];

                    $this->pdoInsert('cad_avaliadores_temas', $evaluatorThemeData);
                }

                if ($userEvaluationArticle->getThemeSecondId()) {
                    // 'cad_avaliadores_temas';
                    $evaluatorThemeData = [
                        // 'id',
                        'cad_avaliadores_id' => $userEvaluationArticle->getId(),
                        'adm_temas_interesse_id' => $userEvaluationArticle->getThemeSecondId()->getId(),
                    ];

                    $this->pdoInsert('cad_avaliadores_temas', $evaluatorThemeData);
                }
            }

        }

        die('evaluators done.');
    }


}
