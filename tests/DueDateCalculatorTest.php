<?php declare(strict_types=1);

namespace Test;

use PHPUnit\Framework\TestCase;
use App\DueDateCalculator;

final class DueDateCalculatorTest extends TestCase {
    public function testCalculateDueDate(): void {
        /**
         * Index[0]: Input
         * Index[1]: Turnaround hour
         * Index[2]: Expect
         * Index[3]: If true expect exception
         */
        $maps = [
            ['2021-02-16 14:12:00', 16, '2021-02-19 14:12:00', false],
            ['2021-02-16 14:11:00', 5, '2021-02-17 11:11:00', false],
            ['2021-02-17 16:59:59', 2, '2021-02-19 10:59:59', false],
            ['2021-02-18 10:45:23', 11, '2021-02-19 13:45:23', false],
            ['2021-02-19 09:01:00', 10, '2021-02-20 11:01:00', false],
            ['2021-02-20 16:59:59', 9, '2021-02-22 09:59:59', false],
            ['2021-02-21 15:00:00', 8, '2021-02-22 15:00:00', false],
            ['2021-02-22 19:11:32', 24, null, true]
        ];
        
        foreach ($maps as $index => $item) {
            $input           = new \DateTime($item[0]);
            $turnaroundHours = $item[1];
            $output          = isset($item[2]) ? new \DateTime($item[2]) : null;
            $isException     = $item[3];
    
            $mock = $this
                ->getMockBuilder(DueDateCalculator::class)
                ->disableOriginalConstructor()
                ->setMethods(['CalculateDueDate'])
                ->getMock();
            
            $mockMethod = $mock->expects($this->any())->method('CalculateDueDate');
            if (!$isException) {
                $mockMethod->willReturn($output);
                $this->assertEquals(
                    $output,
                    $mock->CalculateDueDate($input, $turnaroundHours),
                    sprintf('Loop index: %d, Turnaround hours: %d', $index, $turnaroundHours)
                );
            } else {
                $mockMethod->willThrowException(new \Exception());
                $mockMethod->willReturn(new \DateTime());
                $mock->CalculateDueDate($input, $turnaroundHours);
            }
        }
    }
}
