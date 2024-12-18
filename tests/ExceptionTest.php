<?php

use Mars\Exception;

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class ExceptionTest extends Base
{
    public function testExceptionMessage()
    {
        $message = 'Test exception message';
        $exception = new Exception($message);

        $this->assertEquals($message, $exception->getMessage());
    }

    public function testExceptionType()
    {
        $type = 'TestType';
        $exception = new Exception('Test message', $type);

        $this->assertEquals($type, $exception->type);
    }

    public function testExceptionCode()
    {
        $code = 123;
        $exception = new Exception('Test message', 'TestType', $code);

        $this->assertEquals($code, $exception->getCode());
    }
}