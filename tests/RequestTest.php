<?php

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class RequestTest extends Base
{
    public function testRequestHas()
    {
        $this->assertFalse($this->app->request->get->has('myvar'));

        $_GET['myvar'] = 123;
        $this->assertTrue($this->app->request->get->has('myvar'));
    }

    public function testRequestGet()
    {
        $_GET['myvar'] = ' some string 1234 ';

        $this->assertSame($this->app->request->get->get('myvar'), 'some string 1234');
        $this->assertSame($this->app->request->get->get('myvar', 'alpha'), 'somestring');
        $this->assertSame($this->app->request->get->get('myvar', '', '', true), ['some string 1234']);
    }

    public function testRequestGetRaw()
    {
        $_GET['myvar'] = ' some string 1234 ';

        $this->assertSame($this->app->request->get->getRaw('myvar'), ' some string 1234 ');
    }

    public function testRequestGetAll()
    {
        $_GET = [];
        $_GET['myvar'] = ' some string 1234 ';
        $_GET['myvar2'] = 'xyz';

        $this->assertSame($this->app->request->get->getAll(), ['myvar' => ' some string 1234 ', 'myvar2' => 'xyz']);
    }

    public function testRequestSet()
    {
        $_GET['myvar'] = '123';

        $this->assertSame($this->app->request->get->get('myvar'), '123');
        $this->app->request->get->set('myvar', 'xyz');
        $this->assertSame($this->app->request->get->get('myvar'), 'xyz');
    }

    public function testRequestUnset()
    {
        $_GET['myvar'] = '123';

        $this->assertSame($this->app->request->get->get('myvar'), '123');
        $this->app->request->get->unset('myvar');
        $this->assertFalse($this->app->request->get->has('myvar'));
    }

    public function testRequestFill()
    {
        $_GET = [
            'var1'=> 'a',
            'var2' => 'b',
            'var3' => 'c'
        ];

        $data = [
            'var1' => '',
            'var2' => ''
        ];

        $this->assertSame($this->app->request->get->fill($data), ['var1' => 'a', 'var2' => 'b']);
        $this->assertSame($this->app->request->get->fill($data, ['var2' => 'int']), ['var1' => 'a', 'var2' => 0]);
        $this->assertSame($this->app->request->get->fill($data, ['var2' => 'int'], [], ['var2']), ['var1' => 'a', 'var2' => '']);
    }

    public function testGetOrderBy()
    {
        $_REQUEST[$this->app->config->request_orderby_param] = 'myfield';

        $this->assertSame($this->app->request->getOrderBy(), 'myfield');
        $this->assertSame($this->app->request->getOrderBy(['myfield', 'val']), 'myfield');
        $this->assertSame($this->app->request->getOrderBy(['myfield' => 'mycol', 'val' => 'myval']), 'mycol');
        $this->assertSame($this->app->request->getOrderBy(['val']), '');
        $this->assertSame($this->app->request->getOrderBy(['val'], 'abc'), 'abc');
    }

    public function testGetOrder()
    {
        $_REQUEST[$this->app->config->request_order_param] = ' ASC ';
        $this->assertSame($this->app->request->getOrder(), 'ASC');

        $_REQUEST[$this->app->config->request_order_param] = ' asc ';
        $this->assertSame($this->app->request->getOrder(), 'ASC');

        $_REQUEST[$this->app->config->request_order_param] = ' ascx ';
        $this->assertSame($this->app->request->getOrder(), '');
    }

    public function testGetPage()
    {
        $_REQUEST[$this->app->config->request_page_param] = 'abc';
        $this->assertSame($this->app->request->getPage(), 0);

        $_REQUEST[$this->app->config->request_page_param] = 20;
        $this->assertSame($this->app->request->getPage(), 20);

        $_REQUEST[$this->app->config->request_page_param] = -20;
        $this->assertSame($this->app->request->getPage(), 20);
    }
}


