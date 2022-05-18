<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;
use App\Bundle\Base\Repository\EditionRepository;
use DateTimeInterface;

/**
 * Class Edition
 *
 * @package App\Bundle\Base\Services
 */
class Edition extends ServiceBase implements ServiceInterface
{
    /**
     * @var EditionRepository
     */
    private $repository;

    private $colors = [
        'bggBlue' => '#6d84b2',
        'bggBlueVivid' => '#5494de',
        'bggPurple' => '#9b97be',
        'bggGreen' => '#87cab7',
        'bggGreenVivid' => '#58b8bc',
        'bggBrown' => '#bf9292',
        'bggRose' => '#e4a8a8',
        'bggGray' => '#b9bfc9',
        'bggPink' => '#c86b85',
        'bggYellow' => '#f9be5e',
        'bggRed' => '#bd5657',
        'bggOrange' => '#f5ae77',
        'bggLBlueLight' => '#9cbbcf',
    ];

    /**
     * Edition constructor.
     *
     * @param EditionRepository $editionRepository
     */
    public function __construct(
        EditionRepository $editionRepository
    )
    {
        $this->repository = $editionRepository;
    }

    /**
     * @param $event
     *
     * @return \App\Bundle\Base\Entity\Edition[]
     */
    public function getByEvent($event)
    {
        return $this->repository->getByEvent($event);
    }

    /**
     * @return \App\Bundle\Base\Entity\Edition[]
     */
    public function getAll()
    {
        return $this->repository->findAll();
    }

    /**
     * @param $id
     *
     * @return \App\Bundle\Base\Entity\Edition|null
     */
    public function getById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param $name
     *
     * @return \App\Bundle\Base\Entity\Edition[]
     */
    public function getByName($name)
    {
        return $this->repository->findBy(['name_portuguese' => $name]);
    }

    /**
     * @param string $color
     *
     * @return string
     */
    public function getColor(string $color): string
    {
        if (! isset($this->colors[$color])) {
            return '#000000';
        }

        return $this->colors[$color];
    }

    /**
     * @param DateTimeInterface $start
     * @param DateTimeInterface $end
     * @param string $locale
     *
     * @return bool|false|string
     */
    public function dateIntervalFormat(DateTimeInterface $start, DateTimeInterface $end, $locale = 'en')
    {
        $date = $start->format("yyyyMMd");
        $date2 = $end->format("yyyyMMd");

        if ($date == $date2) {
            $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE);
            $result = $formatter->format($start->getTimestamp());
        } else {
            $m = $start->format("MM");
            $m2 = $end->format("MM");

            $y = $start->format("yyyy");
            $y2 = $end->format("yyyy");

            if ($y == $y2) {
                if ($m == $m2) {
                    if ($locale == 'en') {
                        $result = $start->format('MMM d') . ' - ' . $end->format('d, yyyy');
                    } else {
                        $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE);
                        $result = intval($start->format('d')) . ' - ' . $formatter->format($end->getTimestamp());
                    }
                } else {
                    if ($locale == 'en') {
                        $result = $start->format('MM/d');
                    } else {
                        $result = $start->format('d/MM');
                    }
                    $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::SHORT, \IntlDateFormatter::NONE);
                    $formatter->format($end->getTimestamp());
                }
            } else {
                $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::SHORT, \IntlDateFormatter::NONE);
                $result = $formatter->format($start->getTimestamp()) . ' - ' . $formatter->format($end->getTimestamp());
            }
        }

        return $result;
    }
}
