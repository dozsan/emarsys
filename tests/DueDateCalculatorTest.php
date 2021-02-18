<?php declare(strict_types=1);

namespace Test;

use PHPUnit\Framework\TestCase;
use App\DueDateCalculator;

final class DueDateCalculatorTest extends TestCase {
    
    private static array $validCase = [
        ['2021-02-16 14:12:00', 16, '2021-02-19 14:12:00'],
        ['2021-02-16 14:11:00', 5, '2021-02-17 11:11:00'],
        ['2021-02-17 16:59:59', 2, '2021-02-19 10:59:59'],
        ['2021-02-19 09:01:00', 10, '2021-02-20 11:01:00'],
        ['2021-02-20 16:59:59', 9, '2021-02-23 09:59:59']
    ];
    
    private static array $invalidCase = [
        ['2021-02-18 10:45:23', 11],
        ['2021-02-21 15:00:00', 8],
        ['2021-02-22 19:11:32', 24]
    ];
    
    /**
     * @return DueDateCalculator
     */
    private function dueDateCalculatorFactory(): DueDateCalculator {
        return new DueDateCalculator([
            '2021-02-18' => '2021-02-20'
        ]);
    }
    
    public function testCalculateDueDateValid(): void {
        foreach (self::$validCase as $index => $item) {
            $input           = new \DateTime($item[0]);
            $turnaroundHours = $item[1];
            $output          = new \DateTime($item[2]);
            
            $class = $this->dueDateCalculatorFactory();
            $this->assertEquals(
                $output,
                $class->CalculateDueDate($input, $turnaroundHours),
                sprintf('Loop index: %d, Turnaround hours: %d', $index, $turnaroundHours)
            );
        }
    }
    
    public function testCalculateDueDateInvalid(): void {
        foreach (self::$invalidCase as $index => $item) {
            $input           = new \DateTime($item[0]);
            $turnaroundHours = $item[1];
            
            $class = $this->dueDateCalculatorFactory();
            try {
                $class->CalculateDueDate($input, $turnaroundHours);
            } catch (\Exception $e) {
                $this->assertInstanceOf(\InvalidArgumentException::class, $e);
            }
        }
    }
}
