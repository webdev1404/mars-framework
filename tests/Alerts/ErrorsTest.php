<?php

use Mars\Alerts\Errors;
use Mars\Alerts\Alert;

include_once(dirname(__DIR__) . '/Base.php');

/**
 * @ignore
 */
final class ErrorsTest extends Base
{
    public function testErrors()
    {
        $errors = new Errors($this->app);
        $this->assertTrue($errors->ok());

        $errors->add('1st error');
        $errors->add('2nd error');
        $errors->add('3rd error');

        $this->assertFalse($errors->ok());
        $this->assertSame($errors->count(), 3);
        $this->assertSame($errors->get(), [
            ['text' => '1st error', 'field' => '', 'code' => ''],
            ['text' => '2nd error', 'field' => '', 'code' => ''],
            ['text' => '3rd error', 'field' => '', 'code' => '']
        ]);
        $this->assertSame($errors->getFirst(), ['text' => '1st error', 'field' => '', 'code' => '']);

        $errors->reset();
        $this->assertTrue($errors->ok());
        $this->assertSame($errors->count(), 0);
    }

    public function testAddSingleError()
    {
        $errors = new Errors($this->app);
        $errors->add('Single error');
        $this->assertFalse($errors->ok());
        $this->assertSame($errors->count(), 1);
        $this->assertSame($errors->getFirst(), ['text' => 'Single error', 'field' => '', 'code' => '']);
    }

    public function testAddMultipleErrors()
    {
        $errors = new Errors($this->app);
        $errors->add(['Error 1', 'Error 2']);
        $this->assertFalse($errors->ok());
        $this->assertSame($errors->count(), 2);
        $this->assertSame($errors->get(), ['Error 1', 'Error 2']);
    }

    public function testResetErrors()
    {
        $errors = new Errors($this->app);
        $errors->add('Error to reset');
        $this->assertFalse($errors->ok());
        $errors->reset();
        $this->assertTrue($errors->ok());
        $this->assertSame($errors->count(), 0);
    }

    public function testOkMethodWithNoErrors()
    {
        $errors = new Errors($this->app);
        $this->assertTrue($errors->ok());
    }

    public function testOkMethodWithErrors()
    {
        $errors = new Errors($this->app);
        $errors->add('An error');
        $this->assertFalse($errors->ok());
    }
}
