<?php

namespace App\Bundle\Base\Controller\Site;


use App\Bundle\Base\Entity\UserConsents;
use App\Bundle\Base\Repository\EditionRepository;
use App\Bundle\Base\Repository\EventRepository;
use App\Bundle\Base\Repository\UserThemesRepository;
use App\Bundle\Base\Services\Edition as EditionService;
use App\Bundle\Base\Services\User as UserService;
use App\Bundle\Base\Services\UserConsents as UserConsentsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;


/**
 * @Route("/")
 * Class HomeController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class HomeController extends AbstractController
{
    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var UserConsentsService
     */
    private $userConsentsService;

    /**
     * @var UserThemesRepository
     */
    private $userThemesRepository;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var EditionService
     */
    private $editionService;

    /**
     * HomeController constructor.
     *
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     * @param EventRepository $eventRepository
     * @param UserConsentsService $userConsentsService
     * @param UserThemesRepository $userThemesRepository
     * @param UserService $userService
     * @param EditionService $editionService
     */
    public function __construct(
        Breadcrumbs $breadcrumbs,
        UrlGeneratorInterface $urlGenerator,
        EventRepository $eventRepository,
        UserConsentsService $userConsentsService,
        UserThemesRepository $userThemesRepository,
        UserService $userService,
        EditionService $editionService
    )
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $this->eventRepository = $eventRepository;
        $this->userConsentsService = $userConsentsService;
        $this->userThemesRepository = $userThemesRepository;
        $this->userService = $userService;
        $this->editionService = $editionService;
    }

    /**
     * @Route("/sections", name="sections", methods={"GET"})
     */
    public function sections()
    {
        return $this->render('@Base/sections/index.html.twig');
    }

    /**
     * @Route("/webapp", name="webapp", methods={"GET"})
     */
    public function webapp()
    {
        return $this->render('@Base/webapp/index.html.twig');
    }

    /**
     * @Route("/ensalamento_classroom_registration", name="ensalamento_classroom_registration", methods={"GET"})
     */
    public function ensalamento_classroom_registration()
    {
        return $this->render('@Base/ensalamento_classroom_registration/index.html.twig');
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toIndex()
    {
        return $this->redirectToRoute('index_home');
    }

    /**
     * @Route("/index", name="index_home", methods={"GET"})
     *
     * @param EditionRepository $editionRepository
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function index(EditionRepository $editionRepository)
    {

        if ($user = $this->getUser()) {

            if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_ADMIN_OPERATIONAL')) {
                return $this->redirectToRoute('dashboard_admin_index');
            }

            return $this->redirectToRoute('dashboard_user_index');
        }

        $dashboard = [
            'next' => $editionRepository->findNext(),
            'previous' => $editionRepository->findPrevious(),
        ];

        return $this->render('@Base/home/index.html.twig', ['dashboard' => $dashboard]);
    }

    /**
     * @Route("/index_event", name="index_event", methods={"GET"})
     *
     * @param EditionRepository $editionRepository
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function index_event(EditionRepository $editionRepository)
    {
        $dashboard = [
            'next' => $editionRepository->findNext(),
            'previous' => $editionRepository->findPrevious(),
        ];

        return $this->render('@Base/home/index.html.twig', ['dashboard' => $dashboard]);
    }

    /**
     * @Route("/save_success", name="save_success")
     */
    public function save_success(Request $request): Response
    {
        $step = (int)$request->get('step', 1);
        $response = new Response(json_encode(['step' => ++$step]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/editions/{nome}", name="editions")
     */
    public function editions($nome): Response
    {
        $editions = [];
        for ($i = 0; $i < 30; $i++) {
            $editions[] = [
                'id' => rand(1, 10000),
                'ordem' => $i + 1,
                'nome' => $nome . ' - 20' . rand(10, 19),
                'alteracao' => date('d/m/Y H:i'),
                'local' => 'Local',
                'status' => rand(0, 1),
                'hom' => rand(0, 1) == 0 ? false : true,
                'desc' => 'Descrição da Edição',
            ];
        }
        $response = new Response(json_encode($editions));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/subsections", name="subsections")
     */
    public function subsections(): Response
    {
        $subsections = [];
        for ($i = 0; $i < 30; $i++) {
            $subsections[] = [
                'id' => rand(1, 10000),
                'ordem' => $i + 1,
                'nome' => 'Teste',
                'chamada' => 'Chamada',
                'alteracao' => date('d/m/Y H:i'),
                'local' => 'Local',
                'status' => rand(0, 1),
                'hom' => rand(0, 1) == 0 ? false : true,
                'destaque' => rand(0, 1) == 0 ? false : true,
                'tipo' => rand(0, 1) == 0 ? 'Simples (apenas texto)' : 'Programação Sintética',
                'conteudo' => 'Conteúdo da Subseção',
            ];
        }
        $response = new Response(json_encode($subsections));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/speakers", name="speakers")
     */
    public function speakers(): Response
    {
        $speakers = [];
        for ($i = 0; $i < 30; $i++) {
            $speakers[] = [
                'id' => rand(1, 10000),
                'ordem' => $i + 1,
                'nacional' => rand(0, 1) == 0 ? false : true,
                'nome' => 'Nome palestrante',
                'hom' => rand(0, 1) == 0 ? false : true,
                'status' => rand(0, 1),
                'link' => 'http://google.com/',
                'conteudo' => 'Biografia do Palestrante',
            ];
        }
        $response = new Response(json_encode($speakers));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/files", name="files")
     */
    public function files(): Response
    {
        $files = [];
        for ($i = 0; $i < 30; $i++) {
            $files[] = [
                'id' => rand(1, 10000),
                'texto' => 'Clique aqui para acessar a Chamada de Painéis',
                'link' => 'http://google.com/',
            ];
        }
        $response = new Response(json_encode($files));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/delete_success/{id}", name="delete_success")
     */
    public function delete_success(Request $request): Response
    {
        $response = new Response(json_encode([]));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/theme/evaluation", name="theme_evaluation", methods={"GET"})
     */
    public function theme_evaluation()
    {
        $theme_status = [
            ['name' => 'AGUARDANDO', 'color' => 'light'],
            ['name' => 'APROVADO', 'color' => 'success'],
            ['name' => 'REPROVADO', 'color' => 'danger'],
        ];
        $total = 0;
        for ($i = 0; $i < 11; $i++) {
            $qtd = rand(10, 19);
            $total += $qtd;
            $dashboard['items'][] = [
                'title' => chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)),
                'qtd' => $qtd,
            ];
        }
        $dashboard['total'] = $total;
        $evaluation = [
            'dashboard' => $dashboard,
        ];
        return $this->render('@Base/theme/user/evaluation/index.html.twig', compact('evaluation', 'theme_status'));
    }

    /**
     * @Route("/theme", name="theme", methods={"GET"})
     */
    public function theme(Request $request): Response
    {
        return $this->redirectToRoute('theme_list');
    }

    /**
     * @Route("/coordinators", name="coordinators", methods={"GET"})
     */
    public function coordinators(Request $request): Response
    {
        for ($i = 1; $i <= 30; $i++) {
            $coordinators[] = [
                'id' => $i,
                'user' => chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . rand(1, 9),
                'name' => 'Alexandra Albuquerque Correia Sousa',
                'division' => $request->get('coordinator-list-division') == null ? chr(rand(65, 90)) . chr(rand(65,
                        90)) . chr(rand(65, 90)) : $request->get('coordinator-list-division'),
                'first' => '16/07/2020',
                'email' => 'email@email.com',
            ];
        }
        $response = new Response(json_encode($coordinators));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/researchers", name="researchers", methods={"GET"})
     */
    public function researchers(Request $request): Response
    {
        for ($i = 1; $i <= 30; $i++) {
            $researchers[] = [
                'id' => $i,
                'cod' => chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . rand(1, 9),
                'title' => 'Titulo',
                'name' => 'Alexandra Albuquerque Correia Sousa',
                'institution' => [
                    'primary' => [
                        'name' => 'UFPR - Universidade Federal do Paraná',
                        'program' => 'Prog de MestrProf em Admin: Gestão Contemporânea das Organizações',
                        'filial' => rand(0, 1) == 0 ? false : true,
                    ],
                    'secondary' => [
                        'name' => 'UTFPR - Universidade Técnologica Federal do Paraná',
                        'program' => 'Progr de Mestr Prof em Admin',
                        'filial' => rand(0, 1) == 0 ? false : true,
                    ],
                ],
            ];
        }
        $response = new Response(json_encode($researchers));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function system_evaluation_submissions()
    {
        $pageTitle = 'EnADI 2020';
        $menuBreadcumb = $this->evaluationSubmenu('SYS_EV_MENU_SUBMISSIONS');
        $status = [
            ['id' => 1, 'name' => 'REPROVADO FORMATO', 'color' => 'danger'],
            ['id' => 2, 'name' => 'SELECIONADO PARA APRESENTAÇÃO E PUBLICAÇÃO', 'color' => 'success'],
            ['id' => 3, 'name' => 'REMOVIDO À PEDIDO DO AUTOR', 'color' => 'light'],
            ['id' => 4, 'name' => 'TRABALHO NÃO SELECIONADO PARA', 'color' => 'warning'],
        ];
        for ($i = 1; $i <= 10; $i++) {
            $cod = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90));
            for ($t = 1; $t <= 30; $t++) {
                $themes[$cod][] = [
                    'id' => $t,
                    'name' => "Tema $t - Lorem ipsum dolor sit amet, consectetur adipiscing elit",
                ];
            }
        }
        for ($i = 1; $i <= 3; $i++) {
            $authors[] = [
                'id' => $i,
                'ordem' => $i,
                'country' => 'Brasil',
                'cpf' => '000.000.000-0',
                'name' => 'Cassios Maia Carvalho',
                'email' => 'E-cassios.carvalho@uol.com.br',
                'celular' => '(68) 9922-35846',
                'phone' => '(68) 3214-1413',
                'institution' => 'ESMPU - Escola Superior do MPU',
                'program' => 'Pós-Graduação emGovernança da Tecnologia da Informação',
            ];
        }
        $submissionLogs = [
            [
                'created' => '01/07/2020',
                'status' => 'Reprovado Formato Cesar Alexandre de Souza - Ação: Aceitou a Reprovação de Formato',
            ],
            [
                'created' => '02/07/2020',
                'status' => 'Reprovado Formato Avaliador: Rafael Alfonso Brinkhues - Motivo Artigo com identificação dos autores, logo abaixo do título',
            ],
            ['created' => '03/07/2020', 'status' => 'Avaliação Efetuada/Alterada - Rovian Dill Zuquetto'],
            [
                'created' => '04/07/2020',
                'status' => 'Designado Avaliador - Rovian Dill Zuquetto - Cristinae Dreves',
            ],
        ];

        for ($i = 0; $i < 10; $i++) {
            $stats[] = [
                'id' => $i,
                'created' => "0$i/07/2020",
                'ip' => "$i$i$i.00.000.000",
            ];
        }

        for ($i = 1; $i < 11; $i++) {
            $articles[] = [
                'id' => $i,
                'name' => "EnADI $i",
                'title' => 'Inovação Startup: Transformando ideias em negócios desucesso',
                'status' => rand(0, 3),
                'desiginatios' => rand(0, 3),
                'evaluations' => rand(0, 3),
                'themes' => rand(1, 8),
                'enquadramento' => 'Artigo Teórico-Empírico',
                'authors' => $authors,
                'confirmDate' => '22/07/2020 00:00',
                'confirmIp' => "$i$i$i.00.000.000",
                'submissionLogs' => $submissionLogs,
                'stats' => $stats,
            ];
        }
        return $this->render('@Base/system_evaluation/submissions/index.html.twig',
            compact('articles', 'status', 'themes', 'pageTitle', 'menuBreadcumb'));
    }

    public function evaluationSubmenu($label)
    {
        return [
            [
                'label' => 'SYS_EV_MENU_DASHBOARD',
                'href' => '/system_evaluation/dashboard',
                'active' => $label == 'SYS_EV_MENU_DASHBOARD',
            ],
            [
                'label' => 'SYS_EV_MENU_SUBMISSIONS',
                'href' => '/system_evaluation/submissions',
                'active' => $label == 'SYS_EV_MENU_SUBMISSIONS',
            ],
            [
                'label' => 'SYS_EV_MENU_EVALUATORS',
                'href' => '/system_evaluation/evaluators',
                'active' => $label == 'SYS_EV_MENU_EVALUATORS',
            ],
            [
                'label' => 'SYS_EV_MENU_REPORTS',
                'href' => [
                    ['label' => 'SYS_EV_MENU_STATISTICS', 'href' => '/system_evaluation/reports/statistics'],
                    ['label' => 'SYS_EV_MENU_SYS_STATISTICS', 'href' => '/system_evaluation/reports/sys_statistics'],
                    ['label' => 'SYS_EV_MENU_ACTION', 'href' => '/system_evaluation/reports/pending_action'],
                    ['label' => 'SYS_EV_MENU_REFUSED', 'href' => '/system_evaluation/reports/refused_format'],
                    ['label' => 'SYS_EV_MENU_REFUSED_PRE', 'href' => '/system_evaluation/reports/refused_pre'],
                    ['label' => 'SYS_EV_MENU_WORKS', 'href' => '/system_evaluation/reports/coord_division'],
                    ['label' => 'SYS_EV_MENU_ARTICLES', 'href' => '/system_evaluation/reports/articles_theme'],
                    ['label' => 'SYS_EV_MENU_DISCREPENCIES', 'href' => '/system_evaluation/reports/discrepancies'],
                    ['label' => 'SYS_EV_MENU_INPROGRESS', 'href' => '/system_evaluation/reports/inprogress'],
                    [
                        'label' => 'SYS_EV_MENU_INPROGRESS_EVALUATORS',
                        'href' => '/system_evaluation/reports/inprogress_evaluators',
                    ],
                    [
                        'label' => 'SYS_EV_MENU_INPROGRESS_EVALUATIONS',
                        'href' => '/system_evaluation/reports/inprogress_evaluations',
                    ],
                    ['label' => 'SYS_EV_MENU_APPROVED', 'href' => '/system_evaluation/reports/approved'],
                    ['label' => 'SYS_EV_MENU_APPROVED_ARTICLES', 'href' => '/system_evaluation/reports/approved_articles'],
                    ['label' => 'SYS_EV_MENU_AUTHOR', 'href' => '/system_evaluation/reports/author'],
                    ['label' => 'SYS_EV_MENU_INSTITUTIONS', 'href' => '/system_evaluation/reports/institutions'],
                    ['label' => 'SYS_EV_MENU_ARTICLES_QTD', 'href' => '/system_evaluation/reports/articles_qtd'],
                    ['label' => 'SYS_EV_MENU_TABLE', 'href' => '/system_evaluation/reports/table'],
                    ['label' => 'SYS_EV_MENU_CHART', 'href' => '/system_evaluation/reports/chart/total'],
                    ['label' => 'SYS_EV_MENU_CHART_LAST', 'href' => '/system_evaluation/reports/chart/last'],
                ],
                'active' => $label == 'SYS_EV_MENU_REPORTS',
            ],
            [
                'label' => 'SYS_EV_MENU_COORDINATORS',
                'href' => '/system_evaluation/coordinators',
                'active' => $label == 'SYS_EV_MENU_COORDINATORS',
            ],
            [
                'label' => 'SYS_EV_MENU_CONFIG',
                'href' => '/system_evaluation/configurations',
                'active' => $label == 'SYS_EV_MENU_CONFIG',
            ],
        ];
    }

    public function system_evaluation_coordinators()
    {
        $pageTitle = 'EnADI 2020';
        $menuBreadcumb = $this->evaluationSubmenu('SYS_EV_MENU_COORDINATORS');
        $types = [
            1 => 'Líder de Tema',
            2 => 'Coordenador de Divisão',
            3 => 'Comitê',
        ];
        for ($i = 1; $i <= 30; $i++) {
            $themes[] = [
                'id' => $i,
                'name' => "Tema $i",
            ];
        }
        for ($i = 1; $i <= 10; $i++) {
            $rand = rand(0, 1);
            $coordinators[] = [
                'id' => $i,
                'name' => $rand == 0 ? 'Abdinardo Moreira Barreto de Oliveira' : 'Adalberto Ramos Cassia',
                'user' => 'LID-06511882888',
                'type' => rand(1, 3),
                'access' => '01/07/2020 00:00',
                'themes' => [rand(1, 10), rand(11, 21), rand(21, 30)],
            ];
        }
        return $this->render('@Base/system_evaluation/coordinators/index.html.twig',
            compact('coordinators', 'types', 'themes', 'pageTitle', 'menuBreadcumb'));
    }

    public function reportTableData($page)
    {
        $tableData = [];
        switch ($page) {
            case 'statistics':
                for ($i = 1; $i < 10; $i++) {
                    $tableData[] = [
                        'division' => 'EnADI',
                        'themes' => ['Tema 1', 'Tema 2'],
                        'submitted' => rand(8, 18),
                        'rp_pre' => rand(0, 1),
                        'available' => rand(1, 16),
                        'discrepancies' => rand(1, 16),
                    ];
                }
                break;
            case 'sys_statistics':
                for ($i = 1; $i < 10; $i++) {
                    $tableData[] = [
                        'division' => 'EnADI',
                        'themes' => ['Tema 1', 'Tema 2'],
                        'submitted_orig' => rand(8, 18),
                        'submitted_final' => rand(8, 18),
                        'available' => rand(1, 16),
                        'rp' => rand(0, 1),
                        'evaluators' => rand(5, 16),
                        'evaluator_by1' => rand(5, 16),
                        'evaluator_by2' => rand(5, 16),
                        'evaluator_by3' => rand(5, 16),
                        'discrepancies' => rand(1, 16),
                        'invited' => rand(1, 16),
                        'selected' => rand(1, 16),
                    ];
                }
                break;
            case 'pending_action':
                $fit = ['Artigo Teórico-Empírico', 'Ensaio Teórico', 'Casos para Ensino'];
                $situation = ['RECUSADO POR FORMATO', 'RECUSADO NA PRÉ SELEÇÃO'];
                for ($i = 1; $i < 10; $i++) {
                    $tableData[] = [
                        'id' => 'EnADI' . rand(1, 90),
                        'theme' => 'Tema ' . rand(1, 9),
                        'title' => 'Inovação Startup: Transformando ideias em negócios desucesso',
                        'fit' => $fit[rand(0, 2)],
                        'evaluator' => 'Valter de Assis Moreno Jr.',
                        'situation' => $situation[rand(0, 1)],
                        'reason' => 'Embora  o  artigo  esteja  bem  escrito,  há  várias  deficiências  importantes  que  impedem  a  sua  aceitação.  1.  O  trabalho  não  dialoga com  a  literatura.  Não  há  uma  discussão  sobre  como  ele  se  insere  na  literatura  e  qual  seria  a  sua  contribuição  para  o  avanço  do conhecimento  na  área  de  Sistemas  de  Informação.  2.  O  tema  tratado  já  foi  amplamente  estudado,  não  havendo,  mesmo  nos resultados da pesquisa empírica, novidades em relação ao que já se sabe sobre a implantação de sistemas. Deve-se ressaltar que o fato  de  se  descrever  a  implantação  de  um  sistema  específico  não  constitui  por  si  só  novidade  relevante.  O  diálogo  supracitado deve  revelar  lacunas  no  conhecimento  e  potenciais  desenvolvimentos  teóricos,  e  assim  a  forma  como  um  trabalho  que  pretenda ser realmente inédito pode ajudar a responder uma questão de pesquisa relevante. 3. Não há uma revisão da literatura pertinente. 4. O método adotado (survey) pouco contribui para a identificação de novos temas, já que tem por base um questionário fechado baseado, segundo os autores, numa revisão da literatura por eles conduzida. Um método qualitativo de pesquisa em profundidade e  longitudinal  poderia  revelar  aspectos  interessantes  e  importantes  relativos  à  percepção  subjetiva  dos  atores  organizacionais envolvidos,  e  as  dinâmicas  subjetivas  associadas  à  implantação  e  adoção  do  sistema.  5.  Pelas  razões  acima,  os  resultados apresentados  são  pobres.  6.  Não  há  uma  discussão  propriamente  dita  dos  resultados,  os  contrastando  e  comparando  com  o conhecimento acumulado no campo. Por fim, ressalto também que o artigo não cumpre as normas de formatação da ANPAD. Isso, por si só, já justificaria sua rejeição.',
                    ];
                }
                break;
            case 'refused_format':
                $fit = ['Artigo Teórico-Empírico', 'Ensaio Teórico', 'Casos para Ensino'];
                $removal = ['accept', 'denied'];
                $user = ['Susana Carla Farias Pereira', 'Valter de Assis Moreno Jr.'];
                for ($i = 1; $i < 10; $i++) {
                    $tableData[] = [
                        'id' => 'EnADI' . rand(1, 90),
                        'theme' => 'Tema ' . rand(1, 9),
                        'title' => 'Inovação Startup: Transformando ideias em negócios desucesso',
                        'fit' => $fit[rand(0, 2)],
                        'evaluator' => 'Valter de Assis Moreno Jr.',
                        'removal' => [
                            'action' => $removal[rand(0, 1)],
                            'user' => $user[rand(0, 1)],
                        ],
                        'situation' => 'RECUSADO POR FORMATO',
                        'reason' => 'Embora  o  artigo  esteja  bem  escrito,  há  várias  deficiências  importantes  que  impedem  a  sua  aceitação.  1.  O  trabalho  não  dialoga com  a  literatura.  Não  há  uma  discussão  sobre  como  ele  se  insere  na  literatura  e  qual  seria  a  sua  contribuição  para  o  avanço  do conhecimento  na  área  de  Sistemas  de  Informação.  2.  O  tema  tratado  já  foi  amplamente  estudado,  não  havendo,  mesmo  nos resultados da pesquisa empírica, novidades em relação ao que já se sabe sobre a implantação de sistemas. Deve-se ressaltar que o fato  de  se  descrever  a  implantação  de  um  sistema  específico  não  constitui  por  si  só  novidade  relevante.  O  diálogo  supracitado deve  revelar  lacunas  no  conhecimento  e  potenciais  desenvolvimentos  teóricos,  e  assim  a  forma  como  um  trabalho  que  pretenda ser realmente inédito pode ajudar a responder uma questão de pesquisa relevante. 3. Não há uma revisão da literatura pertinente. 4. O método adotado (survey) pouco contribui para a identificação de novos temas, já que tem por base um questionário fechado baseado, segundo os autores, numa revisão da literatura por eles conduzida. Um método qualitativo de pesquisa em profundidade e  longitudinal  poderia  revelar  aspectos  interessantes  e  importantes  relativos  à  percepção  subjetiva  dos  atores  organizacionais envolvidos,  e  as  dinâmicas  subjetivas  associadas  à  implantação  e  adoção  do  sistema.  5.  Pelas  razões  acima,  os  resultados apresentados  são  pobres.  6.  Não  há  uma  discussão  propriamente  dita  dos  resultados,  os  contrastando  e  comparando  com  o conhecimento acumulado no campo. Por fim, ressalto também que o artigo não cumpre as normas de formatação da ANPAD. Isso, por si só, já justificaria sua rejeição.',
                    ];
                }
                break;
            case 'refused_pre':
                $fit = ['Artigo Teórico-Empírico', 'Ensaio Teórico', 'Casos para Ensino'];
                $evaluator = ['Susana Carla Farias Pereira', 'Valter de Assis Moreno Jr.'];
                for ($i = 1; $i < 10; $i++) {
                    $tableData[] = [
                        'id' => 'EnADI' . rand(1, 90),
                        'theme' => 'Tema ' . rand(1, 9),
                        'title' => 'Inovação Startup: Transformando ideias em negócios desucesso',
                        'fit' => $fit[rand(0, 2)],
                        'evaluator' => $evaluator[rand(0, 1)],
                        'situation' => 'RECUSADO NA PRÉ SELEÇÃO',
                        'reason' => 'Embora  o  artigo  esteja  bem  escrito,  há  várias  deficiências  importantes  que  impedem  a  sua  aceitação.  1.  O  trabalho  não  dialoga com  a  literatura.  Não  há  uma  discussão  sobre  como  ele  se  insere  na  literatura  e  qual  seria  a  sua  contribuição  para  o  avanço  do conhecimento  na  área  de  Sistemas  de  Informação.  2.  O  tema  tratado  já  foi  amplamente  estudado,  não  havendo,  mesmo  nos resultados da pesquisa empírica, novidades em relação ao que já se sabe sobre a implantação de sistemas. Deve-se ressaltar que o fato  de  se  descrever  a  implantação  de  um  sistema  específico  não  constitui  por  si  só  novidade  relevante.  O  diálogo  supracitado deve  revelar  lacunas  no  conhecimento  e  potenciais  desenvolvimentos  teóricos,  e  assim  a  forma  como  um  trabalho  que  pretenda ser realmente inédito pode ajudar a responder uma questão de pesquisa relevante. 3. Não há uma revisão da literatura pertinente. 4. O método adotado (survey) pouco contribui para a identificação de novos temas, já que tem por base um questionário fechado baseado, segundo os autores, numa revisão da literatura por eles conduzida. Um método qualitativo de pesquisa em profundidade e  longitudinal  poderia  revelar  aspectos  interessantes  e  importantes  relativos  à  percepção  subjetiva  dos  atores  organizacionais envolvidos,  e  as  dinâmicas  subjetivas  associadas  à  implantação  e  adoção  do  sistema.  5.  Pelas  razões  acima,  os  resultados apresentados  são  pobres.  6.  Não  há  uma  discussão  propriamente  dita  dos  resultados,  os  contrastando  e  comparando  com  o conhecimento acumulado no campo. Por fim, ressalto também que o artigo não cumpre as normas de formatação da ANPAD. Isso, por si só, já justificaria sua rejeição.',
                    ];
                }
                break;
            case 'coord_division':
                $fit = ['Artigo Teórico-Empírico', 'Ensaio Teórico', 'Casos para Ensino'];
                for ($i = 1; $i < 10; $i++) {
                    $tableData[] = [
                        'id' => 'EnADI' . rand(1, 90),
                        'theme' => 'Tema ' . rand(1, 9),
                        'title' => 'Inovação Startup: Transformando ideias em negócios desucesso',
                        'fit' => $fit[rand(0, 2)],
                        'date' => '05/08/2020 08:00:00',
                        'status' => rand(0, 3),
                    ];
                }
                break;
            case 'articles_theme':
                $fit = ['Artigo Teórico-Empírico', 'Ensaio Teórico', 'Casos para Ensino'];
                for ($i = 1; $i < 10; $i++) {
                    $tableData[] = [
                        'id' => 'EnADI' . rand(1, 90),
                        'title' => 'Inovação Startup: Transformando ideias em negócios desucesso',
                        'fit' => $fit[rand(0, 2)],
                        'theme_original' => 'Tema ' . rand(1, 9),
                        'theme_current' => 'Tema ' . rand(1, 9),
                        'date' => '05/08/2020 08:00:00',
                    ];
                }
                break;
            case 'discrepancies':
                $fit = ['Artigo Teórico-Empírico', 'Ensaio Teórico', 'Casos para Ensino'];
                for ($i = 1; $i < 10; $i++) {
                    $tableData[] = [
                        'id' => 'EnADI' . rand(1, 90),
                        'title' => 'Inovação Startup: Transformando ideias em negócios desucesso',
                        'designations' => rand(1, 3),
                        'theme' => 'Tema ' . rand(1, 9),
                        'fit' => $fit[rand(0, 2)],
                        'date' => '05/08/2020 08:00:00',
                        'status' => 'AGUARDANDO DESIGNAÇÃO',
                    ];
                }
                break;
            case 'inprogress':
                $fit = ['Artigo Teórico-Empírico', 'Ensaio Teórico', 'Casos para Ensino'];
                for ($i = 1; $i < 10; $i++) {
                    $tableData[] = [
                        'id' => 'EnADI' . rand(1, 90),
                        'title' => 'Inovação Startup: Transformando ideias em negócios desucesso',
                        'fit' => $fit[rand(0, 2)],
                        'theme' => 'Tema ' . rand(1, 9),
                        'designations' => rand(1, 3),
                        'evaluations' => rand(1, 3),
                        'status' => rand(0, 3),
                    ];
                }
                break;
            case 'inprogress_evaluators':
                $names = ['Valter de Assis Moreno Jr.', 'João Felipe Ferreira', 'Antônio Carlos de Abreu'];
                for ($i = 1; $i < 10; $i++) {
                    $designations = rand(1, 16);
                    $tableData[] = [
                        'name' => $names[rand(0, 2)],
                        'email' => 'adelciomachado@gmail.com',
                        'themes' => ['EnADI30', 'EnADI34', 'EnADI68'],
                        'designations' => $designations,
                        'evaluations' => rand(0, $designations),
                    ];
                }
                break;
            case 'inprogress_evaluations':
                $fit = ['Artigo Teórico-Empírico', 'Ensaio Teórico', 'Casos para Ensino'];
                for ($i = 1; $i < 10; $i++) {
                    $tableData[] = [
                        'id' => 'EnADI' . rand(1, 90),
                        'title' => 'Inovação Startup: Transformando ideias em negócios desucesso',
                        'fit' => $fit[rand(0, 2)],
                        'theme' => 'Tema ' . rand(1, 9),
                        'date' => '05/08/2020 08:00:00',
                        'evaluator_id' => rand(100, 400),
                        'char_count' => rand(500, 8000),
                    ];
                }
                break;
            case 'approved':
                $fit = ['Artigo Teórico-Empírico', 'Ensaio Teórico', 'Casos para Ensino'];
                $work = ['Outro', 'Dissertação Concluída', 'Projeto de Pesquisa'];
                $keywords = [
                    [
                        'Negócios Internacionais',
                        'Internacionalização de Empresas',
                        'Empresas Multinacionais',
                    ],
                    ['Trabalho em Família', 'Maternidade / Paternidade', 'Gênero'],
                    ['Organizações Públicas', 'Funções Gerenciais'],
                ];
                for ($i = 1; $i < 10; $i++) {
                    $tableData[] = [
                        'id' => 'EnADI' . rand(1, 90),
                        'title' => 'Inovação Startup: Transformando ideias em negócios desucesso',
                        'fit' => $fit[rand(0, 2)],
                        'theme' => 'Tema ' . rand(1, 9),
                        'work' => $work[rand(0, 2)],
                        'keywords' => $keywords[rand(0, 2)],
                        'mode' => chr(rand(65, 90)) . chr(rand(65, 90)),
                    ];
                }
                break;
            case 'author':
                $fit = ['Artigo Teórico-Empírico', 'Ensaio Teórico', 'Casos para Ensino'];
                $work = ['Outro', 'Dissertação Concluída', 'Projeto de Pesquisa'];
                $keywords = [
                    [
                        'Negócios Internacionais',
                        'Internacionalização de Empresas',
                        'Empresas Multinacionais',
                    ],
                    ['Trabalho em Família', 'Maternidade / Paternidade', 'Gênero'],
                    ['Organizações Públicas', 'Funções Gerenciais'],
                ];
                for ($i = 1; $i < 10; $i++) {
                    $tableData[] = [
                        'id' => 'EnADI' . rand(1, 90),
                        'title' => 'Inovação Startup: Transformando ideias em negócios desucesso',
                        'fit' => $fit[rand(0, 2)],
                        'theme' => 'Tema ' . rand(1, 9),
                        'work' => $work[rand(0, 2)],
                        'keywords' => $keywords[rand(0, 2)],
                        'order' => $i,
                        'type' => chr(rand(65, 90)) . chr(rand(65, 90)),
                        'name' => 'Mário Henrique Ogasavara',
                        'doc' => '1433626900',
                        'email' => 'mario.ogasavara@espm.br',
                        'institution_primary' => 'ESPM - Esc Sup de Prop e MKT de São Paulo/Ass Esc Sup de Prop e MKT',
                        'state_primary' => 'SP',
                        'program_primary' => 'Prog de Mestr e Dout em Admin em Gestão Internacional/Dout e Mestr em Admin - PMDGI',
                        'institution_secondary' => '',
                        'state_secondary' => '',
                        'program_secondary' => '',
                        'graduate' => 'Concluído',
                        'master' => 'Concluído',
                        'doctorate' => 'Em Andamento',
                        'thank' => 'O  primeiro  autor  agradece  ao  CNPq  pelo  apoio  ao  projeto  de  pesquisa  registrado  sob  o  número  409280/2018-6.  A  segunda  autora  agradece  à CAPES pelo apoio no Programa de Bolsas Demanda Social.',
                    ];
                }
                break;
            case 'institutions':
                $institutions = [
                    'UFPR - Universidade Federal do Paraná',
                    'UFMG - Universidade Federal de Minas Gerais',
                    'Unihorizontes - Centro Universitário Unihorizontes',
                ];
                $programs = [
                    'Centro de Pesq e Pós-Grad em Admin.CEPPAD',
                    'Centro de Pós-Grad e Pesquisas em Admin. CEPEAD',
                    'Curso de Mestr Acadêmico em Admin',
                ];
                for ($i = 1; $i < 10; $i++) {
                    $tableData[] = [
                        'institution' => $institutions[rand(0, 2)],
                        'program' => $programs[rand(0, 2)],
                        'submitted' => rand(40, 400),
                        'selected' => rand(0, 8),
                        'authors' => rand(0, 5),
                    ];
                }
                break;
            case 'articles_qtd':
                $authors = [
                    'Valter de Assis Moreno Jr.',
                    'João Felipe Ferreira',
                    'Antônio Carlos de Abreu',
                ];
                for ($i = 1; $i < 10; $i++) {
                    $tableData[] = [
                        'name' => $authors[rand(0, 2)],
                        'doc' => '1031046232',
                        'passport' => '-',
                        'qtd' => rand(1, 3),
                        'articles' => [
                            chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . rand(2000, 9000),
                            chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . rand(2000, 9000),
                            chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . rand(2000, 9000),
                        ],
                    ];
                }
                break;
            case 'table':
                $fit = ['Artigo Teórico-Empírico', 'Ensaio Teórico', 'Casos para Ensino'];
                for ($i = 1; $i < 10; $i++) {
                    $tableData[] = [
                        'id' => 'EnADI' . rand(1, 90),
                        'theme' => 'Tema ' . rand(1, 9),
                        'fit' => $fit[rand(0, 2)],
                        'ev_final1' => rand(0, 2),
                        'ev_final2' => rand(0, 2),
                        'pont_primary' => rand(0, 18),
                        'pont_secundary' => rand(0, 18),
                        'title' => 'Inovação Startup: Transformando ideias em negócios desucesso',
                        'leader' => 'GOL - Tema 9 - Andrew Beheregarai Finger',
                        'committe' => '',
                        'coord' => '',
                        'dir' => '',
                        'publish' => 'Trabalho Completo',
                        'work' => 'Dissertação Concluída',
                    ];
                }
                break;
        }
        return $tableData;
    }

    /**
     * @Route("/system_evaluation/reports/chart/{period}", name="system_evaluation_reports_chart", methods={"GET"})
     */
    public function system_evaluation_reports_chart($period)
    {
        $pageTitle = 'EnADI 2020';
        $menuBreadcumb = $this->evaluationSubmenu('SYS_EV_MENU_REPORTS');
        if ($period == 'total') {
            $data = [
                '05/03' => rand(200, 3000),
                '09/03' => rand(200, 3000),
                '13/03' => rand(200, 3000),
                '17/03' => rand(200, 3000),
                '21/03' => rand(200, 3000),
                '25/03' => rand(200, 3000),
                '29/03' => rand(200, 3000),
                '02/04' => rand(200, 3000),
                '06/04' => rand(200, 3000),
                '10/04' => rand(200, 3000),
                '14/04' => rand(200, 3000),
                '18/04' => rand(200, 3000),
                '22/04' => rand(200, 3000),
                '26/04' => rand(200, 3000),
            ];
        } else {
            if ($period == 'last') {
                $submissions = ['EnAPG 2019', 'EnEo 2019', 'EnANPAD 2019', '3Es 2020', 'EnADI2020'];
                for ($i = 0; $i < 5; $i++) {
                    $data[] = [
                        'label' => $submissions[$i],
                        'data' => [
                            1 => rand(200, 3000),
                            5 => rand(200, 3000),
                            10 => rand(200, 3000),
                            15 => rand(200, 3000),
                            20 => rand(200, 3000),
                            25 => rand(200, 3000),
                            30 => rand(200, 3000),
                        ],
                    ];
                }
            }
        }
        return $this->render("@Base/system_evaluation/reports/chart.html.twig",
            compact('pageTitle', 'menuBreadcumb', 'period', 'data'));
    }

    /**
     * @Route("/layout_erro/{code}", name="layout_erro", defaults={"code"=0})
     */
    public function layout_erro(Request $request)
    {
        $code = $request->get('code');

        return $this->render('/layout_erro/error.html.twig', compact('code'));
    }

    /**
     * @Route("/certificates", name="certificates")
     */
    public function certificates()
    {
        $pageTitle = 'CERTIFICATES_TITLE';
        $names = ['Adalberto Ramos Cassia', 'Adelcio Machado dos Santos'];
        $types = [
            ['id' => 1, 'name' => 'Avaliação de Trabalhos'],
            ['id' => 2, 'name' => 'Apresentação de Trabalhos'],
            ['id' => 3, 'name' => 'Coordenador de Divisão'],
            ['id' => 4, 'name' => 'Comitês Científicos'],
            ['id' => 5, 'name' => 'Coordenador / Debatedor'],
            ['id' => 6, 'name' => 'Coordenador de Sessão'],
            ['id' => 7, 'name' => 'Debatedor de Sessão'],
        ];
        for ($i = 1; $i <= 10; $i++) {
            $certificates[] = [
                'id' => $i,
                'type' => rand(1, 7),
                'name' => $names[rand(0, 1)],
                'doc' => '00712556954',
                'info' => 'EnEO - Tema 09',
                'status' => rand(0, 1),
                'active_date' => '2020-7-29 00:00:00',
            ];
        }
        return $this->render('/certificates/index.html.twig',
            compact('pageTitle', 'types', 'certificates'));
    }

    /**
     * @Route("/gestor_informativos", name="gestor_informativos", methods={"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function gestor_informativos()
    {
        $menuBreadcumb = [
            ['label' => 'DASHBOARD', 'href' => '/'],
            ['label' => 'EVENTOS', 'href' => '/gestor'],
            ['label' => 'INFORMATIVOS', 'href' => '/gestor_informativos', 'active' => true],
        ];
        $title = ['REGE FEA-USP - CALL FOR PAPERS', 'Sinergie-SIMA 2020 Conference', 'X Colóquio REIAD'];
        for ($i = 1; $i <= 10; $i++) {
            $types[] = [
                'id' => $i,
                'order' => $i,
                'title' => "Título $i",
                'status' => rand(0, 1),
                'hom' => (rand(0, 1) == 1),
            ];
        }
        for ($i = 1; $i <= 10; $i++) {
            $informatives[] = [
                'id' => $i,
                'date_enter' => '2020-8-10 00:00:00',
                'date_out' => '2020-8-10 00:00:00',
                'title' => $title[rand(0, 2)],
                'type' => rand(1, 10),
                'status' => rand(0, 1),
                'highlight' => (rand(0, 1) == 1),
                'desc' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse efficitur leo quam, eu congue nisi feugiat mollis. Pellentesque pharetra quis orci a lacinia. Duis tempor efficitur purus sit amet convallis. Vivamus porta, turpis ac interdum tempus, ipsum magna tincidunt mauris, non aliquet dolor risus non mi. Nulla a laoreet magna. Suspendisse potenti. Maecenas tristique commodo sapien. Suspendisse vehicula metus sem, in suscipit orci condimentum tincidunt.',
            ];
        }
        return $this->render('@Base/gestor_informativos/index.html.twig',
            compact('menuBreadcumb', 'informatives', 'types'));
    }

    /**
     * @Route("/legal/cookieconsent/{choice}", name="legal_cookieconsent", methods={"GET"}, requirements={"choice"="1|2"})
     * @param Request $request
     * @param int $choice
     *
     * @return JsonResponse
     */
    public function legal_cookieconsent(
        Request $request,
        $choice = UserConsents::USER_CONSENTS_STATUS_DECLINE
    ): JsonResponse
    {
        $cookie = Cookie::create('COOKIE_CONSENT')
            ->withValue($request->get('choice'))
            ->withExpires(time() + strtotime('+1 year'))
            ->withDomain($request->getHost())
            ->withSecure($request->isSecure());

        $this->userConsentsService->register(
            $request->getClientIp(),
            $choice,
            UserConsents::USER_CONSENTS_TYPE_COOKIE
        );

        $response = new JsonResponse([], 200);
        $response->headers->setCookie($cookie);

        return $response;
    }

    /**
     * @Route("/legal/cookies", name="legal_cookies", methods={"GET"})
     * @return Response
     */
    public function legal_cookies(): Response
    {
        return $this->render('@Base/legal/cookies.html.twig');
    }
}

