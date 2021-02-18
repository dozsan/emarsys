<?php

namespace App;

use \DateTime;

class DueDateCalculator {
    
    private const DEFAULT_DATE_FORMAT      = 'Y-m-d';
    private const DEFAULT_DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    private const TIME_SEPARATOR           = ':';
    
    /**
     * DateTime class -> format -> N
     *
     * @var int[]
     */
    private array $defaultHolidayNumber = [6, 7];
    
    /**
     * @var array
     */
    private array $defaultWorkingHours = [
        'start' => '09:00:00',
        'end'   => '17:00:00'
    ];
    
    /**
     * Non work day => Work day
     *
     * @var string[]
     */
    private array $customHolidayMap = [
        '2021-02-18' => '2021-02-20'
    ];
    
    /**
     * @param DateTime $dateTime
     * @param int      $turnaroundHour
     *
     * @return DateTime
     * @throws \InvalidArgumentException
     */
    public function CalculateDueDate(DateTime $dateTime, int $turnaroundHour = 0): DateTime {
        if (!$this->workTimeValidate($dateTime)) {
            throw new \InvalidArgumentException(sprintf('This time is not during working hours: %s', $dateTime->format(self::DEFAULT_DATE_TIME_FORMAT)));
        }
        if (0 < $turnaroundHour) {
            $dateTime = $this->calculateTurnaroundHour($dateTime, $turnaroundHour);
        }
        return $this->calculateOutput($dateTime);
    }
    
    /**
     * Information panel in console
     */
    public function info(): void {
        $info   = [];
        $info[] .= "=======================================================";
        $info[] .= "Info panel\n";
        $info[] .= sprintf("Working hours: %s - %s", $this->defaultWorkingHours['start'], $this->defaultWorkingHours['end']);
        $info[] .= "Non working day indexes: " . implode(', ', $this->defaultHolidayNumber) . "\n";
        $info[] .= "Custom non working days: ";
        foreach ($this->customHolidayMap as $nonWorkingDay => $workingDay) {
            $info[] .= sprintf("\t %s => %s", $nonWorkingDay, $workingDay);
        }
        $info[] .= "=======================================================";
        echo implode("\n", $info) . "\n";
    }
    
    /**
     * @param DateTime $dateTime
     * @param int      $turnaroundHour
     *
     * @return DateTime
     */
    private function calculateTurnaroundHour(DateTime $dateTime, int $turnaroundHour): DateTime {
        $turnaroundSeconds    = $turnaroundHour * 60 * 60;
        $workingTimeInSeconds = $this->transformToSeconds('end') - $this->transformToSeconds('start');
        
        $while = true;
        do {
            if (!$this->guessHoliday($dateTime)) {
                $startDate = $dateTime;
                $sinceDate = $startDate->diff($this->setTime($dateTime, 'end'));
                
                $turnaroundSeconds -= (($sinceDate->h * 60 * 60) + ($sinceDate->i * 60) + $sinceDate->s);
                $dateTime          = $this->setTime($dateTime, 'start');
                if ($turnaroundSeconds < $workingTimeInSeconds) {
                    $dateTime->setTimestamp($dateTime->getTimestamp() + $turnaroundSeconds);
                    $while = false;
                }
            }
            $dateTime->modify('+1 day');
        } while ($while);
        return $dateTime;
    }
    
    /**
     * @param DateTime $dateTime
     *
     * @return DateTime
     */
    private function calculateOutput(DateTime $dateTime): DateTime {
        if ($this->guessHoliday($dateTime)) {
            $dateTime->modify('+1 day');
            return $this->calculateOutput($dateTime);
        }
        return $dateTime;
    }
    
    /**
     * @param string $key
     *
     * @return int
     */
    private function transformToSeconds(string $key): int {
        $item = explode(self::TIME_SEPARATOR, $this->defaultWorkingHours[$key]);
        return (isset($item[0]) ? (int)$item[0] * 60 * 60 : 0) + (isset($item[1]) ? (int)$item[1] * 60 : 0) + (isset($item[2]) ? (int)$item[2] : 0);
    }
    
    /**
     * @param DateTime $dateTime
     *
     * @return bool
     */
    private function workTimeValidate(DateTime $dateTime): bool {
        if ($this->guessHoliday($dateTime)) {
            return false;
        }
        $timestampStart = $this->setTime($dateTime, 'start')->getTimestamp();
        $timestampEnd   = $this->setTime($dateTime, 'end')->getTimestamp();
        return $timestampStart < $dateTime->getTimestamp() && $dateTime->getTimestamp() < $timestampEnd;
    }
    
    /**
     * @param DateTime $dateTime
     * @param string   $key
     *
     * @return DateTime
     */
    private function setTime(DateTime $dateTime, string $key): DateTime {
        $item          = explode(self::TIME_SEPARATOR, $this->defaultWorkingHours[$key]);
        $cloneDateTime = clone $dateTime;
        $cloneDateTime->setTime(
            (isset($item[0]) ? (int)$item[0] : 0),
            (isset($item[1]) ? (int)$item[1] : 0),
            (isset($item[2]) ? (int)$item[2] : 0),
        );
        return $cloneDateTime;
    }
    
    /**
     * @param DateTime $dateTime
     *
     * @return bool
     */
    private function guessHoliday(DateTime $dateTime): bool {
        $dayNumber  = $dateTime->format('N');
        $formatDate = $dateTime->format(self::DEFAULT_DATE_FORMAT);
        
        if (array_key_exists($formatDate, $this->customHolidayMap)) {
            return true;
        }
        if (in_array($formatDate, array_values($this->customHolidayMap))) {
            return false;
        }
        if (in_array($dayNumber, $this->defaultHolidayNumber)) {
            return true;
        }
        return false;
    }
}

$dueDateCalculator = new DueDateCalculator();
// $dueDateCalculator->info();
/*
$dateTime       = new DateTime('2021-02-17 14:12');
$turnaroundHour = 16;
$dueDateTime    = $dueDateCalculator->CalculateDueDate($dateTime, $turnaroundHour);
echo strtr("\n==========\nInput: {input}\nTurnaround Hours: {th}\nOutput: {output}\n", [
    '{input}'  => $dateTime->format('Y-m-d H:i:s'),
    '{th}'     => $turnaroundHour,
    '{output}' => $dueDateTime->format('Y-m-d H:i:s')
]);
*/
