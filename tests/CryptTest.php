<?php
use Mars\App;

include_once(__DIR__ . '/Base.php');

/**
 * @ignore
 */
final class CryptTest extends Base
{
    public function testEncryptDecryptString()
    {
        $crypt = $this->app->crypt;

        $original = 'Hello, Mars!';
        $encrypted = $crypt->encrypt($original);
        $this->assertIsString($encrypted);

        $decrypted = $crypt->decrypt($encrypted);
        $this->assertEquals($original, $decrypted);
    }

    public function testEncryptDecryptArray()
    {
        $crypt = $this->app->crypt;

        $original = ['foo' => 'bar', 'baz' => 42];
        $encrypted = $crypt->encrypt($original);

        $this->assertIsString($encrypted);
        $decrypted = $crypt->decrypt($encrypted);

        $this->assertEquals($original, $decrypted);
    }

    public function testDecryptWithInvalidDataReturnsNull()
    {
        $crypt = $this->app->crypt;

        $invalid = 'not::a::valid::crypt';
        $this->assertNull($crypt->decrypt($invalid));
    }

    public function testDecryptWithUnknownKeyReturnsNull()
    {
        $crypt = $this->app->crypt;

        $original = 'test';
        $encrypted = $crypt->encrypt($original);

        // Tamper with key index
        $parts = explode('::', $encrypted);
        $parts[0] = 'nonexistent_key';
        $tampered = implode('::', $parts);

        $this->assertNull($crypt->decrypt($tampered));
    }

    public function testEncryptDecryptObject()
    {
        $crypt = $this->app->crypt;

        $original = (object)['a' => 1, 'b' => 'test'];
        $encrypted = $crypt->encrypt($original);

        $this->assertIsString($encrypted, 'Encrypted value should be a string');
        $decrypted = $crypt->decrypt($encrypted);

        $this->assertEquals($original, $decrypted);
    }
}
