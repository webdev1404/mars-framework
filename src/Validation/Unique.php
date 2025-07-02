<?php
/**
* The Unique Validator Class
* @package Mars
*/

namespace Mars\Validation;

/**
 * The Unique Validator Class
 */
class Unique extends Rule
{
    /**
     * {@inheritdoc}
     */
    protected string $error = '';

    /**
     * Validates that a value is unique in a table
     * @param string $value The value
     * @param string $table The name of the table
     * @param string $column The name of the column
     * @return bool
     */
    public function isValid(string $value, ?string $table = null, ?string $column = 'id', ?string $error = null) : bool
    {
        if (!$table) {
            throw new \Exception("The 'unique' validation rule must have the name of the table and (optionally) column specified. Eg: unique:users or unique:users:id");
        }

        $this->error = $error ?? 'validate_unique_error';

        $exists = $this->app->db->exists($table, [$column => $value], $column);
        if (!$exists) {
            return true;
        }

        return false;
    }
}
