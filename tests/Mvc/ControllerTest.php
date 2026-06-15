<?php

use Mars\Mvc\Controller;

include_once(dirname(__DIR__) . '/Base.php');


class MyController extends Controller
{
    protected bool $load_model = false;
    protected bool $load_view = false;

    public protected(set) string $default_success_method = 'mySuccess';
    public protected(set) string $default_error_method = 'myError';

    public function index()
    {
        echo 'my index';
    }

    public function myAction()
    {
        echo 'my action';
    }

    public function mySuccess()
    {
        echo 'my success';
    }

    public function myError()
    {
        echo 'my error';
    }

    protected function myProtectedAction()
    {
        echo 'none';
    }

    public function withSuccess()
    {
        echo 'with success';
        return true;
    }

    public function withError()
    {
        echo 'with error';
        return false;
    }

    public function myJson()
    {
        return [
            'foo' => 'bar'
        ];
    }
}

/**
 * @ignore
 */
final class ControllerTest extends Base
{
    public function testDispatch()
    {
        $controller = new MyController;

        $data = $controller->dispatch();
        $this->assertSame('my index', $data->content);

        $data = $controller->dispatch('index');
        $this->assertSame('my index', $data->content);

        $data = $controller->dispatch('myAction');
        $this->assertSame('my action', $data->content);

        $data = $controller->dispatch('myProtectedAction');
        $this->assertSame('my index', $data->content);

        $data = $controller->dispatch('withSuccess');
        $this->assertSame('my success', $data->content);

        $data = $controller->dispatch('withError');
        $this->assertSame('my error', $data->content);
    }
}
