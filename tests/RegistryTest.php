<?php

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class RegistryTest extends Base
{
    public function testRegistry()
    {
        $this->assertNull($this->app->registry->get('my_val'));

        $this->app->registry->set('my_val', 'test123');
        $this->assertSame($this->app->registry->get('my_val'), 'test123');
    }

    public function testRegistryCallable()
    {
        $this->app->registry->set('my_callable', function ($app) {
            return 'Hello world';
        });

        $this->assertSame($this->app->registry->get('my_callable'), 'Hello world');
    }
}