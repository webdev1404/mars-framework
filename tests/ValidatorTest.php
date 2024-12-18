<?php
use Mars\App;

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class ValidatorTest extends Base
{
    public function testIsDatetime()
    {
        $this->assertSame($this->app->validator->isDatetime('xxxxx', 'Y-m-d H:i'), false);
        $this->assertSame($this->app->validator->isDatetime('2015', 'Y-m-d H:i'), false);
        $this->assertSame($this->app->validator->isDatetime('2015-02-35 12:54', 'Y-m-d H:i'), false);
        $this->assertSame($this->app->validator->isDatetime('2015-02-23 12:54', 'Y-m-d H:i'), true);
    }

    public function testIsDate()
    {
        $this->assertSame($this->app->validator->isDate('xxxxx', 'Y-m-d'), false);
        $this->assertSame($this->app->validator->isDate('2015', 'Y-m-d'), false);
        $this->assertSame($this->app->validator->isDate('2015-02-35', 'Y-m-d'), false);
        $this->assertSame($this->app->validator->isDate('2015-02-23', 'Y-m-d'), true);
    }

    public function testIsTime()
    {
        $this->assertSame($this->app->validator->isTime('xxxxx', 'H:i'), false);
        $this->assertSame($this->app->validator->isTime('2015', 'H:i'), false);
        $this->assertSame($this->app->validator->isTime('2015-02-35', 'H:i'), false);
        $this->assertSame($this->app->validator->isTime('25:34', 'H:i'), false);
        $this->assertSame($this->app->validator->isTime('14:26', 'H:i'), true);
    }

    public function testIsUrl()
    {
        $this->assertSame($this->app->validator->isUrl('https://www.google.com/'), true);
        $this->assertSame($this->app->validator->isUrl('http://www.google.com/'), true);
        $this->assertSame($this->app->validator->isUrl('ftp://google.com/'), false);
        $this->assertSame($this->app->validator->isUrl('fTP://google.com/'), false);
        $this->assertSame($this->app->validator->isUrl('google.com/'), false);
    }

    public function testIsEmail()
    {
        $this->assertSame($this->app->validator->isEmail('xxxxx'), false);
        $this->assertSame($this->app->validator->isEmail('xxxxx@mydomain'), false);
        $this->assertSame($this->app->validator->isEmail('xxxxx@mydomain.com'), true);
    }

    public function testIsIp()
    {
        $this->assertSame($this->app->validator->isIp('xxxxxxxx'), false);
        $this->assertSame($this->app->validator->isIp('127.456'), false);
        $this->assertSame($this->app->validator->isIp('127.156.100.0'), true);
        $this->assertSame($this->app->validator->isIp('2001:0db8:0000:0000:0000:8a2e:0370:7334'), true);
        $this->assertSame($this->app->validator->isIp('2001:db8::8a2e:370:7334'), true);
        $this->assertSame($this->app->validator->isIp('xxxxxxxx', true), false);
        $this->assertSame($this->app->validator->isIp('127.*.100.0', true), true);
        $this->assertSame($this->app->validator->isIp('aaa.*.100.0', true), false);
        $this->assertSame($this->app->validator->isIp('2001:0db8:*:*:0000:8a2e:0370:7334', true), true);
    }

    public function testRequired()
    {
        $this->assertSame($this->app->validator->validate(['field' => ''], ['field' => 'req']), false);
        $this->assertSame($this->app->validator->validate(['field' => '   '], ['field' => 'req']), false);
        $this->assertSame($this->app->validator->validate(['field' => 'zzzzz'], ['field' => 'req']), true);
    }

    public function testMin()
    {
        $this->assertSame($this->app->validator->validate(['field' => 12.0], ['field' => 'min:12']), true);
        $this->assertSame($this->app->validator->validate(['field' => 12.1], ['field' => 'min:12']), true);
        $this->assertSame($this->app->validator->validate(['field' => '12'], ['field' => 'min:12']), true);
        $this->assertSame($this->app->validator->validate(['field' => '12'], ['field' => 'min:50']), false);
        $this->assertSame($this->app->validator->validate(['field' => '12'], ['field' => 'min:5']), true);

        $this->expectException(\Exception::class);
        $this->app->validator->validate(['field' => '12'], ['field' => 'min']);
    }

    public function testMax()
    {
        $validator = $this->app->validator;

        $this->assertSame($this->app->validator->validate(['field' => 12.0], ['field' => 'max:12']), true);
        $this->assertSame($this->app->validator->validate(['field' => 12.1], ['field' => 'max:12']), false);
        $this->assertSame($this->app->validator->validate(['field' => '12'], ['field' => 'max:12']), true);
        $this->assertSame($this->app->validator->validate(['field' => '12'], ['field' => 'max:50']), true);
        $this->assertSame($this->app->validator->validate(['field' => '12'], ['field' => 'max:5']), false);

        $this->expectException(\Exception::class);
        $this->app->validator->validate(['field' => '12'], ['field' => 'max']);
    }

    public function testInterval()
    {
        $this->assertSame($this->app->validator->validate(['field' => '12'], ['field' => 'interval:0:20']), true);
        $this->assertSame($this->app->validator->validate(['field' => 12], ['field' => 'interval:0:20']), true);
        $this->assertSame($this->app->validator->validate(['field' => -10], ['field' => 'interval:-20:10']), true);
        $this->assertSame($this->app->validator->validate(['field' => 12], ['field' => 'interval:0:10']), false);

        $this->expectException(\Exception::class);
        $this->app->validator->validate(['field' => '12'], ['field' => 'interval']);

        $this->expectException(\Exception::class);
        $this->app->validator->validate(['field' => '12'], ['field' => 'interval:1:']);
    }

    public function testMinChars()
    {
        $this->assertSame($this->app->validator->validate(['field' => 'abc'], ['field' => 'min_chars:3']), true);
        $this->assertSame($this->app->validator->validate(['field' => 'abc'], ['field' => 'min_chars:5']), false);
        $this->assertSame($this->app->validator->validate(['field' => 'abcdefg'], ['field' => 'min_chars:5']), true);

        $this->expectException(\Exception::class);
        $this->app->validator->validate(['field' => 'abc'], ['field' => 'min_chars']);
    }

    public function testMaxChars()
    {
        $this->assertSame($this->app->validator->validate(['field' => 'abc'], ['field' => 'max_chars:3']), true);
        $this->assertSame($this->app->validator->validate(['field' => 'abc'], ['field' => 'max_chars:5']), true);
        $this->assertSame($this->app->validator->validate(['field' => 'abcdefg'], ['field' => 'max_chars:5']), false);

        $this->expectException(\Exception::class);
        $this->app->validator->validate(['field' => 'abc'], ['field' => 'max_chars']);
    }

    public function testChars()
    {
        $this->assertSame($this->app->validator->validate(['field' => 'abc'], ['field' => 'chars:0:3']), true);
        $this->assertSame($this->app->validator->validate(['field' => 'abc'], ['field' => 'chars:0:5']), true);
        $this->assertSame($this->app->validator->validate(['field' => 'abcdefg'], ['field' => 'chars:0:3']), false);
        $this->assertSame($this->app->validator->validate(['field' => 'abcdefg'], ['field' => 'chars:2:4']), false);

        $this->expectException(\Exception::class);
        $this->app->validator->validate(['field' => 'abc'], ['field' => 'chars']);

        $this->expectException(\Exception::class);
        $this->app->validator->validate(['field' => 'abc'], ['field' => 'chars:5']);
    }

    public function testPattern()
    {
        $this->assertSame($this->app->validator->validate(['field' => '12'], ['field' => 'pattern:/^[a-z0-9]*$/']), true);
        $this->assertSame($this->app->validator->validate(['field' => 'aaa12'], ['field' => 'pattern:/^[a-z0-9]*$/']), true);
        $this->assertSame($this->app->validator->validate(['field' => 'aaa12bbb'], ['field' => 'pattern:/^[a-z0-9]*$/']), true);
        $this->assertSame($this->app->validator->validate(['field' => 'aaa1 2bbb'], ['field' => 'pattern:/^[a-z0-9]*$/']), false);

        $this->expectException(\Exception::class);
        $this->app->validator->validate(['field' => '12'], ['field' => 'pattern']);
    }
}
