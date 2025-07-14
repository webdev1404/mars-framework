<?php

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class LogTest extends Base
{
    public function testMessage()
    {
        $filename = $this->app->log->getFilename('messages');
        $this->app->log->message('some message');
        $this->assertFileExists($filename);
        $this->assertStringContainsString('some message', file_get_contents($filename));
    }

    public function testError()
    {
        $filename = $this->app->log->getFilename('errors');
        $this->app->log->error('some error');
        $this->assertFileExists($filename);
        $this->assertStringContainsString('some error', file_get_contents($filename));
    }

    public function testWarning()
    {
        $filename = $this->app->log->getFilename('warnings');
        $this->app->log->warning('some warning');
        $this->assertFileExists($filename);
        $this->assertStringContainsString('some warning', file_get_contents($filename));
    }

    public function testInfo()
    {
        $filename = $this->app->log->getFilename('info');
        $this->app->log->info('some info');
        $this->assertFileExists($filename);
        $this->assertStringContainsString('some info', file_get_contents($filename));
    }

    public function testSystem()
    {
        $filename = $this->app->log->getFilename('system');
        $this->app->log->system('some system message');
        $this->assertFileExists($filename);
        $this->assertStringContainsString('some system message', file_get_contents($filename));
    }

    public function testException()
    {
        $filename = $this->app->log->getFilename('errors');
        $exception = new \Exception('some exception');
        $this->app->log->exception($exception);
        $this->assertFileExists($filename);
        $this->assertStringContainsString('some exception', file_get_contents($filename));
    }
}
