<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;
use App\Bundle\Base\Entity\ThemeSubmissionConfig;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserThemes as Entity;
use App\Bundle\Base\Entity\UserThemesBibliographies;
use App\Bundle\Base\Entity\UserThemesDetails;
use App\Bundle\Base\Entity\UserThemesResearchers;
use App\Bundle\Base\Form\UserThemesDetailsType;
use App\Bundle\Base\Form\UserThemesResearchersType;
use App\Bundle\Base\Form\UserThemesType;
use App\Bundle\Base\Repository\UserThemesDetailsRepository;
use App\Bundle\Base\Repository\UserThemesRepository;
use App\Bundle\Base\Repository\UserThemesResearchersRepository;
use App\Bundle\Base\Services\User as UserService;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserThemes extends ServiceBase implements ServiceInterface
{
    public static array $languages = [
        'pt_br' => '1',
        'en' => '2',
        'es' => '3',
    ];

    private UserThemesRepository $repository;

    private UserThemesResearchersRepository $researchersRepository;

    private UserThemesDetailsRepository $detailsRepository;

    private UserService $userService;

    public function __construct(UserThemesRepository $userThemesRepository, UserThemesResearchersRepository $researchersRepository, userThemesDetailsRepository $userThemesDetailsRepository, UserService $userService)
    {
        $this->repository = $userThemesRepository;
        $this->researchersRepository = $researchersRepository;
        $this->detailsRepository = $userThemesDetailsRepository;
        $this->userService = $userService;
    }

    /**
     * Usado para preencher o depdrop
     *
     * @param int $theme
     * @param int $lang 1 BR | 2 EN | 3 ES
     *
     * @return array
     */
    public function getKeywordsByThemeLang(int $theme, int $lang): array
    {
        $result = $this->detailsRepository->keywordsByThemeLang($theme, $lang);

        $keywords = [];

        foreach ($result as $item) {
            if (empty($item['keywords'])) {
                continue;
            }

            $tmp = is_string($item['keywords']) ? json_decode($item['keywords'], true) : $item['keywords'];

            if (empty($tmp)) {
                continue;
            }

            foreach ($tmp as $word) {
                if (empty($word)) {
                    continue;
                }

                $keywords[$word] = ['id' => $word, 'name' => $word];
            }
        }

        uasort($keywords, function ($a, $b) {
            return strcmp($a['id'], $b['id']);
        });

        return array_values($keywords);
    }

    /**
     * Usado para preencher o depdrop (todas do idioma da sessão)
     *
     * @return array
     */
    public function getKeywordsByLang($session_lang): array
    {
        $lang = self::$languages[$session_lang] ?? "1";

        $result = $this->detailsRepository->keywordsByLang($lang);

        $keywords = [];

        if (empty($result)) {
            return $keywords;
        }

        foreach ($result as $item) {
            if (empty($item) || empty($item['keywords'])) {
                continue;
            }

            $words = is_string($item['keywords']) ? json_decode($item['keywords'], true) : $item['keywords'];

            if (! is_array($words) || 0 === count($words)) {
                continue;
            }

            $group = trim($item['division'] . ' - ' . $item['title']);

            if (empty($keywords[$group])) {
                $keywords[$group] = [];
            }

            foreach ($words as $word) {
                $word = trim($word);
                if ('' === $word) {
                    continue;
                }

                $keywords[$group][$word] = $word;
            }

            asort($keywords[$group]);
        }

        ksort($keywords);

        return $keywords;
    }

    /**
     * Usado para criar uma lista única de keywords p/ validação no form
     *
     * @return array
     */
    public function getAllKeywords(): array
    {
        $result = $this->detailsRepository->getAllKeywords();

        $keywords = [];
        array_walk($result,
            function ($row) use (&$keywords) {

                if (is_array($row)) {
                    foreach ($row as $lang => $list) {
                        if (empty($list)) {
                            continue;
                        }

                        $words = is_string($list) ? json_decode($list, true) : $list;

                        $keywords = array_merge($keywords, $words);
                    }
                }
            }
        );

        $output = [];
        foreach (array_unique($keywords) as $word) {
            $word = trim($word);
            if (empty($word)) {
                continue;
            }

            $output[] = $word;
        }
        return $output;
    }

    /**
     * @param $division
     *
     * @return array
     */
    public function getByDivision($division): array
    {
        $themes = $this->detailsRepository->getByDivision($division);

        if (0 === count($themes)) {
            return [];
        }

        $result = [];
        foreach ($themes as $theme) {
            $result[] = [
                'id' => $theme->getUserThemes()->getId(),
                'name' => sprintf('%d - %s', $theme->getUserThemes()->getPosition(), $theme->getTitle()),
            ];
        }

        return $result;
    }

    /**
     * @param User|null $user
     *
     * @return int
     */
    public function getApprovedByUser(?User $user)
    {
        return $user
            ? $this->repository->count(['user' => $user, 'status' => Entity::THEME_EVALUATION_APPROVED])
            : 0;
    }

    public function getApproved()
    {
        return $this->repository->count(['status' => Entity::THEME_EVALUATION_APPROVED]);
    }

    public function getCount()
    {
        return $this->repository->count([]);
    }

    public function validateStep1(FormInterface $form, TranslatorInterface $translator): void
    {
        $submitted = [];
        $bibliographies = $form->get('userThemesBibliographies')->getData();
        /** @var UserThemesBibliographies $bibliography */
        foreach ($bibliographies as $index => $bibliography) {
            if (in_array($bibliography->getName(), $submitted)) {
                $form->get('userThemesBibliographies')->get($index)->get('name')->addError(
                    new FormError(UserThemesType::USER_THEMES_BIBLIOGRAPHIES_UNIQUE_MESSAGE)
                );
            }

            $submitted[] = $bibliography->getName();
        }

        /** @var UserThemesDetails $details */
        $details = $form->get('details')->getData();

        // pt
        $wordCount = count(explode(' ', preg_replace('/\s+/m', ' ', strip_tags($details->getPortugueseDescription()))));
        if ($wordCount < UserThemesDetailsType::PORTUGUESE_DESCRIPTION_WORD_COUNT_MIN) {
            $form->get('details')->get('portugueseDescription')->addError(
                new FormError($translator->trans(
                    UserThemesDetailsType::DESCRIPTION_WORD_COUNT_MIN_MESSAGE,
                    ['%min%' => UserThemesDetailsType::PORTUGUESE_DESCRIPTION_WORD_COUNT_MIN]
                ))
            );
        }
        if ($wordCount > UserThemesDetailsType::PORTUGUESE_DESCRIPTION_WORD_COUNT_MAX) {
            $form->get('details')->get('portugueseDescription')->addError(
                new FormError($translator->trans(
                    UserThemesDetailsType::DESCRIPTION_WORD_COUNT_MAX_MESSAGE,
                    ['%max%' => UserThemesDetailsType::PORTUGUESE_DESCRIPTION_WORD_COUNT_MAX]
                ))
            );
        }

        // en
        $wordCount = count(explode(' ', preg_replace('/\s+/m', ' ', strip_tags($details->getEnglishDescription()))));
        if ($wordCount < UserThemesDetailsType::ENGLISH_DESCRIPTION_WORD_COUNT_MIN) {
            $form->get('details')->get('englishDescription')->addError(
                new FormError($translator->trans(
                    UserThemesDetailsType::DESCRIPTION_WORD_COUNT_MIN_MESSAGE,
                    ['%min%' => UserThemesDetailsType::ENGLISH_DESCRIPTION_WORD_COUNT_MIN]
                ))
            );
        }
        if ($wordCount > UserThemesDetailsType::ENGLISH_DESCRIPTION_WORD_COUNT_MAX) {
            $form->get('details')->get('englishDescription')->addError(
                new FormError($translator->trans(
                    UserThemesDetailsType::DESCRIPTION_WORD_COUNT_MAX_MESSAGE,
                    ['%max%' => UserThemesDetailsType::ENGLISH_DESCRIPTION_WORD_COUNT_MAX]
                ))
            );
        }
    }

    public function validateStep2(FormInterface $form, TranslatorInterface $translator, User $user, ThemeSubmissionConfig $config): void
    {
        $institutions = [];
        $submitted = [];
        $isLoggedUserExists = false;
        $isAssociatedPPGCount = 0;
        $isPostgraduateProgramProfessorCount = 0;

        $researchers = $form->get('userThemesResearchers')->getData();

        /** @var UserThemesResearchers $researcher */
        foreach ($researchers as $index => $researcher) {
            if (! $researcher->getResearcher()) {
                continue;
            }

            // valida se o usuário logado está na lista
            if ((int)$researcher->getResearcher()->getId() === (int)$user->getId()) {
                $isLoggedUserExists = true;
            }

            $isAssociatedIndividual = $this->userService->isAssociatedIndividual($researcher->getResearcher());
            $isAssociatedPPG = $this->userService->isAssociatedPPG($researcher->getResearcher());

            $isAssociated = $isAssociatedIndividual || $isAssociatedPPG;

            if ($isAssociatedPPG) {
                $isAssociatedPPGCount++;
            }

            if ($researcher->isPostgraduateProgramProfessor()) {
                $isPostgraduateProgramProfessorCount++;
            }

            if (
                User::USER_RECORD_TYPE_BRAZILIAN === $researcher->getResearcher()->getRecordType()
                && ! $isAssociated
            ) {
                $form->get('userThemesResearchers')->get($index)->get('researcher')->addError(
                    new FormError(UserThemesResearchersType::USER_THEMES_RESEARCHERS_ONLY_ASSOCIATED_MESSAGE)
                );
            }

            $institutionsPrograms = $researcher->getResearcher()->getInstitutionsPrograms();

            if ($institutionsPrograms) {
                $institutionFirst = $institutionsPrograms->getInstitutionFirstId();
                if ($institutionFirst) {
                    if (in_array($institutionFirst->getId(), $institutions)) {
                        $form->get('userThemesResearchers')->get($index)->get('researcher')->addError(
                            new FormError(UserThemesResearchersType::USER_THEMES_RESEARCHERS_UNIQUE_PER_INSTITUTION_MESSAGE)
                        );
                    }

                    // Quando a instituição do proponente for "Outra"
                    // não deve realizar a verificação dos proponentes
                    // estarem vinculados a IES diferentes;
                    if (99999 !== $institutionFirst->getId()) {
                        $institutions[] = $institutionFirst->getId();
                    }
                }

                $institutionSecond = $institutionsPrograms->getInstitutionSecondId();
                if ($institutionSecond) {
                    // previne o erro quando
                    // a instituição secundária for igual a instituição primária
                    if ($institutionSecond !== $institutionFirst) {
                        if (in_array($institutionSecond->getId(), $institutions)) {
                            $form->get('userThemesResearchers')->get($index)->get('researcher')->addError(
                                new FormError(UserThemesResearchersType::USER_THEMES_RESEARCHERS_UNIQUE_PER_INSTITUTION_MESSAGE)
                            );
                        }
                    }

                    // Quando a instituição do proponente for "Outra"
                    // não deve realizar a verificação dos proponentes
                    // estarem vinculados a IES diferentes;
                    if (99999 !== $institutionSecond->getId()) {
                        $institutions[] = $institutionSecond->getId();
                    }
                }
            }

            if (in_array($researcher->getResearcher()->getId(), $submitted)) {
                $form->get('userThemesResearchers')->get($index)->get('researcher')->addError(
                    new FormError(UserThemesType::USER_THEMES_RESEARCHERS_UNIQUE_MESSAGE)
                );
            } else {
                $exists = $this->researchersRepository->findOneResearcherByIdAndSubmissionConfig($researcher->getResearcher(), $config);
                if ($exists instanceof UserThemesResearchers) {
                    $form->get('userThemesResearchers')->get($index)->get('researcher')->addError(
                        new FormError(UserThemesResearchersType::USER_THEMES_RESEARCHERS_LIMIT_BY_THEME_MESSAGE)
                    );
                }
            }

            $submitted[] = $researcher->getResearcher()->getId();

            $wordCount = count(explode(' ', preg_replace('/\s+/m', ' ', $researcher->getBiography())));
            if ($wordCount > UserThemesResearchersType::BIOGRAPHY_WORD_COUNT_MAX) {
                $form->get('userThemesResearchers')->get($index)->get('biography')->addError(
                    new FormError($translator->trans(
                        UserThemesResearchersType::BIOGRAPHY_WORD_COUNT_MAX_MESSAGE,
                        ['%max%' => UserThemesResearchersType::BIOGRAPHY_WORD_COUNT_MAX]
                    ))
                );
            }
        }

        if (
            ! $isLoggedUserExists
            && (
                ! $this->userService->isAdmin($user)
                && ! $this->userService->isAdminOperational($user)
                && ! $this->userService->isDivisionCoordinator($user)
                && ! $this->userService->isDivisionCommittee($user)
            )
        ) {
            $form->get('userThemesResearchers')->addError(
                new FormError(UserThemesResearchersType::IS_LOGGED_USER_EXISTS_MESSAGE)
            );
        }

        if ($isAssociatedPPGCount < UserThemesResearchersType::IS_ASSOCIATED_PPG_COUNT_MIN) {
            $form->get('userThemesResearchers')->addError(
                new FormError(UserThemesResearchersType::IS_ASSOCIATED_PPG_COUNT_MIN_MESSAGE)
            );
        }

        if ($isPostgraduateProgramProfessorCount < UserThemesResearchersType::IS_POSTGRADUATE_PROGRAM_PROFESSOR_COUNT_MIN) {
            $form->get('userThemesResearchers')->addError(
                new FormError(UserThemesResearchersType::IS_POSTGRADUATE_PROGRAM_PROFESSOR_COUNT_MIN_MESSAGE)
            );
        }
    }
}
