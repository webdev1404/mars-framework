<?php

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class TimerTest extends Base
{

    public function testGetExecutionTime()
    {
        $executionTime = $this->app->timer->getExecutionTime();
        $this->assertIsFloat($executionTime);
        $this->assertGreaterThanOrEqual(0, $executionTime);
    }

    public function testStopTimer()
    {
        $this->app->timer->start('test_timer');
        usleep(100000); // Sleep for 0.1 seconds
        $timeElapsed = $this->app->timer->stop('test_timer');
        $this->assertIsFloat($timeElapsed);
        $this->assertGreaterThanOrEqual(0.1, $timeElapsed);
    }

    public function testEndTimerWithoutErase()
    {
        $this->app->timer->start('test_timer');
        usleep(100000); // Sleep for 0.1 seconds
        $timeElapsed = $this->app->timer->stop('test_timer', false);
        $this->assertIsFloat($timeElapsed);
        $this->assertGreaterThanOrEqual(0.1, $timeElapsed);
    }

    public function testEndNonExistentTimer()
    {
        $timeElapsed = $this->app->timer->stop('non_existent_timer');
        $this->assertEquals(0, $timeElapsed);
    }
}
