<?php

namespace App\Bundle\Base\EventListener;

use App\Bundle\Base\Entity\ActivitiesGuest;
use App\Bundle\Base\Entity\ActivitiesPanelist;
use App\Bundle\Base\Entity\Activity;
use App\Bundle\Base\Entity\DivisionCoordinator;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\EditionFile;
use App\Bundle\Base\Entity\Event;
use App\Bundle\Base\Entity\Panel;
use App\Bundle\Base\Entity\Program;
use App\Bundle\Base\Entity\Speaker;
use App\Bundle\Base\Entity\Subsection;
use App\Bundle\Base\Entity\SystemEnsalementRooms;
use App\Bundle\Base\Entity\SystemEnsalementScheduling;
use App\Bundle\Base\Entity\SystemEnsalementSessions;
use App\Bundle\Base\Entity\SystemEnsalementSlots;
use App\Bundle\Base\Entity\Theme;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Entity\UserArticlesAuthors;
use App\Bundle\Base\Entity\UserCommittee;
use App\Bundle\Base\Entity\UserInstitutionsPrograms;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesResearchers;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class EditionActivitySubscriber implements EventSubscriberInterface
{
    private TagAwareCacheInterface $cache;

    public function __construct(TagAwareCacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postRemove,
            Events::postUpdate,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->handleActivity('persist', $args);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->handleActivity('remove', $args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->handleActivity('update', $args);
    }

    private function handleActivity(string $action, LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Event) {
            foreach ($entity->getEditions() as $edition) {
                $edition
                && $this->deleteCache($edition);
            }

            return;
        }

        if ($entity instanceof Edition) {
            $this->deleteCache($entity);

            return;
        }

        if ($entity instanceof User) {
            $this->deleteCacheByUser($entity);

            return;
        }

        $class = get_class($entity);

        $children = [
            Activity::class,
            //Certificate::class,
            DivisionCoordinator::class,
            //EditionDiscount::class,
            EditionFile::class,
            //EditionPaymentMode::class,
            //EditionSignup::class,
            //Modality::class,
            Panel::class,
            //PanelEvaluationList::class,
            Speaker::class,
            Subsection::class,
            SystemEnsalementRooms::class,
            SystemEnsalementScheduling::class,
            SystemEnsalementSessions::class,
            SystemEnsalementSlots::class,
            //SystemEvaluationAverages::class,
            //SystemEvaluationConfig::class,
            Theme::class,
            // ThemeEvaluationList::class,
            // Thesis::class,
            UserArticles::class,
            UserCommittee::class,
        ];

        if (in_array($class, $children)) {
            /** @var Edition $edition */
            $edition = $entity->getEdition();

            $edition
            && $this->deleteCache($edition);

            return;
        }

        $children = [
            ActivitiesGuest::class => 'getGuest',
            ActivitiesPanelist::class => 'getPanelist',
            //Certificate::class => 'getUser',
            DivisionCoordinator::class => 'getCoordinator',
            //EditionSignup::class => 'getJoined',
            Panel::class => 'getProponentId',
            //PanelEvaluationLog::class => 'getUser',
            //PanelsPanelist::class => 'getPanelistId',
            Program::class => 'getUser',
            SystemEnsalementScheduling::class => 'getUserRegister',
            //SystemEvaluation::class => 'getUserOwner',
            //SystemEvaluationAverages::class => 'getUser',
            //SystemEvaluationConfig::class => 'getUser',
            //SystemEvaluationIndications::class => 'getUserEvaluator',
            //SystemEvaluationLog::class => 'getUserLog',
            //Thesis::class => 'getUser',
            //UserAcademics::class => 'getUser',
            //UserArticles::class => 'getUserId', // já está na lista de autores
            UserArticlesAuthors::class => 'getUserAuthor',
            //UserAssociation::class => 'getUser',
            UserCommittee::class => 'getUser',
            //UserConsents::class => 'getUser',
            //UserEvaluationArticles::class => 'getUser',
            UserInstitutionsPrograms::class => 'getUser',
            //UserThemeKeyword::class => 'getUser',
            UserThemes::class => 'getUser',
            //UserThemesEvaluationLog::class => 'getUser',
            UserThemesResearchers::class => 'getResearcher',
        ];

        if (array_key_exists($class, $children)) {
            $method = $children[$class];
            /** @var User $edition */
            $user = $entity->{$method}();

            $this->deleteCacheByUser($user);

            return;
        }
    }

    private function deleteCacheByUser(User $user)
    {
        //$user->getAssociations();
        foreach ($user->getUserThemesResearchers() as $userThemesResearcher) {
            if ($userThemesResearcher->getUserThemes()) {
                continue;
            }
            foreach ($userThemesResearcher->getUserThemes()->getUserArticles() as $userArticle) {
                $userArticle->getEdition()
                && $this->deleteCache($userArticle->getEdition());
            }
        }
        foreach ($user->getUserDivisionCoordinator() as $userDivisionCoordinator) {
            $userDivisionCoordinator->getEdition()
            && $this->deleteCache($userDivisionCoordinator->getEdition());
        }
        // já está na lista de autores
        /*foreach ($user->getUserArticles() as $userArticle) {
            $userArticle->getEdition()
            && $this->deleteCache($userArticle->getEdition());
        }*/
        /*foreach ($user->getIndications() as $indication) {
            foreach ($indication->getUserArticles() as $userArticle) {
                $userArticle->getEdition()
                && $this->deleteCache($userArticle->getEdition());
            }
        }*/
        /*foreach ($user->getMethods() as $method) {
            foreach ($method->getUserArticles() as $userArticle) {
                $userArticle->getEdition()
                && $this->deleteCache($userArticle->getEdition());
            }
        }
        foreach ($user->getTheories() as $theory) {
            foreach ($theory->getUserArticles() as $userArticle) {
                $userArticle->getEdition()
                && $this->deleteCache($userArticle->getEdition());
            }
        }*/
        foreach ($user->getUserCommittees() as $userCommittee) {
            $userCommittee->getEdition()
            && $this->deleteCache($userCommittee->getEdition());
        }
        foreach ($user->getActivitiesGuests() as $activitiesGuest) {
            $activitiesGuest->getActivity()
            && $activitiesGuest->getActivity()->getEdition()
            && $this->deleteCache($activitiesGuest->getActivity()->getEdition());
        }
        foreach ($user->getActivitiesPanelists() as $activitiesPanelist) {
            $activitiesPanelist->getActivity()
            && $activitiesPanelist->getActivity()->getEdition()
            && $this->deleteCache($activitiesPanelist->getActivity()->getEdition());
        }
        /*foreach ($user->getCertificates() as $certificate) {
            $certificate->getEdition()
            && $this->deleteCache($certificate->getEdition());
        }*/
        /*foreach ($user->getEditionSignups() as $editionSignup) {
            $editionSignup->getEdition()
            && $this->deleteCache($editionSignup->getEdition());
        }*/
        foreach ($user->getPanels() as $panel) {
            $panel->getEdition()
            && $this->deleteCache($panel->getEdition());
        }
        /*foreach ($user->getPanelEvaluationLogs() as $panelEvaluationLog) {
            $panelEvaluationLog->getPanel()
            && $panelEvaluationLog->getPanel()->getEdition()
            && $this->deleteCache($panelEvaluationLog->getPanel()->getEdition());
        }
        foreach ($user->getPanelsPanelists() as $panelsPanelist) {
            $panelsPanelist->getPanelId()
            && $panelsPanelist->getPanelId()->getEdition()
            && $this->deleteCache($panelsPanelist->getPanelId()->getEdition());
        }*/
        foreach ($user->getSystemEnsalementSchedulingUserRegisters() as $systemEnsalementSchedulingUserRegister) {
            $systemEnsalementSchedulingUserRegister->getEdition()
            && $this->deleteCache($systemEnsalementSchedulingUserRegister->getEdition());
        }
        foreach ($user->getSystemEnsalementSchedulingCoordinatorDebaters1() as $value) {
            $value->getEdition()
            && $this->deleteCache($value->getEdition());
        }
        foreach ($user->getSystemEnsalementSchedulingCoordinatorDebaters2() as $value) {
            $value->getEdition()
            && $this->deleteCache($value->getEdition());
        }
        /*foreach ($user->getSystemEvaluations() as $systemEvaluation) {
            $systemEvaluation->getUserArticles()->getEdition();
        }
        foreach ($user->getSystemEvaluationAverages() as $systemEvaluationAverage) {
            $systemEvaluationAverage->getEdition()
            && $this->deleteCache($systemEvaluationAverage->getEdition());
        }
        foreach ($user->getSystemEvaluationConfigs() as $systemEvaluationConfig) {
            $systemEvaluationConfig->getEdition()
            && $this->deleteCache($systemEvaluationConfig->getEdition());
        }
        foreach ($user->getSystemEvaluationLogs() as $systemEvaluationLog) {
            $systemEvaluationLog->getSystemEvaluation()
            && $systemEvaluationLog->getSystemEvaluation()->getUserArticles()
            && $systemEvaluationLog->getSystemEvaluation()->getUserArticles()->getEdition()
            && $this->deleteCache($systemEvaluationLog->getSystemEvaluation()->getUserArticles()->getEdition());
        }*/
        /*foreach ($user->getTheses() as $thesis) {
            $thesis->getEdition()
            && $this->deleteCache($thesis->getEdition());
        }*/
        foreach ($user->getUserArticlesAuthors() as $userArticlesAuthor) {
            $userArticlesAuthor->getUserArticles()
            && $userArticlesAuthor->getUserArticles()->getEdition()
            && $this->deleteCache($userArticlesAuthor->getUserArticles()->getEdition());
        }
        //$user->getUserConsents();
        //$user->getUserThemeKeywords();
        foreach ($user->getUserThemes() as $userTheme) {
            foreach ($userTheme->getUserArticles() as $userArticle) {
                $userArticle->getEdition()
                && $this->deleteCache($userArticle->getEdition());
            }
        }
        /*foreach ($user->getUserThemesEvaluationLogs() as $userThemesEvaluationLog) {
            if (! $userThemesEvaluationLog->getUserThemes()) {
                continue;
            }

            foreach ($userThemesEvaluationLog->getUserThemes()->getUserArticles() as $userArticle) {
                $userArticle->getEdition()
                && $this->deleteCache($userArticle->getEdition());
            }
        }*/
    }

    private function deleteCache(Edition $edition)
    {
        $this->cache->invalidateTags(['edition.details.' . $edition->getId()]);
    }
}
