<?php

namespace App\Twig;

use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Services\Helper\PanelEvaluation;
use App\Bundle\Base\Services\Helper\Permission;
use App\Bundle\Base\Services\Helper\ThemeEvaluation;
use App\Bundle\Base\Services\Helper\UserAssociate;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class AppExtension extends AbstractExtension
{
    private $nivel_associacao = [
        'USER_ASSOCIATIONS_LEVEL_UNDEF' => 'light',
        'USER_ASSOCIATIONS_LEVEL_INACTIVE' => 'secondary',
        'USER_ASSOCIATIONS_LEVEL_OTHER' => 'secondary',
        'USER_ASSOCIATIONS_LEVEL_REGISTER' => 'dark',
        'USER_ASSOCIATIONS_LEVEL_SPEAKER' => 'success',
        'USER_ASSOCIATIONS_LEVEL_DOWNLOAD' => 'secondary',
    ];

    // usar translate
    // verificar se irá usar id ou enum
    private $pagamento_status = [
        ['label' => 'USERLIST_PAYMENT_NOT_PAID', 'color' => 'warning', 'icon' => 'clock'],
        ['label' => 'USERLIST_PAYMENT_PAID', 'color' => 'success', 'icon' => 'check'],
    ];

    private $evento_status = [
        ['label' => 'EVENT_STATUS_INACTIVE', 'color' => 'light'],
        ['label' => 'EVENT_STATUS_ACTIVE', 'color' => 'success'],
    ];

    private $institution_status = [
        ['label' => 'INSTITUTION_STATUS_INACTIVE', 'color' => 'light'],
        ['label' => 'INSTITUTION_STATUS_ACTIVE', 'color' => 'success'],
    ];

    private $program_status = [
        ['label' => 'PROGRAM_STATUS_INACTIVE', 'color' => 'light'],
        ['label' => 'PROGRAM_STATUS_ACTIVE', 'color' => 'success'],
    ];

    private $permissions = [
        ['ROLE_ADMIN' => 'light'],
        ['ROLE_ADMIN_OPERATIONAL' => 'dark'],
        ['ROLE_LEADER' => 'secondary'],
        ['ROLE_DIVISION_COORDINATOR' => 'success'],
        ['ROLE_COMMITTEE' => 'success'],
        ['ROLE_EVALUATOR' => 'secondary'],
        ['ROLE_DIRECTOR' => 'blue'],
    ];

    private $errors = [
        0 => [
            'title' => 'ERROR_0_TITLE',
            'msg' => 'ERROR_0_MSG',
            'text' => 'ERROR_0_TEXT',
            'color' => 'warning',
        ],
        401 => [
            'title' => 'ERROR_401_TITLE',
            'msg' => 'ERROR_401_MSG',
            'text' => 'ERROR_401_TEXT',
            'color' => 'warning',
        ],
        404 => [
            'title' => 'ERROR_404_TITLE',
            'msg' => 'ERROR_404_MSG',
            'text' => 'ERROR_404_TEXT',
            'color' => 'warning',
        ],
        500 => [
            'title' => 'ERROR_500_TITLE',
            'msg' => 'ERROR_500_MSG',
            'text' => 'ERROR_500_TEXT',
            'color' => 'danger',
        ],
    ];

    private $tema_avaliacao_status = [
        'THEME_EVALUATION_STATUS_WAITING' => 'secondary',
        'THEME_EVALUATION_STATUS_NOT_SELECTED' => 'danger',
        'THEME_EVALUATION_STATUS_SELECTED' => 'primary',
        'THEME_EVALUATION_STATUS_APPROVED' => 'success',
        'THEME_EVALUATION_STATUS_CANCELED' => 'light',

        'THEME_EVALUATION_STATUS_WAITING_ARCHIVE' => 'secondary',
        'THEME_EVALUATION_STATUS_NOT_SELECTED_ARCHIVE' => 'danger',
        'THEME_EVALUATION_STATUS_SELECTED_ARCHIVE' => 'primary',
        'THEME_EVALUATION_STATUS_APPROVED_ARCHIVE' => 'success',
        'THEME_EVALUATION_STATUS_CANCELED_ARCHIVE' => 'light',
    ];

    private $article_evaluation_status = [
        'ARTICLE_EVALUATION_STATUS_WAITING' => 'secondary',
        'ARTICLE_EVALUATION_STATUS_APPROVED' => 'success',
        'ARTICLE_EVALUATION_STATUS_REPROVED' => 'danger',
        'ARTICLE_EVALUATION_STATUS_CANCELED' => 'light',
    ];

    private $academic_level = [
        User::USER_LEVEL_MASTER => 'Master`s',
        User::USER_LEVEL_GRADUATE => 'Graduate',
        User::USER_LEVEL_DOCTORATE => 'Doctorate',
    ];

    private $painel_avaliacao_status = [
        ['label' => 'PANEL_EVALUATION_STATUS_WAITING', 'color' => 'warning', 'icon' => 'clock'],
        ['label' => 'PANEL_EVALUATION_STATUS_APPROVED', 'color' => 'success', 'icon' => 'check'],
        ['label' => 'PANEL_EVALUATION_STATUS_REPROVED', 'color' => 'danger'],
        ['label' => 'PANEL_EVALUATION_STATUS_CANCELED', 'color' => 'light'],
    ];

    private $coord_div_status = [
        ['label' => 'SYS_EV_REPORT_COORD_DIV_CHANGED', 'color' => 'success'],
        ['label' => 'SYS_EV_REPORT_COORD_DIV_RATED', 'color' => 'success'],
        ['label' => 'SYS_EV_REPORT_COORD_DIV_REFUSED', 'color' => 'danger'],
        ['label' => 'SYS_EV_REPORT_COORD_DIV_WAITING', 'color' => 'warning'],
    ];

    private $ev_inprogress_status = [
        ['label' => 'SYS_EV_REPORT_INPROGRESS_FINISHED', 'color' => 'success'],
        ['label' => 'SYS_EV_REPORT_INPROGRESS_PENDENT', 'color' => 'danger'],
        ['label' => 'SYS_EV_REPORT_INPROGRESS_INPROGRESS', 'color' => 'warning'],
    ];

    private $ev_final = [
        ['label' => 'SYS_EV_REPORT_FINAL_EV_APPROVE', 'color' => 'success'],
        ['label' => 'SYS_EV_REPORT_FINAL_EV_REJECT', 'color' => 'danger'],
        ['label' => 'SYS_EV_REPORT_FINAL_EV_APPROVE_LOW', 'color' => 'warning'],
    ];

    private $active_inactive = [
        ['label' => 'STATUS_INACTIVE', 'color' => 'light'],
        ['label' => 'STATUS_ACTIVE', 'color' => 'success'],
    ];

    private $room_type = [
        ['label' => 'EVENT_DETAILS_ROOM_TYPE_INTERNATIONAL', 'color' => 'danger'],
        ['label' => 'EVENT_DETAILS_ROOM_TYPE_TRADITIONAL', 'color' => 'info'],
    ];

    public function getFilters()
    {
        return [
            new TwigFilter('nivel_associacao', [$this, 'nivelAssociacao']),
            new TwigFilter('status_pagamento', [$this, 'statusPagamento']),
            new TwigFilter('status_evento', [$this, 'statusEvento']),
            new TwigFilter('status_institution', [$this, 'statusInstitution']),
            new TwigFilter('status_program', [$this, 'statusProgram']),
            new TwigFilter('permission', [$this, 'permission']),
            new TwigFilter('error_msg', [$this, 'errorMsg']),
            new TwigFilter('format_money', [$this, 'formatMoney']),
            new TwigFilter('tema_avaliacao_status', [$this, 'temaAvaliacaoStatus']),
            new TwigFilter('article_evaluation_status', [$this, 'articleEvaluationStatus']),
            new TwigFilter('painel_avaliacao_status', [$this, 'painelAvaliacaoStatus']),
            new TwigFilter('coord_div_status', [$this, 'coordDivStatus']),
            new TwigFilter('ev_inprogress_status', [$this, 'evInprogressStatus']),
            new TwigFilter('ev_final', [$this, 'evFinal']),
            new TwigFilter('active_inactive', [$this, 'activeInactive']),
            new TwigFilter('group_by', [$this, 'groupBy']),
            new TwigFilter('obj_group_by', [$this, 'objGroupBy']),
            new TwigFilter('obj_group_by_int', [$this, 'objGroupByInt']),
            new TwigFilter('ksort', [$this, 'kSort']),
            new TwigFilter('room_type', [$this, 'roomType']),
            new TwigFilter('academic_level', [$this, 'academicLevel']),
            new TwigFilter('array_flip', [$this, 'arrayFlip']),
        ];
    }

    public function getTests()
    {
        return [
            new TwigTest('file_exists', [$this, 'fileExists']),
        ];
    }

    /**
     * @param $index
     * @param $key
     *
     * @return mixed
     */
    public function temaAvaliacaoStatus($index, $key)
    {
        $levels = ThemeEvaluation::getStatus($index);

        if ($key === 'label') {
            return $levels[$index];
        }

        return $this->tema_avaliacao_status[$levels[$index]];
    }

    /**
     * @param $index
     *
     * @return mixed
     */
    public function academicLevel($index)
    {
        return $this->academic_level[$index];
    }


    /**
     * @param $index
     * @param $key
     *
     * @return mixed
     */
    public function articleEvaluationStatus($index, $key)
    {
        $value = array_search($index, UserArticles::ARTICLE_EVALUATION_STATUS);

        if ($key === 'label') {
            return $value;
        }

        if (! isset($this->article_evaluation_status[$value])) {
            return 'default';
        }

        return $this->article_evaluation_status[$value];
    }

    /**
     * @param $index
     * @param $key
     *
     * @return mixed
     */
    public function painelAvaliacaoStatus($index, $key)
    {
        if ($key === 'label') {
            $levels = PanelEvaluation::getStatus($index);

            return $levels[$index][$key];
        }

        if (is_int($index) && in_array($index, range(0, count($this->painel_avaliacao_status) - 1))) {
            return $this->painel_avaliacao_status[$index - 1][$key];
        } else {
            return $this->painel_avaliacao_status[0][$key];
        }
    }

    /**
     * @param $index
     * @param $key
     *
     * @return mixed
     */
    public function nivelAssociacao($index, $key)
    {
        $levels = UserAssociate::getLevel($index);

        if ($key === 'label') {
            return $levels[$index];
        }

        return $this->nivel_associacao[$levels[$index]];
    }

    public function statusPagamento($index, $key)
    {
        if (is_int($index) && in_array($index, range(0, count($this->pagamento_status) - 1))) {
            return $this->pagamento_status[$index][$key];
        } else {
            return $this->pagamento_status[0][$key];
        }
    }

    public function statusEvento($index, $key)
    {
        if (is_int($index) && in_array($index, range(0, count($this->evento_status) - 1))) {
            return $this->evento_status[$index][$key];
        } else {
            return $this->evento_status[0][$key];
        }
    }

    public function statusInstitution($index, $key)
    {
        if (is_bool($index) && in_array($index, range(0, count($this->institution_status) - 1))) {
            return $this->institution_status[(int)$index][$key];
        } else {
            return $this->institution_status[0][$key];
        }
    }

    public function statusProgram($index, $key)
    {
        if (is_int($index) && in_array($index, range(0, count($this->program_status) - 1))) {
            return $this->program_status[$index][$key];
        } else {
            return $this->program_status[0][$key];
        }
    }

    /**
     * @param $index
     * @param $key
     *
     * @return mixed
     */
    public function permission($index, $key)
    {
        $permission = Permission::getPermission($index);

        if ($key === 'label') {
            return $permission[$index];
        }

        return $this->permissions[$permission[$index]];
    }

    public function errorMsg($index, $key)
    {
        if (in_array($index, array_keys($this->errors))) {
            return $this->errors[$index][$key];
        } else {
            return $this->errors[0][$key];
        }
    }

    public function coordDivStatus($index, $key)
    {
        if (in_array($index, array_keys($this->coord_div_status))) {
            return $this->coord_div_status[$index][$key];
        } else {
            return $this->coord_div_status[0][$key];
        }
    }

    public function evInprogressStatus($index, $key)
    {
        if (in_array($index, array_keys($this->ev_inprogress_status))) {
            return $this->ev_inprogress_status[$index][$key];
        } else {
            return $this->ev_inprogress_status[0][$key];
        }
    }

    public function evFinal($index, $key)
    {
        if (in_array($index, array_keys($this->ev_final))) {
            return $this->ev_final[$index][$key];
        } else {
            return $this->ev_final[0][$key];
        }
    }

    public function activeInactive($index, $key)
    {
        if (is_int($index) && in_array($index, range(0, count($this->active_inactive) - 1))) {
            return $this->active_inactive[$index][$key];
        } else {
            return $this->active_inactive[0][$key];
        }
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('form_last_error', [$this, 'formLastError'], ['is_safe' => ['html']]),
        ];
    }

    public function formLastError($form_item)
    {
        $errors = $form_item->vars['errors'];
        $qtd = count($errors);
        if (! $qtd) {
            return null;
        }
        $lasterror = $errors[($qtd - 1)]->getMessage();
        return "<ul><li>{$lasterror}</li></ul>";
    }

    public function groupBy($array, $key)
    {
        $res = [];
        foreach ($array as $item) {
            $res[$item[$key]][] = $item;
        }
        return $res;
    }

    public function objGroupBy($array, $key)
    {
        $res = [];
        foreach ($array as $item) {

            $ref = $item;
            $value = null;

            $keys = explode('.', $key);

            foreach ($keys as $k) {
                if (! $ref) {
                    continue;
                }
                if (method_exists($ref, $k)) {
                    $ref = $ref->$k();
                } elseif (property_exists($ref, $k)) {
                    $value = $ref->$k;
                }
            }

            if (is_null($value)) {
                if (is_object($ref)) {
                    if ($ref instanceof \DateTimeInterface) {
                        $value = $ref->format('Y-m-d H:i:s');
                    } elseif (method_exists($ref, 'getId')) {
                        $value = $ref->getId();
                    }
                } else {
                    $value = $ref;
                }
            }

            $res[$value][] = $item;
        }

        return $res;
    }

    public function objGroupByInt($array, $key)
    {
        $tmp = $this->objGroupBy($array, $key);

        $res = [];

        if (! count($tmp)) {
            return $res;
        }

        foreach ($tmp as $key => $items) {
            if (! isset($res[(int)$key])) {
                $res[(int)$key] = [];
            }

            foreach ($items as $item) {
                $res[(int)$key][] = $item;
            }
        }

        return $res;
    }

    public function kSort($array)
    {
        ksort($array);
        return $array;
    }

    public function roomType($index, $key)
    {
        if (is_int($index) && in_array($index, range(0, count($this->room_type) - 1))) {
            return $this->room_type[$index][$key];
        } else {
            return $this->room_type[0][$key];
        }
    }

    public function arrayFlip($array)
    {
        return array_flip($array);
    }

    public function fileExists($filename)
    {
        return file_exists($filename) && is_file($filename);
    }
}
