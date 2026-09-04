<?php

use Mars\Mvc\Controller;

include_once(dirname(__DIR__) . '/Base.php');


class MyController extends Controller
{
    protected bool $load_model = false;
    protected bool $load_view = false;

    public function default()
    {
        echo 'my default';
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
        $this->assertSame('my default', $data->content);

        $data = $controller->dispatch('default');
        $this->assertSame('my default', $data->content);

        $data = $controller->dispatch('myAction');
        $this->assertSame('my action', $data->content);

        $data = $controller->dispatch('myProtectedAction');
        $this->assertSame('my default', $data->content);

        $data = $controller->dispatch('withSuccess');
        $this->assertSame('with successmy default', $data->content);

        $data = $controller->dispatch('withError');
        $this->assertSame('with errormy default', $data->content);
    }
}
