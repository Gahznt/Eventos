<?php

namespace App\Bundle\Base\Controller\Site;


use App\Bundle\Base\Entity\City;
use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\Institution;
use App\Bundle\Base\Entity\Keyword;
use App\Bundle\Base\Entity\Method;
use App\Bundle\Base\Entity\Program;
use App\Bundle\Base\Entity\State;
use App\Bundle\Base\Entity\Subsection;
use App\Bundle\Base\Entity\SystemEnsalementScheduling;
use App\Bundle\Base\Entity\SystemEnsalementSchedulingArticles;
use App\Bundle\Base\Entity\SystemEvaluation;
use App\Bundle\Base\Entity\SystemEvaluationAverages;
use App\Bundle\Base\Entity\SystemEvaluationAveragesArticles;
use App\Bundle\Base\Entity\SystemEvaluationConfig;
use App\Bundle\Base\Entity\SystemEvaluationIndications;
use App\Bundle\Base\Entity\SystemEvaluationLog;
use App\Bundle\Base\Entity\Theory;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserAcademics;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Entity\UserArticlesAuthors;
use App\Bundle\Base\Entity\UserArticlesFiles;
use App\Bundle\Base\Entity\UserArticlesKeywords;
use App\Bundle\Base\Entity\UserAssociation;
use App\Bundle\Base\Entity\UserEvaluationArticles;
use App\Bundle\Base\Entity\UserInstitutionsPrograms;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesBibliographies;
use App\Bundle\Base\Entity\UserThemesDetails;
use App\Bundle\Base\Entity\UserThemesEvaluationLog;
use App\Bundle\Base\Entity\UserThemesResearchers;
use App\Bundle\Base\Entity\UserThemesReviewers;
use App\Bundle\Base\Services\SystemEvaluation as SystemEvaluationService;
use App\Bundle\Base\Services\SystemEvaluationAverages as SystemEvaluationAveragesService;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/migration")
 * Class MigrationController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class MigrationController extends AbstractController
{
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
     * @param $sql
     */
    protected function executeQuery($sql)
    {
        $em = $this->getDoctrine()->getManager();

        $connection = $em->getConnection();
        $connection->beginTransaction();

        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $connection->query($sql);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
            echo $e;
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
     * @return \PDO
     */
    protected function getPDO()
    {
        set_time_limit(0);
        try {
            $pdo = new \PDO('mysql:host=anpad.mysql.database.azure.com;dbname=2020_evento_enanpad', 'report@anpad', 'Proj3toMigracao@2020');
            $pdo->query('SET SESSION group_concat_max_len = 1024*1024*1024;');
        } catch (\Exception $e) {
            die('PDO error.');
        }

        return $pdo;
    }

    /**
     * @return \PDO
     */
    protected function getPDO2()
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
     * @Route("/user_articles_authors/{page}", name="migration_user_articles_authors", methods={"GET"})
     */
    public function user_articles_authors(int $page = 0)
    {
        $pdo = $this->getPDO();

        $em = $this->getDoctrine()->getManager();

        if (0 === $page) {
            $this->truncateTable(UserArticlesAuthors::class);
        }

        $sth = $pdo->prepare("SELECT * FROM cad_autores ORDER BY id ASC");
        $sth->execute();
        $authors = $sth->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($authors as $i => $author) {
            if ($page > 0 && $page >= $i) {
                continue;
            }

            echo $i . ' ';

            if (empty($author['cad_artigos_id'])) {
                continue;
            }

            $article = $em->getRepository(UserArticles::class)->find($author['cad_artigos_id']);
            if (! $article) {
                continue;
            }

            $entity = $em->getRepository(UserArticlesAuthors::class)->find($author['id']);
            if (! $entity) {
                $entity = new UserArticlesAuthors();
                $entity->setId($author['id']);
            }

            $entity->setOrder($author['ordem']);
            $entity->setUserArticles($article);

            if (! empty($author['UsuarioID'])) {
                $user = $this->checkUser($author['UsuarioID']);
                if ($user) {
                    if (empty($user->getName())) {
                        $user->setName($author['nome']);
                    }
                    $entity->setUserAuthor($user);
                }
            }

            $em->persist($entity);
            $metadata = $em->getClassMetadata(UserArticlesAuthors::class);
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new AssignedGenerator());
            $em->flush();

            // usleep(500000);

            if ($i > 0 && $i % 5 === 0) {
                ?>
                <script type="text/javascript">
                    setTimeout(function () {
                        window.location = '/pt_br/migration/user_articles_authors/<?= $i ?>';
                    }, 500);
                </script>
                <?php
                exit;
            }
        }

        echo 'user_articles_authors done.';
        exit();
    }

    protected function checkUser($id)
    {
        if (! empty($id)) {
            $em = $this->getDoctrine()->getManager();
            $tmp = $em->getRepository(User::class)->find($id);
            if (! $tmp) {
                $tmp = new User();
                $tmp->setCreatedAt(new \DateTime());
                $tmp->setId($id);
                $tmp->setName('User ' . $id);
                $em->persist($tmp);
                $metadata = $em->getClassMetadata(User::class);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $metadata->setIdGenerator(new AssignedGenerator());
                $em->flush();
            }
            return $tmp;
        }

        return false;
    }

    protected function checkEdition($id)
    {
        if (! empty($id)) {
            $em = $this->getDoctrine()->getManager();
            $tmp = $em->getRepository(Edition::class)->find($id);
            if (! $tmp) {
                $tmp = new Edition();
                $tmp->setCreatedAt(new \DateTime());
                $tmp->setId($id);
                $tmp->setNamePortuguese('Edição ' . $id);
                $tmp->setDescriptionPortuguese('Descrição Edição ' . $id);
                $tmp->setDescriptionEnglish('US Descrição Edição ' . $id);
                $tmp->setDescriptionSpanish('ES Descrição Edição ' . $id);
                $tmp->setColor('bggBlue');
                $tmp->setStatus(1);
                $tmp->setDateStart(new \DateTime('2010-01-01'));
                $tmp->setDateEnd(new \DateTime('2030-01-01'));
                $em->persist($tmp);
                $metadata = $em->getClassMetadata(Edition::class);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $metadata->setIdGenerator(new AssignedGenerator());
                $em->flush();
            }
            return $tmp;
        }

        return false;
    }

    protected function getPGSqlPDO()
    {
        set_time_limit(0);
        try {
            return new \PDO('pgsql:host=anpad-postgresql.postgres.database.azure.com;port=5432;dbname=anpad', 'report@anpad-postgresql', 'Proj3toMigracao@2020');
        } catch (\Exception $e) {
            die('PDO error.');
        }
    }

    /**
     * @Route("/user_articles/{page}", name="migration_user_articles", methods={"GET"})
     */
    public function user_articles(int $page = 0)
    {
        $pgPdo = $this->getPGSqlPDO();
        $pdo = $this->getPDO();

        $em = $this->getDoctrine()->getManager();

        if (0 === $page) {
            /*
                delete from edition_signup_articles;
                delete from system_evaluation_averages;
                delete from system_evaluation_config;
                delete from system_evaluation_indications;
                delete from system_evaluation_log;
                delete from system_evaluation;

                delete from user_articles_keywords;
                delete from user_articles_files;
                delete from user_articles_authors;
                delete from user_articles;
             */
            // $this->truncateTable(EditionxSignUpArticles::class);

            $this->truncateTable(SystemEnsalementSchedulingArticles::class);

            $this->truncateTable(SystemEvaluationAveragesArticles::class);
            $this->truncateTable(SystemEvaluationAverages::class);
            // $this->truncateTable(SystemEvaluationAveragesSearch::class);
            $this->truncateTable(SystemEvaluationConfig::class);
            $this->truncateTable(SystemEvaluationIndications::class);
            // $this->truncateTable(SystemEvaluationIndicationsSearch::class);
            $this->truncateTable(SystemEvaluationLog::class);
            // $this->truncateTable(SystemEvaluationSubmissionsSearch::class);
            $this->truncateTable(SystemEvaluation::class);

            $this->truncateTable(UserArticlesAuthors::class);
            $this->truncateTable(UserArticlesFiles::class);
            $this->truncateTable(UserArticlesKeywords::class);
            $this->truncateTable(UserArticles::class);
        }

        $quantity = 20;
        $offset = $quantity * $page;

        $pgSth = $pgPdo->prepare("SELECT * FROM edicao_trabalhos
                                    LIMIT $quantity
                                    OFFSET $offset");
        $pgSth->execute();
        $data = $pgSth->fetchAll(\PDO::FETCH_ASSOC);

        if (count($data) > 0) {
            foreach ($data as $i => $item) {
                echo ($offset + $i) . ' ';

                $sth = $pdo->prepare("SELECT a.*, 
                                        GROUP_CONCAT(p.palavra_pt) as keywords_pt, 
                                        GROUP_CONCAT(p.palavra_en) as keywords_en, 
                                        GROUP_CONCAT(p.palavra_es) as keywords_es,
                                        d.sigla as div_sigla,
                                        t.titulo_portugues as tema_titulo
                                    FROM cad_artigos a 
                                    INNER JOIN adm_div_academica d ON
                                        (d.id = a.adm_div_academica_id)
                                        
                                    INNER JOIN adm_temas_interesse t ON
                                        (t.id = a.adm_temas_interesse_id)
                                        
                                    LEFT JOIN cad_temas_interesse_palavraschave tp ON
                                        (tp.cad_artigos_id = a.id) 
                                    LEFT JOIN adm_temas_interesse_palavraschave p ON
                                        (p.id = tp.adm_temas_interesse_palavraschave_id)
                                    WHERE 
                                        1=1 
                                        AND a.status='A' 
                                        AND a.id=:id
                                    ORDER BY 
                                        a.id ASC");

                $sth->bindValue(':id', $item['cod_edicao_trabalho']);
                $sth->execute();
                $_userArticle = $sth->fetch(\PDO::FETCH_ASSOC);

                if (! empty($_userArticle) && ! empty($_userArticle['id'])) {

                    $entity = $em->getRepository(UserArticles::class)->find($_userArticle['id']);
                    if (! $entity) {
                        $entity = new UserArticles();
                        $entity->setCreatedAt(new \DateTime());
                        $entity->setId($_userArticle['id']);
                    }

                    /*$children = $entity->getUserArticlesAuthors();
                    if (count($children) > 0) {
                        foreach ($children as $child) {
                            $em->remove($child);
                            $em->flush();
                        }
                    }

                    $children = $entity->getUserArticlesFiles();
                    if (count($children) > 0) {
                        foreach ($children as $child) {
                            $em->remove($child);
                            $em->flush();
                        }
                    }

                    $children = $entity->getUserArticlesKeywords();
                    if (count($children) > 0) {
                        foreach ($children as $child) {
                            $em->remove($child);
                            $em->flush();
                        }
                    }*/

                    if (! empty($_userArticle['quem_submeteu'])) {
                        $user = $this->checkUser($_userArticle['quem_submeteu']);
                        if ($user) {
                            $entity->setUserId($user);
                        }
                    }

                    if (! empty($item['cod_evento_edicao'])) {
                        $edition = $this->checkEdition($item['cod_evento_edicao']);
                        if ($edition) {
                            $entity->setEditionId($edition);
                        }
                    }

                    // faz o mapeamento pela sigla
                    if (! empty($_userArticle['div_sigla'])) {

                        $division = $em->getRepository(Division::class)->findOneBy([
                            'initials' => $_userArticle['div_sigla'],
                        ]);

                        $entity->setDivisionId($division);
                    }

                    if (! empty($_userArticle['adm_temas_interesse_id'])) {
                        /** @var UserThemesDetails $themeDetails */
                        $themeDetails = $em->getRepository(UserThemesDetails::class)->findOneBy([
                            'portugueseTitle' => $_userArticle['tema_titulo'],
                        ]);

                        $themeDetails->getUserThemes() && var_dump($themeDetails->getUserThemes()->getId());
                        $theme = $em->getRepository(UserThemes::class)->find($_userArticle['adm_temas_interesse_id']);
                        if (! $theme) {
                            var_dump($_userArticle['adm_temas_interesse_id']);
                            exit();
                            $theme = new UserThemes();
                            $theme->setId($_userArticle['adm_temas_interesse_id']);
                            if (! empty($division)) {
                                $theme->setDivision($division);
                            }
                            $em->persist($theme);
                            $metadata = $em->getClassMetadata(UserThemes::class);
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                            $metadata->setIdGenerator(new AssignedGenerator());
                            $em->flush();
                        }
                        $entity->setUserThemes($theme);
                        $entity->setOriginalUserThemes($theme);
                    }

                    if (! empty($_userArticle['adm_teorias_id'])) {
                        $theory = $em->getRepository(Theory::class)->find($_userArticle['adm_teorias_id']);
                        if (! $theory) {
                            $theory = new Theory();
                            $theory->setId($_userArticle['adm_teorias_id']);
                            $theory->setPortuguese('Theory ' . $_userArticle['adm_teorias_id']);
                            $theory->setEnglish('US Theory ' . $_userArticle['adm_teorias_id']);
                            $theory->setSpanish('ES Theory ' . $_userArticle['adm_teorias_id']);
                            $em->persist($theory);
                            $metadata = $em->getClassMetadata(Theory::class);
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                            $metadata->setIdGenerator(new AssignedGenerator());
                            $em->flush();
                        }
                        $entity->setTheoryId($theory);
                    }

                    if (! empty($_userArticle['adm_metodos_id'])) {
                        $method = $em->getRepository(Method::class)->find($_userArticle['adm_metodos_id']);
                        if (! $method) {
                            $method = new Method();
                            $method->setId($_userArticle['adm_metodos_id']);
                            $method->setPortuguese('Method ' . $_userArticle['adm_metodos_id']);
                            $method->setEnglish('US Method ' . $_userArticle['adm_metodos_id']);
                            $method->setSpanish('ES Method ' . $_userArticle['adm_metodos_id']);
                            $em->persist($method);
                            $metadata = $em->getClassMetadata(Method::class);
                            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                            $metadata->setIdGenerator(new AssignedGenerator());
                            $em->flush();
                        }
                        $entity->setMethodId($method);
                    }

                    $entity->setTitle($_userArticle['titulo']);
                    $entity->setResume($_userArticle['resumos']);
                    $entity->setFrame($_userArticle['adm_enquadramento_id']);
                    $entity->setAcknowledgment($_userArticle['agradecimentos']);

                    $entity->setLastId($_userArticle['adm_trab_decorrente_id']);

                    $entity->setLanguage($_userArticle['adm_idioma_id']);
                    if ($_userArticle['adm_idioma_id'] == 2) {
                        $entity->setEnglish(true);
                    } elseif ($_userArticle['adm_idioma_id'] == 3) {
                        $entity->setSpanish(true);
                    } else {
                        $entity->setPortuguese(true);
                    }

                    $entity->setStatus(2);

                    $keywordsPt = explode(',', $_userArticle['keywords_pt']);

                    if (! empty($keywordsPt)) {
                        $entity->setKeywords($keywordsPt);
                    }

                    $file = new UserArticlesFiles();
                    $file->setCreatedAt(new \DateTime());
                    $file->setPath($item['caminho_pdf']);
                    $entity->addUserArticlesFile($file);

                    $em->persist($entity);
                    $metadata = $em->getClassMetadata(UserArticles::class);
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                    $metadata->setIdGenerator(new AssignedGenerator());
                    $em->flush();

                    $keywordsEn = explode(',', $_userArticle['keywords_en']);
                    $keywordsEs = explode(',', $_userArticle['keywords_es']);

                    if (! empty($keywordsPt) && ! empty($theme) && ! empty($division)) {
                        foreach ($keywordsPt as $k => $v) {
                            if (empty($keywordsPt[$k]) || empty($keywordsEn[$k]) || empty($keywordsEs[$k])) {
                                continue;
                            }

                            $keyword = $em->getRepository(Keyword::class)->findOneBy([
                                'portuguese' => $keywordsPt[$k],
                                'division' => $division,
                                'theme' => $theme,
                            ]);

                            if (! $keyword) {
                                $keyword = new Keyword();
                                $keyword->setTheme($theme);
                                $keyword->setDivision($division);
                                $keyword->setPortuguese($keywordsPt[$k]);
                                $keyword->setEnglish($keywordsEn[$k]);
                                $keyword->setSpanish($keywordsEs[$k]);
                                $em->persist($keyword);
                                $em->flush();
                            }

                            $userArticlesKeywords = new UserArticlesKeywords();
                            $userArticlesKeywords->setUserArticlesId($entity);
                            $userArticlesKeywords->setKeywords($keyword);

                            $em->persist($userArticlesKeywords);
                            $em->flush();
                        }
                    }

                    // usleep(500000);
                }
            }

            ?>
            <script type="text/javascript">
                setTimeout(function () {
                    window.location = '/pt_br/migration/user_articles/<?= $page + 1 ?>';
                }, 200);
            </script>
            <?php
            exit;
        }

        echo 'user_articles done.';
        exit;
    }

    /**
     * @Route("/subsections/{page}", name="migration_subsections", methods={"GET"})
     *
     */
    public function subsections(int $page = 0)
    {
        $pdo = $this->getPDO();

        $em = $this->getDoctrine()->getManager();

        if (0 === $page) {
            $this->truncateTable(Subsection::class);
        }

        $file = dirname(dirname(dirname(__FILE__))) . '/Resources/dump/edicao_subsecoes_20201110.json';
        $json = json_decode(file_get_contents($file), true);

        $data = $json['RECORDS'];

        foreach ($data as $i => $item) {
            if ($page > 0 && $page >= $i) {
                continue;
            }

            echo $i . ' ';

            $entity = $em->getRepository(Subsection::class)->find($item['cod_edicao_subsecao']);
            if (! $entity) {
                $entity = new Subsection();
                $entity->setCreatedAt(new \DateTime());
                $entity->setId($item['cod_edicao_subsecao']);
            }

            if (! empty($item['cod_evento_edicao'])) {
                $edition = $this->checkEdition($item['cod_evento_edicao']);
                if ($edition) {
                    $entity->setEdition($edition);
                }
            }

            $entity->setStatus($item['disponivel'] == 't');
            $entity->setIsHomolog($item['fake'] == 't');
            $entity->setIsHighlight($item['capa'] == 't');
            $entity->setUserType($item['cod_tipo_subsecao']);
            $entity->setPosition($item['ordem']);

            $name = $this->unknownToArray($item['nome']);
            if (is_array($name) && count($name) == 3) {
                $entity->setNamePortuguese($name[0]);
                $entity->setNameEnglish($name[1]);
                $entity->setNameSpanish($name[2]);
            }

            $frontCall = $this->unknownToArray($item['chamada_capa']);
            if (is_array($frontCall) && count($frontCall) == 3) {
                $entity->setFrontCallPortuguese($frontCall[0]);
                $entity->setFrontCallEnglish($frontCall[1]);
                $entity->setFrontCallSpanish($frontCall[2]);
            }

            $description = $this->unknownToArray($item['conteudo']);
            if (is_array($description) && count($description) == 3) {
                $entity->setDescriptionPortuguese($description[0]);
                $entity->setDescriptionEnglish($description[1]);
                $entity->setDescriptionSpanish($description[2]);
            }

            $em->persist($entity);
            $metadata = $em->getClassMetadata(Subsection::class);
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new AssignedGenerator());
            $em->flush();

            // usleep(500000);

            if ($i > 0 && $i % 5 === 0) {
                ?>
                <script type="text/javascript">
                    setTimeout(function () {
                        window.location = '/pt_br/migration/subsections/<?= $i ?>';
                    }, 500);
                </script>
                <?php
                exit;
            }
        }

        echo 'subsections done.';
        exit;
    }

    /**
     * @param $title
     *
     * @return UserThemes|null
     */
    protected function getUserThemes($title)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var UserThemesDetails $details */
        $details = $em->getRepository(UserThemesDetails::class)->findOneBy([
            'portugueseTitle' => $title,
        ]);

        if ($details) {
            return $details->getUserThemes();
        }

        return null;
    }

    /**
     * @Route("/user_themes/{page}", name="migration_user_themes", methods={"GET"})
     */
    public function user_themes(int $page = 0)
    {
        $pdo = $this->getPDO();
        $pdo->query("use 2021_evento_temas");

        $em = $this->getDoctrine()->getManager();

        if (0 === $page) {
            /*
                delete from user_theme_keyword;
                delete from user_themes_bibliographies;
                delete from user_themes_reviewers;
                delete from user_themes_researchers;
                delete from user_themes_evaluation_log;
                delete from user_themes_details;
                delete from user_themes;
             */
            // $this->truncateTable(SystemEnsalementSchedulingArticles::class);
            // $this->truncateTable(SystemEnsalementScheduling::class);

            // $this->truncateTable(UserThemeKeyword::class);
            // $this->truncateTable(UserThemesBibliographies::class);
            // $this->truncateTable(UserThemesReviewers::class);
            // $this->truncateTable(UserThemesResearchers::class);
            // $this->truncateTable(UserThemesEvaluationLog::class);
            $this->truncateTable(UserThemesDetails::class);
            // $this->truncateTable(UserThemes::class);
        }

        $quantity = 20;
        $offset = $quantity * $page;

        $sth = $pdo->prepare("SELECT t.*, 
                                        GROUP_CONCAT(p.palavra_pt) as keywords_pt, 
                                        GROUP_CONCAT(p.palavra_en) as keywords_en, 
                                        GROUP_CONCAT(p.palavra_es) as keywords_es,
                                        d.sigla as div_sigla,
                                        xx.id as _id
                                    FROM cad_submissoes t
                                    INNER JOIN ANPADid.adm_temas_interesse xx ON 
                                        (xx.titulo_portugues = t.titulo_pt)
                                    INNER JOIN adm_div_academica d ON
                                        (d.id = t.adm_div_academica_id)
                                    LEFT JOIN cad_submissoes_palavras_chave p ON
                                        (p.cad_submissoes_id = t.id AND p.status IS NULL)
                                    WHERE 
                                        1=1 
                                        AND t.adm_movimentacao_id=17

									GROUP BY t.id

                                    ORDER BY 
                                        t.ordem ASC
                                    LIMIT $quantity
                                    OFFSET $offset");
        $sth->execute();
        $themes = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (count($themes) > 0) {
            foreach ($themes as $i => $theme) {
                echo ($offset + $i) . ' ';

                if (empty($theme['_id'])) {
                    continue;
                }

                /** @var UserThemesDetails $details *
                 * $details = $em->getRepository(UserThemesDetails::class)->findOneBy([
                 * 'portugueseTitle' => $theme['titulo_pt'],
                 * ]);
                 *
                 * $entity = $this->getUserThemes($theme['titulo_pt']);*/

                $entity = $em->getRepository(UserThemes::class)->find($theme['_id']);

                if (! $entity) {
                    $entity = new UserThemes();
                    $entity->setId($theme['_id']);
                }

                // faz o mapeamento pela sigla
                if (! empty($theme['div_sigla'])) {

                    $division = $em->getRepository(Division::class)->findOneBy([
                        'initials' => $theme['div_sigla'],
                    ]);

                    $entity->setDivision($division);
                }

                $entity->setStatus(1);
                $entity->setPosition($theme['ordem']);

                $em->persist($entity);
                $metadata = $em->getClassMetadata(UserThemes::class);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $metadata->setIdGenerator(new AssignedGenerator());
                $em->flush();

                /**
                 *
                 *
                 * UserThemesDetails
                 *
                 *
                 */
                // usleep(500000);

                /*$children = $em->getRepository(UserThemesDetails::class)->findBy([
                    'userThemes' => $entity->getId(),
                ]);
                if (count($children) > 0) {
                    foreach ($children as $child) {
                        $em->remove($child);
                        $em->flush();
                    }
                }*/

                $keywordsPt = explode(',', $theme['keywords_pt']);
                $keywordsEn = explode(',', $theme['keywords_en']);
                $keywordsEs = explode(',', $theme['keywords_es']);

                /*$details = $entity->getDetails();

                if(!$details) {
                    $details = new UserThemesDetails();
                    $details->setUserThemes($entity);
                }*/

                $details = new UserThemesDetails();
                $details->setUserThemes($entity);
                $details->setPortugueseDescription($theme['desc_pt']);
                $details->setEnglishDescription($theme['desc_en']);
                $details->setSpanishDescription($theme['desc_es']);
                $details->setPortugueseTitle($theme['titulo_pt']);
                $details->setEnglishTitle($theme['titulo_en']);
                $details->setSpanishTitle($theme['titulo_es']);
                if (! empty($keywordsPt)) {
                    $details->setPortugueseKeywords(json_encode($keywordsPt));
                }
                if (! empty($keywordsEn)) {
                    $details->setEnglishKeywords(json_encode($keywordsEn));
                }
                if (! empty($keywordsEs)) {
                    $details->setSpanishKeywords(json_encode($keywordsEs));
                }
                $details->setCreatedAt(new \DateTime($theme['dt_submissao']));
                $em->persist($details);
                $em->flush();


                /**
                 *
                 *
                 * UserThemesBibliographies
                 *
                 *
                 */
                // usleep(500000);

                $children = $em->getRepository(UserThemesBibliographies::class)->findBy([
                    'userThemes' => $entity,
                ]);
                if (count($children) > 0) {
                    foreach ($children as $child) {
                        $em->remove($child);
                        $em->flush();
                    }
                }

                for ($x = 1; $x <= 10; $x++) {
                    if (empty($theme['bio' . $x])) {
                        continue;
                    }

                    $bibli = new UserThemesBibliographies();
                    $bibli->setUserThemes($entity);
                    $bibli->setName($theme['bio' . $x]);

                    $em->persist($bibli);
                    $em->flush();
                }

                /**
                 *
                 *
                 * UserThemesResearchers
                 *
                 *
                 */
                // usleep(500000);

                $children = $em->getRepository(UserThemesResearchers::class)->findBy([
                    'userThemes' => $entity,
                ]);
                if (count($children) > 0) {
                    foreach ($children as $child) {
                        $em->remove($child);
                        $em->flush();
                    }
                }

                $sth = $pdo->prepare("SELECT * FROM cad_submissoes_proponentes 
                                        WHERE cad_submissoes_id={$theme['id']} 
                                        ORDER BY ordem");
                $sth->execute();
                $researchers = $sth->fetchAll(\PDO::FETCH_ASSOC);

                foreach ($researchers as $researcher) {
                    $entityResearcher = $em->getRepository(UserThemesResearchers::class)->find($researcher['id']);
                    if (! $entityResearcher) {
                        $entityResearcher = new UserThemesResearchers();
                        // $entityResearcher->setId($researcher['id']);
                    }

                    $entityResearcher->setUserThemes($entity);

                    $user = $this->checkUser($researcher['UsuarioID']);
                    if ($user) {
                        /*if (empty($user->getName())) {
                            $user->setName($researcher['nome']);
                        }*/
                        $entityResearcher->setResearcher($user);
                    }

                    $em->persist($entityResearcher);
                    //$metadata = $em->getClassMetadata(UserThemesResearchers::class);
                    //$metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                    //$metadata->setIdGenerator(new AssignedGenerator());
                    $em->flush();
                }

                /**
                 *
                 *
                 * UserThemesReviewers
                 *
                 *
                 */
                // usleep(500000);

                $children = $em->getRepository(UserThemesReviewers::class)->findBy([
                    'userThemes' => $entity,
                ]);
                if (count($children) > 0) {
                    foreach ($children as $child) {
                        $em->remove($child);
                        $em->flush();
                    }
                }

                $sth = $pdo->prepare("SELECT r.*, u.nome as adm_uf_nome 
                                    FROM cad_submissoes_revisor r 
                                        INNER JOIN adm_uf u ON 
                                        (u.id = r.adm_uf_id)
                                    WHERE cad_submissoes_id={$theme['id']} ORDER BY ordem");
                $sth->execute();
                $reviewers = $sth->fetchAll(\PDO::FETCH_ASSOC);

                foreach ($reviewers as $reviewer) {
                    $entityReviewer = $em->getRepository(UserThemesReviewers::class)->find($reviewer['id']);
                    if (! $entityReviewer) {
                        $entityReviewer = new UserThemesReviewers();
                        $entityReviewer->setId($reviewer['id']);
                    }

                    $entityReviewer->setUserThemes($entity);
                    $entityReviewer->setName($reviewer['nome']);
                    $entityReviewer->setLinkLattes($reviewer['lattes']);
                    $entityReviewer->setEmail($reviewer['email']);
                    $entityReviewer->setPhone($reviewer['tel_fixo']);
                    $entityReviewer->setCellphone($reviewer['tel_celular']);
                    $entityReviewer->setInstitute($reviewer['adm_instituicao_id']);
                    $entityReviewer->setProgram($reviewer['adm_programa_id']);
                    $entityReviewer->setState($reviewer['adm_uf_nome']);

                    $em->persist($entityReviewer);
                    $metadata = $em->getClassMetadata(UserThemesReviewers::class);
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                    $metadata->setIdGenerator(new AssignedGenerator());
                    $em->flush();
                }


                /**
                 *
                 *
                 * UserThemesEvaluationLog
                 *
                 *
                 */
                // usleep(500000);

                $children = $em->getRepository(UserThemesEvaluationLog::class)->findBy([
                    'userThemes' => $entity,
                ]);
                if (count($children) > 0) {
                    foreach ($children as $child) {
                        $em->remove($child);
                        $em->flush();
                    }
                }

                $sth = $pdo->prepare("SELECT l.*, m.nome as action 
                                    FROM log_artigo l 
                                        INNER JOIN adm_movimentacao m ON 
                                        (m.id = l.adm_movimentacao_id)
                                    WHERE cad_artigos_id={$theme['id']} ORDER BY data ASC");
                $sth->execute();
                $logs = $sth->fetchAll(\PDO::FETCH_ASSOC);


                foreach ($logs as $log) {
                    $entityLog = $em->getRepository(UserThemesEvaluationLog::class)->find($log['id']);
                    if (! $entityLog) {
                        $entityLog = new UserThemesEvaluationLog();
                        $entityLog->setId($log['id']);
                        $entityLog->setCreatedAt(new \DateTime($log['data']));
                    }

                    $entityLog->setUserThemes($entity);
                    $entityLog->setAction($log['action']);
                    $entityLog->setVisibleAuthor(1);

                    $em->persist($entityLog);
                    $metadata = $em->getClassMetadata(UserThemesEvaluationLog::class);
                    $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                    $metadata->setIdGenerator(new AssignedGenerator());
                    $em->flush();
                }


                // usleep(500000);
                $children = $em->getRepository(SystemEnsalementScheduling::class)->findBy([
                    'userThemes' => $entity,
                ]);
                if (count($children) > 0) {
                    /** @var SystemEnsalementScheduling $child */
                    foreach ($children as $child) {
                        $__children = $child->getArticles();
                        if (count($__children) > 0) {
                            foreach ($__children as $__child) {
                                $em->remove($__child);
                                $em->flush();
                            }
                        }
                        $em->remove($child);
                        $em->flush();
                    }
                }

                // usleep(500000);
            }

            ?>
            <script type="text/javascript">
                setTimeout(function () {
                    window.location = '/pt_br/migration/user_themes/<?= $page + 1 ?>';
                }, 100);
            </script>
            <?php
            exit;
        }

        echo 'user_themes done.';
        exit();
    }

    /**
     * @Route("/system_evaluation/{eventName}/{page}", name="migration_system_evaluation", methods={"GET"})
     */
    public function system_evaluation(string $eventName, int $page = 0, SystemEvaluationService $systemEvaluationService, SystemEvaluationAveragesService $systemEvaluationAveragesService)
    {
        $editionIds = [
            'eneo' => 117,
            'enapg' => 119,
            'simpoi' => 118,
            'enanpad' => 120,
        ];

        if (empty($editionIds[$eventName])) {
            die('Id da edição inválido');
        }

        $editionId = $editionIds[$eventName];

        $this->destinationDatabase = sprintf('2022_evento_%s', $eventName);

        $pdo = $this->getPDO2();

        $em = $this->getDoctrine()->getManager();

        if (0 === $page) {
            // $this->truncateTable(SystemEvaluationAveragesArticles::class);
            // $this->truncateTable(SystemEvaluationAverages::class);
            // $this->truncateTable(SystemEvaluation::class);

            $this->executeQuery("DELETE SE FROM system_evaluation as SE INNER JOIN user_articles as UA ON UA.id = SE.user_articles_id WHERE UA.edition_id = {$editionId}");
            $this->executeQuery("DELETE FROM system_evaluation_averages where edition_id = {$editionId}");
            $this->executeQuery('DELETE FROM system_evaluation_averages_articles where system_evaluation_averages_id NOT IN (SELECT id FROM system_evaluation_averages)');
        }

        $quantity = 100;
        $offset = $quantity * $page;

        $sth = $pdo->prepare("SELECT 
                                des.id,
                                avl.UsuarioID as user_owner_id,
                                des.cad_artigos_id as user_articles_id,
                                -- CONCAT('[', GROUP_CONCAT(ava.adm_quesitos_id), ']') as criteria_key,
                                CONCAT('{', GROUP_CONCAT('\"', que.id, '\":\"', REPLACE(aav.nome, '\"', ''), '\"'), '}') as criteria_value,
                                GROUP_CONCAT(DISTINCT(TRIM(ava.parecer))) as justification,
                                GROUP_CONCAT(DISTINCT(erf.`data`)) as format_error_at,
                                GROUP_CONCAT(DISTINCT(erf.motivo)) as format_error_justification,
                                GROUP_CONCAT(DISTINCT(log.data)) as reject_at, 
								GROUP_CONCAT(DISTINCT(log.info)) as reject_justification,
       
                                art.adm_temas_interesse_id,
                                art.adm_movimentacao_id as _status
                            
                            FROM cad_designacao as des
                                
                            INNER JOIN cad_avaliadores as avl
                                ON avl.id = des.cad_avaliadores_id
                            
                            INNER JOIN cad_artigos art
                                ON art.id = des.cad_artigos_id
                                
                            LEFT JOIN cad_avaliacoes as ava
                                ON ava.cad_avaliadores_id = des.cad_avaliadores_id
                                AND ava.cad_artigos_id = des.cad_artigos_id
                            
                            LEFT JOIN adm_avaliacoes as aav 
                                ON aav.id = ava.adm_avaliacoes_id
                                
                            LEFT JOIN adm_quesitos as que
                                ON que.id = ava.adm_quesitos_id 
                                AND que.adm_fases_ava_id = ava.adm_fases_ava_id
                                AND que.adm_enquadramento_id = art.adm_enquadramento_id
                            
                            LEFT JOIN cad_artigos_erro_formato as erf
                                ON erf.cad_artigos_id = des.cad_artigos_id
                                AND erf.cad_avaliadores_id = des.cad_avaliadores_id
                                -- AND erf.acao = 'negado'
                                
                            LEFT JOIN log_artigo as log 
                                ON log.cad_artigos_id = des.cad_artigos_id 
                                AND log.adm_movimentacao_id = 16

                            WHERE 
                                ava.teste IS NULL 
                            GROUP BY 
                                des.cad_artigos_id,
                                des.cad_avaliadores_id
                            ORDER BY 
                                des.cad_artigos_id ASC,
                                que.ordem ASC

                            LIMIT $quantity
                            OFFSET $offset");

        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (count($results) > 0) {
            foreach ($results as $i => $item) {
                echo ($offset + $i) . ' ';

                if (empty($item['id']) || empty($item['user_articles_id'])) {
                    continue;
                }

                //$entity = $em->getRepository(SystemEvaluation::class)->find($item['id']);
                //if (! $entity) {
                $entity = new SystemEvaluation();
                //$entity->setId($item['id']);
                $entity->setCreatedAt(new \DateTime());
                //}

                $user = $this->checkUser($item['user_owner_id']);
                if ($user) {
                    $entity->setUserOwner($user);
                }

                /** @var UserArticles $article */
                $article = $em->getRepository(UserArticles::class)->find($item['user_articles_id']);
                if (! $article) {
                    continue;
                }

                $entity->setUserArticles($article);

                $entity->setJustification(trim(ltrim(trim($item['justification']), ',')));

                // Ruim =     -1
                // Bom  =      1
                // Muito Bom = 2

                /*$arr = json_decode($item['criteria_value'], true);

                if (! empty($arr)) {
                    foreach ($arr as $key => $val) {
                        if ($val == 'Regular') {
                            $value = 'regular';
                        } elseif ($val == 'Bom') {
                            $value = 'good';
                        } elseif ($val == 'Fraco') {
                            $value = 'weak';
                        } elseif ($val == 'Muito Bom') {
                            $value = 'very_good';
                        } elseif ($val == 'Rejeitar') {
                            $value = 1;
                        } elseif ($val == 'Aprovar se houver baixa competição') {
                            $value = 2;
                        } elseif ($val == 'Aprovar') {
                            $value = 3;
                        } elseif ($val == 'Aprovar incondicionalmente') {
                            $value = 4;
                        } else {
                            continue;
                        }

                        switch ($key) {
                            case 13:
                            case 22:
                                $entity->setCriteriaOne($value);
                                break;
                            case 14:
                            case 23:
                                $entity->setCriteriaTwo($value);
                                break;
                            case 15:
                            case 24:
                                $entity->setCriteriaThree($value);
                                break;
                            case 16:
                            case 25:
                                $entity->setCriteriaFour($value);
                                break;
                            case 17:
                            case 26:
                                $entity->setCriteriaFive($value);
                                break;
                            case 27:
                                $entity->setCriteriaSix($value);
                                break;
                            case 28:
                                $entity->setCriteriaSeven($value);
                                break;
                            case 18:
                            case 29:
                                $entity->setCriteriaEight($value);
                                break;
                            case 19:
                            case 30:
                                $entity->setCriteriaNine($value);
                                break;
                            case 67:
                            case 68:
                                $entity->setCriteriaTen($value);
                                break;
                            case 20:
                            case 31:
                            case 43:
                            case 65:
                                $entity->setCriteriaFinal($value);
                                break;
                        }
                    }
                }*/

                if (3 == $item['_status']) {
                    if (! empty($item['format_error_at'])) {
                        $item['format_error_at'] = explode(',', trim(ltrim(trim($item['format_error_at']), ',')))[0];
                        $entity->setFormatErrorAt(new \DateTime($item['format_error_at']));
                        $entity->setFormatErrorJustification($item['format_error_justification']);
                    }

                    $article->setStatus(3);
                    $em->persist($article);
                    $em->flush();
                }

                if (16 == $item['_status']) {
                    if (! empty($item['reject_at'])) {
                        $item['reject_at'] = explode(',', trim(ltrim(trim($item['reject_at']), ',')))[0];
                        $entity->setRejectAt(new \DateTime($item['reject_at']));
                        $entity->setRejectJustification($item['reject_justification']);
                    }

                    $article->setStatus(3);
                    $em->persist($article);
                    $em->flush();
                }

                // Trabalho aprovado
                if (17 == $item['_status']) {
                    $article->setStatus(2);
                    $em->persist($article);
                    $em->flush();
                }

                // Trabalho não selecionado para apresentação e publicação
                if (21 == $item['_status']) {
                    $article->setStatus(3);
                    $em->persist($article);
                    $em->flush();
                }


                if (
                    ! empty($item['adm_temas_interesse_id'])
                    && $item['adm_temas_interesse_id'] != $article->getUserThemes()->getId()
                ) {
                    /** @var UserThemes $theme */
                    $theme = $em->getRepository(UserThemes::class)->find($item['adm_temas_interesse_id']);
                    if ($theme) {
                        $article->setUserThemes($theme);
                        $em->persist($article);
                        $em->flush();
                    }
                }

                $em->persist($entity);
                // $metadata = $em->getClassMetadata(SystemEvaluation::class);
                // $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                // $metadata->setIdGenerator(new AssignedGenerator());
                $em->flush();


                $score = $systemEvaluationService->calculateCriterias($article);
                if (! empty($score['primary']) && ! empty($score['secondary'])) {
                    $model = new SystemEvaluationAverages();
                    $model->setCreatedAt(new \DateTime());
                    $model->setDivision($article->getDivisionId());
                    $model->setEdition($article->getEditionId());
                    $user = $this->checkUser($item['user_owner_id']);
                    if ($user) {
                        $model->setUser($user);
                    }
                    $model->setPrimary($score['primary']);
                    $model->setSecondary($score['secondary']);

                    $_model = new SystemEvaluationAveragesArticles();
                    $_model->setUserArticles($article);
                    $model->addUserArticle($_model);

                    $em->persist($model);
                    $em->flush();
                }
            }

            // usleep(500000);

            ?>
            <script type="text/javascript">
                setTimeout(function () {
                    window.location = '/pt_br/migration/system_evaluation/<?= $eventName ?>/<?= $page + 1 ?>';
                }, 500);
            </script>
            <?php
            exit;
        }

        die('system_evaluation done.');
    }

    /**
     * @Route("/system_evaluation_indications/{page}", name="migration_system_evaluation_indications", methods={"GET"})
     */
    public function system_evaluation_indications(int $page = 0)
    {
        $pdo = $this->getPDO();
        // $pdo->query("use 2019_evento_temas");

        $em = $this->getDoctrine()->getManager();

        if (0 === $page) {
            $this->truncateTable(SystemEvaluationIndications::class);
        }

        $quantity = 10;
        $offset = $quantity * $page;

        $sth = $pdo->prepare("SELECT 
                                des.id,
                                des.cad_artigos_id as user_articles_id,
                                avl.UsuarioID as user_evaluator_id,
                                des.data as created_at,
                                des.calcula as valid
                            
                            FROM cad_designacao as des
                            INNER JOIN cad_avaliadores as avl
                                ON avl.id = des.cad_avaliadores_id
                            ORDER BY 
                                des.cad_artigos_id ASC

                            LIMIT $quantity
                            OFFSET $offset");

        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (count($results) > 0) {
            foreach ($results as $i => $item) {
                echo ($offset + $i) . ' ';

                if (empty($item['id']) || empty($item['user_articles_id'])) {
                    continue;
                }

                $entity = $em->getRepository(SystemEvaluationIndications::class)->find($item['id']);
                if (! $entity) {
                    $entity = new SystemEvaluationIndications();
                    $entity->setId($item['id']);
                    $entity->setCreatedAt(new \DateTime());
                }

                $user = $this->checkUser($item['user_evaluator_id']);
                if ($user) {
                    $entity->setUserEvaluator($user);
                }

                /** @var UserArticles $article */
                $article = $em->getRepository(UserArticles::class)->find($item['user_articles_id']);
                if (! $article) {
                    continue;
                }

                $entity->setUserArticles($article);

                $entity->setValid($item['valid'] == 1);

                $entity->setCreatedAt(new \DateTime($item['created_at']));

                $em->persist($entity);
                $metadata = $em->getClassMetadata(SystemEvaluationIndications::class);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $metadata->setIdGenerator(new AssignedGenerator());
                $em->flush();
            }

            // usleep(50000);

            ?>
            <script type="text/javascript">
                setTimeout(function () {
                    window.location = '/pt_br/migration/system_evaluation_indications/<?= $page + 1 ?>';
                }, 500);
            </script>
            <?php
            exit;
        }

        die('system_evaluation_indications done.');
    }

    /**
     * @Route("/system_evaluation_log/{page}", name="migration_system_evaluation_log", methods={"GET"})
     */
    public function system_evaluation_log(int $page = 0)
    {
        $pdo = $this->getPDO();
        // $pdo->query("use 2019_evento_temas");

        $em = $this->getDoctrine()->getManager();

        if (0 === $page) {
            $this->truncateTable(SystemEvaluationLog::class);
        }

        $quantity = 10;
        $offset = $quantity * $page;

        $sth = $pdo->prepare("SELECT 
                                log.cad_artigos_id as user_articles_id,
                                log.adm_movimentacao_id as content,
                                mov.nome as alt_content,
                                log.data as created_at
                            FROM log_artigo log
                            LEFT JOIN adm_movimentacao mov
                                ON mov.id = log.adm_movimentacao_id 
																
                            WHERE 1=1
                                AND log.adm_movimentacao_id != 1
																
                            ORDER BY 
                                log.cad_artigos_id ASC,
								log.data ASC

                            LIMIT $quantity
                            OFFSET $offset");

        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (count($results) > 0) {
            foreach ($results as $i => $item) {
                echo ($offset + $i) . ' ';

                if (empty($item['user_articles_id'])) {
                    continue;
                }

                $evaluations = $em->getRepository(SystemEvaluation::class)->findBy([
                    'userArticles' => $item['user_articles_id'],
                ]);

                if (count($evaluations) > 0) {
                    /** @var SystemEvaluation $evaluation */
                    foreach ($evaluations as $evaluation) {
                        $entity = new SystemEvaluationLog();

                        $related = $em->getRepository(SystemEvaluation::class)->find($evaluation->getId());
                        $entity->setSystemEvaluation($related);

                        if ($related->getUserOwner()) {
                            $user = $this->checkUser($related->getUserOwner()->getId());
                            if ($user) {
                                $entity->setUserLog($user);
                            }
                        }

                        if (mb_strlen($item['content']) > 2) {
                            $entity->setContent($item['content']);
                        } elseif (mb_strlen($item['alt_content']) > 2) {
                            $entity->setContent($item['alt_content']);
                        }

                        $entity->setCreatedAt(new \DateTime($item['created_at']));

                        $em->persist($entity);
                        $em->flush();
                    }
                }
            }

            // usleep(50000);

            ?>
            <script type="text/javascript">
                setTimeout(function () {
                    window.location = '/pt_br/migration/system_evaluation_log/<?= $page + 1 ?>';
                }, 500);
            </script>
            <?php
            exit;
        }

        die('system_evaluation_log done.');
    }

    /**
     * @Route("/user_evaluation_articles/{page}", name="migration_user_evaluation_articles", methods={"GET"})
     */
    public function user_evaluation_articles(int $page = 0)
    {
        $pdo = $this->getPDO();
        $pdo->query("use ANPADid");

        $em = $this->getDoctrine()->getManager();

        if (0 === $page) {
            $this->truncateTable(UserEvaluationArticles::class);
        }

        $quantity = 100;
        $offset = $quantity * $page;

        $sth = $pdo->prepare("SELECT 
                                cad.divisao_principal as division_first_id,
                                cad.divisao_secundaria as division_second_id,
                            
                                GROUP_CONCAT(DISTINCT(kywd.portugues)) as keywords_pt, 
                                GROUP_CONCAT(DISTINCT(kywd.ingles)) as keywords_en, 
                                GROUP_CONCAT(DISTINCT(kywd.espanhol)) as keywords_es,
                            
                                cad.divisao_principal_tema as theme_first_id,
                                cad.divisao_secundaria_tema as theme_second_id,
                                
                                cad.UsuarioID as user_id
                            FROM 
                                Cadastro2 as cad
                            LEFT JOIN Cadastro2_palavras_chave as kwd 
                                ON kwd.Cadastro2_UsuarioID = cad.UsuarioID
                            LEFT JOIN adm_palavras_chave kywd
                                ON kywd.id = kwd.adm_palavras_chave_id
                            GROUP BY
                                cad.UsuarioID
                            ORDER BY
                                cad.UsuarioID ASC,
                                kwd.ordem ASC

                            LIMIT $quantity
                            OFFSET $offset");

        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (count($results) > 0) {
            foreach ($results as $i => $item) {
                echo ($offset + $i) . ' ';

                $entity = new UserEvaluationArticles();

                if (! empty($item['division_first_id'])) {
                    $division1 = $em->getRepository(Division::class)->find($item['division_first_id']);
                    if ($division1) {
                        $entity->setDivisionFirstId($division1);
                    }
                }
                if (! empty($item['division_second_id'])) {
                    $division2 = $em->getRepository(Division::class)->find($item['division_second_id']);
                    if ($division2) {
                        $entity->setDivisionSecondId($division2);
                    }
                }
                if (! empty($item['theme_first_id'])) {
                    $theme1 = $em->getRepository(UserThemes::class)->find($item['theme_first_id']);
                    if ($theme1) {
                        $entity->setThemeFirstId($theme1);
                    }
                }
                if (! empty($item['theme_second_id'])) {
                    $theme2 = $em->getRepository(UserThemes::class)->find($item['theme_second_id']);
                    if ($theme2) {
                        $entity->setThemeSecondId($theme2);
                    }
                }

                if (! empty($item['user_id'])) {
                    $user = $this->checkUser($item['user_id']);
                    if ($user) {
                        $entity->setUser($user);
                    }
                }

                $keywordsPt = explode(',', $item['keywords_pt']);
                $keywordsEn = explode(',', $item['keywords_en']);
                $keywordsEs = explode(',', $item['keywords_es']);

                if (! empty($keywordsPt) && $keywordsPt[0] != '') {
                    $entity2 = clone $entity;

                    $entity2->setPortuguese(true);
                    if (! empty($keywordsPt[0])) {
                        $entity2->setKeywordOne($keywordsPt[0]);
                    }
                    if (! empty($keywordsPt[1])) {
                        $entity2->setKeywordTwo($keywordsPt[1]);
                    }
                    if (! empty($keywordsPt[2])) {
                        $entity2->setKeywordThree($keywordsPt[2]);
                    }
                    if (! empty($keywordsPt[3])) {
                        $entity2->setKeywordFour($keywordsPt[3]);
                    }
                    if (! empty($keywordsPt[4])) {
                        $entity2->setKeywordFive($keywordsPt[4]);
                    }
                    if (! empty($keywordsPt[5])) {
                        $entity2->setKeywordSix($keywordsPt[5]);
                    }

                    $em->persist($entity2);
                    $em->flush();
                } elseif (! empty($keywordsEn) && $keywordsEn[0] != '') {
                    $entity2 = clone $entity;

                    $entity2->setEnglish(true);
                    if (! empty($keywordsEn[0])) {
                        $entity2->setKeywordOne($keywordsEn[0]);
                    }
                    if (! empty($keywordsEn[1])) {
                        $entity2->setKeywordTwo($keywordsEn[1]);
                    }
                    if (! empty($keywordsEn[2])) {
                        $entity2->setKeywordThree($keywordsEn[2]);
                    }
                    if (! empty($keywordsEn[3])) {
                        $entity2->setKeywordFour($keywordsEn[3]);
                    }
                    if (! empty($keywordsEn[4])) {
                        $entity2->setKeywordFive($keywordsEn[4]);
                    }
                    if (! empty($keywordsEn[5])) {
                        $entity2->setKeywordSix($keywordsEn[5]);
                    }

                    $em->persist($entity2);
                    $em->flush();
                } elseif (! empty($keywordsEs) && $keywordsEs[0] != '') {
                    $entity2 = clone $entity;

                    $entity2->setSpanish(true);
                    if (! empty($keywordsEs[0])) {
                        $entity2->setKeywordOne($keywordsEs[0]);
                    }
                    if (! empty($keywordsEs[1])) {
                        $entity2->setKeywordTwo($keywordsEs[1]);
                    }
                    if (! empty($keywordsEs[2])) {
                        $entity2->setKeywordThree($keywordsEs[2]);
                    }
                    if (! empty($keywordsEs[3])) {
                        $entity2->setKeywordFour($keywordsEs[3]);
                    }
                    if (! empty($keywordsEs[4])) {
                        $entity2->setKeywordFive($keywordsEs[4]);
                    }
                    if (! empty($keywordsEs[5])) {
                        $entity2->setKeywordSix($keywordsEs[5]);
                    }

                    $em->persist($entity2);
                    $em->flush();
                }
            }

            // usleep(50000);

            ?>
            <script type="text/javascript">
                setTimeout(function () {
                    window.location = '/pt_br/migration/user_evaluation_articles/<?= $page + 1 ?>';
                }, 200);
            </script>
            <?php
            exit;
        }

        die('user_evaluation_articles done.');
    }


    /**
     * @Route("/users/{page}", name="migration_users", methods={"GET"})
     * @param int $page
     */
    public function users(int $page = 0)
    {
        $pdo = $this->getPDO();
        $pdo->query("use ANPADid");

        $em = $this->getDoctrine()->getManager();

        if (0 === $page) {
            //$this->truncateTable(UserAcademics::class);
            //$this->truncateTable(UserInstitutionsPrograms::class);
        }

        $quantity = 400;
        $offset = $quantity * $page;

        $sth = $pdo->prepare("SELECT * FROM Cadastro2
                            LIMIT $quantity
                            OFFSET $offset");

        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (count($results) > 0) {
            foreach ($results as $i => $item) {
                echo ($offset + $i) . ' ';

                if (empty($item['CPF']) || empty($item['Senha'])) {
                    continue;
                }

                $entity = $this->checkUser($item['UsuarioID']);
                $entity->setCity($this->getCity(($item['Cidade']), ($item['Estado'])));
                $entity->setIdentifier(str_pad($this->clearInputInt($item['CPF']), 11, '0', STR_PAD_LEFT));
                $entity->setName($item['Nome']);
                $entity->setNickname($item['nome_cracha']);
                $entity->setEmail($item['EMail']);
                $entity->setBirthday(new \DateTime($item['Nascimento']));
                $entity->setPassword(password_hash($item['Senha'], PASSWORD_BCRYPT));
                $entity->setZipcode($item['CEP']);
                $entity->setStreet($item['Endereco']);
                $entity->setNumber($this->clearInputInt($item['Numero']));
                $entity->setComplement($item['Complemento']);
                $entity->setNeighborhood($item['Bairro']);
                $entity->setPhone($this->clearInputInt($item['Telefone']));
                $entity->setCellphone($this->clearInputInt($item['celular']));
                $entity->setRoles(['ROLE_USER_GUEST']);
                $entity->setNewsletterAssociated($item['divulgar_email'] == 'S' ? 1 : 0);
                $entity->setNewsletterEvents($item['aceita_receber_anpad'] == 'S' ? 1 : 0);
                $entity->setNewsletterPartners($item['aceita_receber_parceiros'] == 'S' ? 1 : 0);
                $entity->setRecordType($this->setUserType($item['TipoCadastro'], $item['Pais']));
                $entity->setStatus(1);
                $entity->setExtension($this->clearInputInt($item['Ramal']));
                $entity->setPayment(0);
                $entity->setUpdatedAt(new \DateTime($item['Alterado']));
                $entity->setCreatedAt(new \DateTime($item['Cadastrado']));

                foreach ($entity->getAcademics() as $academic) {
                    $entity->removeAcademic($academic);
                    $em->remove($academic);
                    $em->flush();
                }

                if ($item['graduacaoIngresso']) { // != null && $this->getProgram($item['graduacaoPrograma'])) {
                    $academics = new UserAcademics();
                    $academics->setUser($entity);
                    $academics->setLevel(2);
                    $academics->setStatus($item['graduacaoStatus'] == 'concluido' ? 1 : 2);
                    // $academics->setArea($item['graduacaoArea']);

                    // é outra
                    if (! $this->getInstitute($item['graduacaoInstituicao'])) {
                        // $academics->setInstitution($this->getInstitute(99999));
                        // $academics->setOtherInstitution($item['graduacaoInstituicao']);
                    } else {
                        // $academics->setInstitution($this->getInstitute($item['graduacaoInstituicao']));
                    }

                    // é outro
                    if (! $this->getProgram($item['graduacaoPrograma'])) {
                        // $academics->setProgram($this->getProgram(99999));
                        // $academics->setOtherProgram($item['graduacaoPrograma']);
                    } else {
                        // $academics->setProgram($this->getProgram($item['graduacaoPrograma']));
                    }

                    // $academics->setStartDate(new \DateTime($item['graduacaoIngresso']));
                    // $academics->setEndDate(new \DateTime($item['graduacaoConclusao']));

                    $entity->addAcademic($academics);
                }

                if ($item['mestradoIngresso']) { // != null && $this->getProgram($item['mestradoPrograma'])) {
                    $academics = new UserAcademics();
                    $academics->setUser($entity);
                    $academics->setLevel(1);
                    $academics->setStatus($item['mestradoStatus'] == 'concluido' ? 1 : 2);
                    // $academics->setArea($item['mestradoArea']);

                    // é outra
                    if (! $this->getInstitute($item['mestradoInstituicao'])) {
                        // $academics->setInstitution($this->getInstitute(99999));
                        // $academics->setOtherInstitution($item['mestradoInstituicao']);
                    } else {
                        // $academics->setInstitution($this->getInstitute($item['mestradoInstituicao']));
                    }

                    // é outro
                    if (! $this->getProgram($item['mestradoPrograma'])) {
                        // $academics->setProgram($this->getProgram(99999));
                        // $academics->setOtherProgram($item['mestradoPrograma']);
                    } else {
                        // $academics->setProgram($this->getProgram($item['mestradoPrograma']));
                    }

                    // $academics->setStartDate(new \DateTime($item['mestradoIngresso']));
                    // $academics->setEndDate(new \DateTime($item['mestradoConclusao']));

                    $entity->addAcademic($academics);
                }

                if ($item['doutoradoIngresso']) { // && $this->getProgram($item['doutoradoPrograma'])) {
                    $academics = new UserAcademics();
                    $academics->setUser($entity);
                    $academics->setLevel(3);
                    $academics->setStatus($item['doutoradoStatus'] == 'concluido' ? 1 : 2);
                    // $academics->setArea($item['doutoradoArea']);

                    // é outra
                    if (! $this->getInstitute($item['doutoradoInstituicao'])) {
                        // $academics->setInstitution($this->getInstitute(99999));
                        // $academics->setOtherInstitution($item['doutoradoInstituicao']);
                    } else {
                        // $academics->setInstitution($this->getInstitute($item['doutoradoInstituicao']));
                    }

                    // é outro
                    if (! $this->getProgram($item['doutoradoPrograma'])) {
                        // $academics->setProgram($this->getProgram(99999));
                        // $academics->setOtherProgram($item['doutoradoPrograma']);
                    } else {
                        // $academics->setProgram($this->getProgram($item['doutoradoPrograma']));
                    }

                    // $academics->setStartDate(new \DateTime($item['doutoradoIngresso']));
                    // $academics->setEndDate(new \DateTime($item['doutoradoConclusao']));

                    $entity->addAcademic($academics);
                }

                $em->persist($entity);
                $metadata = $em->getClassMetadata(User::class);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $metadata->setIdGenerator(new AssignedGenerator());
                $em->flush();

                $allPrograms = $em->getRepository(UserInstitutionsPrograms::class)->findBy([
                    'user' => $entity,
                ]);

                if (count($allPrograms) > 0) {
                    foreach ($allPrograms as $program) {
                        $em->remove($program);
                        $em->flush();
                    }
                }

                if (
                    $item['instituicao']
                    || $item['instituicao_secundaria']
                    || $item['programa']
                    || $item['programa_secundaria']
                ) {


                    //if (! $programs) {
                    $programs = new UserInstitutionsPrograms();

                    if ($item['instituicao_estado'] && $this->getState($item['instituicao_estado'])) {
                        $programs->setStateFirstId($this->getState($item['instituicao_estado']));
                    }

                    if ($item['instituicao_secundaria_estado'] && $this->getState($item['instituicao_secundaria_estado'])) {
                        $programs->setStateSecondId($this->getState($item['instituicao_secundaria_estado']));
                    }

                    // é outra
                    if (! $this->getInstitute($item['instituicao'])) {
                        $programs->setInstitutionFirstId($this->getInstitute(99999));
                        $programs->setOtherInstitutionFirst($item['instituicao']);
                    } else {
                        $programs->setInstitutionFirstId($this->getInstitute($item['instituicao']));
                    }

                    // é outra
                    if (! $this->getInstitute($item['instituicao_secundaria'])) {
                        $programs->setInstitutionSecondId($this->getInstitute(99999));
                        $programs->setOtherInstitutionSecond($item['instituicao_secundaria']);
                    } else {
                        $programs->setInstitutionSecondId($this->getInstitute($item['instituicao_secundaria']));
                    }

                    // é outro
                    if (! $this->getProgram($item['programa'])) {
                        $programs->setProgramFirstId($this->getProgram(99999));
                        $programs->setOtherProgramFirst($item['programa']);
                    } else {
                        $programs->setProgramFirstId($this->getProgram($item['programa']));
                    }

                    // é outro
                    if (! $this->getProgram($item['programa_secundaria'])) {
                        $programs->setProgramSecondId($this->getProgram(99999));
                        $programs->setOtherProgramSecond($item['programa_secundaria']);
                    } else {
                        $programs->setProgramSecondId($this->getProgram($item['programa_secundaria']));
                    }

                    $programs->setUser($entity);

                    $em->persist($programs);
                    $em->flush();
                    //}
                }
            }

            // usleep(50000);

            ?>
            <script type="text/javascript">
                setTimeout(function () {
                    window.location = '/pt_br/migration/users/<?= $page + 1 ?>';
                }, 500);
            </script>
            <?php
            exit;
        }

        die('users done.');
    }

    /**
     * @param $state
     *
     * @return object|null|State
     */
    private function getState($state)
    {
        $em = $this->getDoctrine()->getManager();

        $tmp = $em->getRepository(State::class)->findOneBy([
            'name' => $state,
        ]);

        if (! $tmp) {
            $tmp = $em->getRepository(State::class)->find($state);
        }

        return $tmp;
    }

    /**
     * @param $institute
     *
     * @return object|null|Institution
     */
    private function getInstitute($institute)
    {
        if (empty($institute)) {
            return null;
        }

        $em = $this->getDoctrine()->getManager();

        $tmp = $em->getRepository(Institution::class)->findOneBy([
            'name' => $institute,
        ]);

        if (! $tmp) {
            $tmp = $em->getRepository(Institution::class)->find($institute);
        }

        return $tmp;
    }

    /**
     * @param $program
     *
     * @return object|Program
     */
    private function getPrograms($program, $name = '')
    {
        if (empty($program)) {
            return null;
        }

        $em = $this->getDoctrine()->getManager();

        $tmp = $em->getRepository(Program::class)->findBy([
            'name' => $program,
        ]);

        return $tmp;
    }

    /**
     * @param $program
     *
     * @return object|Program
     */
    private function getProgram($program, $name = '')
    {
        if (empty($program)) {
            return null;
        }

        $em = $this->getDoctrine()->getManager();

        if ($name !== '') {
            return $em->getRepository(Program::class)->findOneBy([
                'id' => $program,
                'name' => $name,
            ]);
        }

        $tmp = $em->getRepository(Program::class)->findOneBy([
            'name' => $program,
        ]);

        if (! $tmp) {
            $tmp = $em->getRepository(Program::class)->find($program);
        }

        return $tmp;
    }

    /**
     * @param $city
     * @param $uf
     *
     * @return object|null|User
     */
    private function getCity($city, $uf)
    {
        $em = $this->getDoctrine()->getManager();

        $city = $em->getRepository(City::class)->findOneBy([
            'name' => $city,
        ]);

        if (! $city) {
            $city = $em->getRepository(City::class)->find(11524);
        }

        return $city;
    }

    /**
     * @param $type
     * @param $ext
     *
     * @return int
     */
    private function setUserType($type, $ext)
    {
        $types = ['pf' => 0, 'pj' => 1, 'ext' => 2];

        if ($ext != 28) {
            return $types['ext'];
        }

        if (isset($types[$type])) {
            return $types[$type];
        }

        return 0;
    }

    /**
     * @param $input
     *
     * @return int
     */
    private function clearInputInt($input)
    {
        return (int)preg_replace('/[^\d]/', '', $input);
    }

    /**
     * @Route("/programs/{page}", name="migration_programs", methods={"GET"})
     * @param int $page
     */
    public function programs(int $page = 0)
    {
        $pdo = $this->getPDO();
        //$pdo->query("use 2017_evento_enanpad");
        $pdo->query("use ANPADid");

        $em = $this->getDoctrine()->getManager();

        if (0 === $page) {
            $this->truncateTable(Program::class);

            $entity = new Program();
            $entity->setId(99999);

            $entity->setName('Outro');
            $entity->setPaid(0);
            $entity->setSortPosition(99999);

            $entity->setStatus(1);

            $em->persist($entity);
            $metadata = $em->getClassMetadata(Program::class);
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new AssignedGenerator());
            $em->flush();
        }

        $quantity = 50;
        $offset = $quantity * $page;

        $sth = $pdo->prepare("SELECT * FROM adm_programa
                            LIMIT $quantity
                            OFFSET $offset");

        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (count($results) > 0) {
            foreach ($results as $i => $item) {
                echo ($offset + $i) . ' ';

                $entity = $this->getProgram($item['id']);

                if (! $entity) {
                    $entity = new Program();
                    $entity->setId($item['id']);
                }

                $entity->setName($item['nome']);

                if (! isset($item['emdia'])) {
                    $item['emdia'] = 0;
                }
                $entity->setPaid($item['emdia']);

                $entity->setStatus($item['status']);
                $entity->setInstitution($this->getInstitute($item['adm_instituicao_id']));

                $entity->setSortPosition(1);

                $em->persist($entity);
                $metadata = $em->getClassMetadata(Program::class);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $metadata->setIdGenerator(new AssignedGenerator());
                $em->flush();
            }

            // usleep(50000);

            ?>
            <script type="text/javascript">
                setTimeout(function () {
                    window.location = '/pt_br/migration/programs/<?= $page + 1 ?>';
                }, 100);
            </script>
            <?php
            exit;
        }

        die('programs done.');
    }


    /**
     * @Route("/institutions/{page}", name="migration_institutions", methods={"GET"})
     * @param int $page
     */
    public function institutions(int $page = 0)
    {
        $pdo = $this->getPDO();
        //$pdo->query("use 2017_evento_enanpad");
        $pdo->query("use ANPADid");

        $em = $this->getDoctrine()->getManager();

        if (0 === $page) {
            $this->truncateTable(Institution::class);

            $entity = new Institution();
            $entity->setId(99999);

            $entity->setName('Outra');
            $entity->setPaid(0);
            $entity->setStatus(1);
            $entity->setSortPosition(99999);

            $entity->setType(true);
            $entity->setCity($this->getCity('-----', '-----'));
            $entity->setPhone(0);
            $entity->setCellphone(0);
            $entity->setEmail(0);
            $entity->setWebsite(0);
            $entity->setStreet(0);
            $entity->setZipcode(0);
            $entity->setNumber(0);
            $entity->setComplement(0);
            $entity->setNeighborhood(0);
            $entity->setCoordinator(0);

            $em->persist($entity);
            $metadata = $em->getClassMetadata(Institution::class);
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new AssignedGenerator());
            $em->flush();
        }

        $quantity = 50;
        $offset = $quantity * $page;

        $sth = $pdo->prepare("SELECT * FROM adm_instituicao
                            LIMIT $quantity
                            OFFSET $offset");

        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (count($results) > 0) {
            foreach ($results as $i => $item) {
                echo ($offset + $i) . ' ';

                $entity = $this->getInstitute($item['id']);

                if (! $entity) {
                    $entity = new Institution();
                    $entity->setId($item['id']);
                }

                $entity->setName($item['nome']);

                $entity->setInitials($item['sigla']);

                if (! isset($item['emdia'])) {
                    $item['emdia'] = 0;
                }
                $entity->setPaid($item['emdia']);
                $entity->setStatus($item['status']);
                $entity->setSortPosition(1);

                $entity->setType(true);
                $entity->setCity($this->getCity('-----', '-----'));
                $entity->setPhone(0);
                $entity->setCellphone(0);
                $entity->setEmail(0);
                $entity->setWebsite(0);
                $entity->setStreet(0);
                $entity->setZipcode(0);
                $entity->setNumber(0);
                $entity->setComplement(0);
                $entity->setNeighborhood(0);
                $entity->setCoordinator(0);

                $em->persist($entity);
                $metadata = $em->getClassMetadata(Institution::class);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $metadata->setIdGenerator(new AssignedGenerator());
                $em->flush();
            }

            // usleep(50000);

            ?>
            <script type="text/javascript">
                setTimeout(function () {
                    window.location = '/pt_br/migration/institutions/<?= $page + 1 ?>';
                }, 100);
            </script>
            <?php
            exit;
        }

        die('institutions done.');
    }


    /**
     * @Route("/user_associations/{page}", name="migration_user_associations", methods={"GET"})
     * @param int $page
     */
    public function user_associations(int $page = 0)
    {
        $pdo = $this->getPDO();
        //$pdo->query("use 2017_evento_enanpad");
        $pdo->query("use ANPADid");

        $em = $this->getDoctrine()->getManager();

        if (0 === $page) {
            // $this->truncateTable(UserxAssociationDivisions::class);
            $this->truncateTable(UserAssociation::class);
        }

        $quantity = 200;
        $offset = $quantity * $page;

        $sth = $pdo->prepare("SELECT
                                b.id AS _id,
                            CASE
                                    
                                    WHEN c.filiacao = 1 THEN
                                    3 
                                    WHEN c.filiacao = 2 THEN
                                    2 
                                    WHEN c.filiacao = 3 THEN
                                    4 
                                    WHEN c.filiacao = 6 THEN
                                    1 ELSE 0 
                                END AS _type,
                                c.UsuarioID AS _user_id,
                                c.instituicao AS _intitution_id,
                                c.programa AS _program_id,
                                d.sigla AS _div_sigla,
                                b.dataEmissao AS _created_at,
                                DATE_ADD( b.dataPagto, INTERVAL 1 YEAR ) AS _expired_at,
                                b.dataPagto AS _last_pay,
                            CASE
                                    
                                    WHEN b.tipo = 'associado' THEN
                                    1 
                                    WHEN b.tipo = 'compras' THEN
                                    2 ELSE 0 
                                END AS _level,
                            CASE
                                    
                                    WHEN b.pago = 'sim' THEN
                                    1 ELSE 0 
                                END AS _status_pay 
                            FROM
                                boletos_kit b
                                INNER JOIN Cadastro2 c ON c.UsuarioID = b.id_origem
                                INNER JOIN adm_divisoes_academicas d ON d.id = c.divisao_principal
                            WHERE
                                b.dataPagto IS NOT NULL 
                                AND b.valido = 'sim' 
                                AND b.tipo IN ( 'associado', 'download' )
                            ORDER BY
                                b.dataPagto DESC
                            LIMIT $quantity
                            OFFSET $offset");

        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (count($results) > 0) {
            foreach ($results as $i => $item) {
                echo ($offset + $i) . ' ';

                $entity = $em->getRepository(UserAssociation::class)->find($item['_id']);
                if (! $entity) {
                    $entity = new UserAssociation();
                    $entity->setId($item['_id']);
                }

                $entity->setType($item['_type']);
                $entity->setCreatedAt(new \DateTime($item['_created_at']));

                /** @var Division $division */
                $division = $em->getRepository(Division::class)->findOneBy([
                    'initials' => $item['_div_sigla'],
                ]);

                $entity->setDivision($division);


                $entity->setLevel($item['_level']);

                $user = $this->checkUser($item['_user_id']);
                $entity->setUser($user);

                $entity->setStatusPay($item['_status_pay']);

                if ($item['_status_pay']) {
                    $entity->setUpdatedAt(new \DateTime($item['_last_pay']));
                    $entity->setExpiredAt(new \DateTime($item['_expired_at']));
                    $entity->setLastPay(new \DateTime($item['_last_pay']));
                } else {
                    $entity->setUpdatedAt(new \DateTime($item['_created_at']));
                }

                // é outra
                if (! $this->getInstitute($item['_intitution_id'])) {
                    $entity->setInstitution($this->getInstitute(99999));
                    $entity->setOtherInstitution($item['_intitution_id']);
                } else {
                    $entity->setInstitution($this->getInstitute($item['_intitution_id']));
                }

                // é outro
                if (! $this->getProgram($item['_program_id'])) {
                    $entity->setProgram($this->getProgram(99999));
                    $entity->setOtherProgram($item['_program_id']);
                } else {
                    $entity->setProgram($this->getProgram($item['_program_id']));
                }


                $sth2 = $pdo->prepare("SELECT
                                        c.usuarioId AS _user_id,
                                        d.sigla AS _div_sigla
                                    FROM
                                        Cadastro2_div_adicionais c
                                        INNER JOIN adm_divisoes_academicas d ON d.id = c.divisao
                                    WHERE
                                        c.usuarioId={$item['_user_id']}");

                $sth2->execute();
                $results2 = $sth2->fetchAll(\PDO::FETCH_ASSOC);

                if (count($results2) > 0) {
                    foreach ($results2 as $i2 => $item2) {

                        /** @var Division $division2 */
                        $division2 = $em->getRepository(Division::class)->findOneBy([
                            'initials' => $item2['_div_sigla'],
                        ]);

                        $entity->addAditional($division2);
                    }
                }


                $em->persist($entity);
                $metadata = $em->getClassMetadata(UserAssociation::class);
                $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $metadata->setIdGenerator(new AssignedGenerator());
                $em->flush();
            }

            // usleep(50000);

            ?>
            <script type="text/javascript">
                setTimeout(function () {
                    window.location = '/pt_br/migration/user_associations/<?= $page + 1 ?>';
                }, 100);
            </script>
            <?php
            exit;
        }

        die('user_associations done.');
    }


}
