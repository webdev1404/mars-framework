<?php
/**
* The Identifier Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;

/**
 * The Identifier Class
 * Generates unique identifiers and UUIDs
 */
class Identifier
{
    use Kernel;

    /**
     * Returns a unique ID from a string, if the provided ID is empty
     * @param string $id The ID to check
     * @param string $value The string to generate the ID from
     * @param int $length The length of the ID
     * @return string The generated ID
     */
    public function get(string $id, string $value, int $length = 12) : string
    {
        if ($id) {
            return $id;
        }

        return $this->generate($value, $length);
    }

    /**
     * Generates a unique ID from a string
     * @param string $value The string to generate the ID from
     * @param int $length The length of the ID
     * @return string The generated ID
     */
    public function generate(string $value, int $length = 12) : string
    {
        return substr(hash('sha256', $value), 0, $length);
    }

    /**
     * Generates a 32-character UUID
     * @return string The uuid
     */
    public function getUuid(): string
    {
        $chars = 32;

        $alphabet = '0123456789abcdefghjkmnpqrstvwxyz';
        $len = strlen($alphabet);

        $time = '';
        $timestamp = (int) floor(microtime(true) * 1000);

        for ($i = 9; $i >= 0; $i--) {
            $time = $alphabet[$timestamp % 32] . $time;
            $timestamp = intdiv($timestamp, 32);
        }

        $random_chars = $chars - strlen($time);

        $random = '';
        for ($i = 0; $i < $random_chars; $i++) {
            $random .= $alphabet[random_int(0, $len - 1)];
        }

        return $random . $time;
    }

    /**
     * Generates a UUID v7
     * @return string The uuid v7
     */
    public function getUuid7(): string
    {
        $timestamp = (int)floor(microtime(true) * 1000);

        $bytes = random_bytes(16);
        $bytes[0] = chr(($timestamp >> 40) & 0xFF);
        $bytes[1] = chr(($timestamp >> 32) & 0xFF);
        $bytes[2] = chr(($timestamp >> 24) & 0xFF);
        $bytes[3] = chr(($timestamp >> 16) & 0xFF);
        $bytes[4] = chr(($timestamp >> 8) & 0xFF);
        $bytes[5] = chr($timestamp & 0xFF);
        $bytes[6] = chr((ord($bytes[6]) & 0x0F) | 0x70);
        $bytes[8] = chr((ord($bytes[8]) & 0x3F) | 0x80);

        $hex = bin2hex($bytes);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }
}
