<?php

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class HtmlTest extends Base
{
    public function testImg()
    {
        $this->assertSame($this->app->html->img('https://mydomain/mypic.jpg'), '<img src="https://mydomain/mypic.jpg" alt="mypic.jpg">');
        $this->assertSame($this->app->html->img('https://mydomain/mypic.jpg', 200, 100, 'My Alt'), '<img src="https://mydomain/mypic.jpg" alt="My Alt" width="200" height="100">');
    }

    public function testImgWh()
    {
        $this->assertSame($this->app->html->imgWH(200, 100), ' width="200" height="100"');
        $this->assertSame($this->app->html->imgWH(), '');
    }

    public function testPicture()
    {
        $this->assertSame(
            $this->app->html->picture('https://mydomain/mypic.jpg', [['url' => 'https://mydomain/mypic-small.jpg', 'min' => 500], ['url' => 'https://mydomain/mypic-big.jpg', 'min' => 1000]]),
            '<picture>
<source media="(min-width:500px)" srcset="https://mydomain/mypic-small.jpg">
<source media="(min-width:1000px)" srcset="https://mydomain/mypic-big.jpg">
<img src="https://mydomain/mypic.jpg" alt="mypic.jpg">
</picture>' . "\n"
        );

        $this->assertSame(
            $this->app->html->picture('https://mydomain/mypic.jpg', [['url' => 'https://mydomain/mypic-small.jpg', 'min' => 500, 'max' => 1000], ['url' => 'https://mydomain/mypic-big.jpg', 'min' => 1000]]),
            '<picture>
<source media="(min-width:500px) and (max-width:1000px)" srcset="https://mydomain/mypic-small.jpg">
<source media="(min-width:1000px)" srcset="https://mydomain/mypic-big.jpg">
<img src="https://mydomain/mypic.jpg" alt="mypic.jpg">
</picture>' . "\n"
        );
    }

    public function testA()
    {
        $this->assertSame($this->app->html->a('https://mydomain/mypage'), '<a href="https://mydomain/mypage">https://mydomain/mypage</a>');
        $this->assertSame($this->app->html->a('https://mydomain/mypage', 'My Page'), '<a href="https://mydomain/mypage">My Page</a>');
    }

    public function testUl()
    {
        $this->assertSame($this->app->html->ul(['abc', 'def']), '<ul>
<li>abc</li>
<li>def</li>
</ul>' . "\n");

        $this->assertSame($this->app->html->ul(['<a>bc', 'def']), '<ul>
<li><a>bc</li>
<li>def</li>
</ul>' . "\n");
    }

    public function testFormOpen()
    {
        $this->assertSame($this->app->html->formOpen('https://mydomain/mypage'), '<form action="https://mydomain/mypage" method="post">' . "\n");
        $this->assertSame($this->app->html->formOpen('https://mydomain/mypage', ['enctype' => 'multipart/form-data']), '<form action="https://mydomain/mypage" method="post" enctype="multipart/form-data">' . "\n");
    }

    public function testFormClose()
    {
        $this->assertSame($this->app->html->formClose(), '</form>' . "\n");
    }

    public function testInput()
    {
        $this->assertSame($this->app->html->input('foo', 'bar', 'bar placeholder'), '<input type="text" name="foo" value="bar" placeholder="bar placeholder" id="foo">' . "\n");
        $this->assertSame($this->app->html->input('foo', 'bar', 'bar placeholder'), '<input type="text" name="foo" value="bar" placeholder="bar placeholder" id="foo-1">' . "\n");
        $this->assertSame($this->app->html->input('foo', 'bar', 'bar placeholder', false, ['id' => 'my-id']), '<input type="text" name="foo" value="bar" placeholder="bar placeholder" id="my-id">' . "\n");
    }

    public function testhidden()
    {
        $this->assertSame($this->app->html->hidden('foo', 'bar'), '<input type="hidden" name="foo" value="bar" id="foo-2">' . "\n");
        $this->assertSame($this->app->html->hidden('foo', 'bar', ['id' => 'my-id']), '<input type="hidden" name="foo" value="bar" id="my-id">' . "\n");
    }

    public function testEmail()
    {
        $this->assertSame($this->app->html->email('foo-email', 'bar', 'bar placeholder'), '<input type="email" name="foo-email" value="bar" placeholder="bar placeholder" id="foo-email">' . "\n");
    }

    public function testpassword()
    {
        $this->assertSame($this->app->html->password('foo-pass', 'bar'), '<input type="password" name="foo-pass" value="bar" id="foo-pass">' . "\n");
    }

    public function testphone()
    {
        $this->assertSame($this->app->html->phone('foo-phone', 'bar', 'bar placeholder'), '<input type="tel" name="foo-phone" value="bar" placeholder="bar placeholder" id="foo-phone">' . "\n");
    }

    public function testTextarea()
    {
        $this->assertSame($this->app->html->textarea('my-textarea', 'bar'), '<textarea name="my-textarea" id="my-textarea">bar</textarea>' . "\n");
        $this->assertSame($this->app->html->textarea('my-textarea1', '<b>bar</b>'), '<textarea name="my-textarea1" id="my-textarea1">&lt;b&gt;bar&lt;/b&gt;</textarea>' . "\n");
    }

    public function testButton()
    {
        $this->assertSame($this->app->html->button('click now!'), '<input type="button" value="click now!">' . "\n");
    }

    public function testSubmit()
    {
        $this->assertSame($this->app->html->submit('click now!'), '<input type="submit" value="click now!">' . "\n");
    }

    public function testCheckbox()
    {
        $this->assertSame($this->app->html->checkbox('my-checkbox'), '<input type="checkbox" name="my-checkbox" value="1" id="my-checkbox">' . "\n");
        $this->assertSame($this->app->html->checkbox('my-checkbox', 'My Label'), '<input type="checkbox" name="my-checkbox" value="1" id="my-checkbox-1">
<label for="my-checkbox-1">My Label</label>' . "\n");
    }

    public function testRadio()
    {
        $this->assertSame($this->app->html->radio('my-radio'), '<input type="radio" name="my-radio" value="1" id="my-radio">' . "\n");
        $this->assertSame($this->app->html->radio('my-radio', 'My Label'), '<input type="radio" name="my-radio" value="1" id="my-radio-1">
<label for="my-radio-1">My Label</label>' . "\n");
    }

    public function testRadioGroup()
    {
        $this->assertSame($this->app->html->radioGroup('my-radio-group', ['radio1' => 'Radio1', 'radio2' => 'Radio2'], 'radio2'), '<input type="radio" value="radio1" name="my-radio-group" id="my-radio-group">
<label for="my-radio-group">Radio1</label>
<input type="radio" value="radio2" checked name="my-radio-group" id="my-radio-group-1">
<label for="my-radio-group-1">Radio2</label>' . "\n");
    }

    public function testOptions()
    {
        $this->assertSame($this->app->html->options(['option1' => 'Option 1', 'option2' => 'Option 2'], 'option2'), '<option value="option1">Option 1</option>
<option value="option2" selected>Option 2</option>' . "\n");
        $this->assertSame($this->app->html->options(['option1' => 'Option 1', 'option2' => 'Option 2'], ['option1', 'option2']), '<option value="option1" selected>Option 1</option>
<option value="option2" selected>Option 2</option>' . "\n");
        $this->assertSame($this->app->html->options(['foo' => ['option1' => 'Option 1', 'option2' => 'Option 2'], 'bar' => ['option3' => 'Option 3', 'option4' => 'Option 4']], ['option1', 'option4']), '<optgroup label="foo">
<option value="option1" selected>Option 1</option>
<option value="option2">Option 2</option>
</optgroup>
<optgroup label="bar">
<option value="option3">Option 3</option>
<option value="option4" selected>Option 4</option>
</optgroup>' . "\n");
    }

    public function testselectOpen()
    {
        $this->assertSame($this->app->html->selectOpen('my-select'), '<select name="my-select" size="1" id="my-select">' . "\n");
    }

    public function testSelectClose()
    {
        $this->assertSame($this->app->html->selectClose(), '</select>' . "\n");
    }

    public function testSelect()
    {
        $this->assertSame($this->app->html->select('my-select', ['option1' => 'Option 1', 'option2' => 'Option 2'], 'option1', true), '<select name="my-select" required size="1" id="my-select-1">
<option value="option1" selected>Option 1</option>
<option value="option2">Option 2</option>
</select>' . "\n");
    }

    public function testDatetime()
    {
        $this->assertSame($this->app->html->datetime('my-datetime', '2022-07-05 15:34:23'), '<input type="date" name="my-datetime-date" value="2022-07-05" id="my-datetime-date">
&nbsp;<input type="time" name="my-datetime-time" value="15:34:23" id="my-datetime-time">' . "\n");
    }

    public function testDate()
    {
        $this->assertSame($this->app->html->date('my-date', '2022-07-05'), '<input type="date" name="my-date" value="2022-07-05" id="my-date">' . "\n");
    }

    public function testTime()
    {
        $this->assertSame($this->app->html->time('my-time', '15:34:23'), '<input type="time" name="my-time" value="15:34:23" id="my-time">' . "\n");
    }
}
