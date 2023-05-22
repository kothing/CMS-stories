<?php

namespace Botble\Analytics;

use Botble\Analytics\Exceptions\InvalidPeriod;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class Period
{
    public CarbonInterface $startDate;

    public CarbonInterface $endDate;

    public function __construct(CarbonInterface $startDate, CarbonInterface $endDate)
    {
        if ($startDate > $endDate) {
            throw InvalidPeriod::startDateCannotBeAfterEndDate($startDate, $endDate);
        }

        $this->startDate = $startDate;

        $this->endDate = $endDate;
    }

    public static function create(CarbonInterface $startDate, CarbonInterface $endDate): static
    {
        return new static($startDate, $endDate);
    }

    public static function days(int $numberOfDays): static
    {
        $endDate = Carbon::today();

        $startDate = Carbon::today()->subDays($numberOfDays)->startOfDay();

        return new static($startDate, $endDate);
    }

    public static function months(int $numberOfMonths): static
    {
        $endDate = Carbon::today();

        $startDate = Carbon::today()->subMonths($numberOfMonths)->startOfDay();

        return new static($startDate, $endDate);
    }

    public static function years(int $numberOfYears): static
    {
        $endDate = Carbon::today();

        $startDate = Carbon::today()->subYears($numberOfYears)->startOfDay();

        return new static($startDate, $endDate);
    }
}
