<?php
/**
* The Form Class
* @package Mars
*/

namespace Mars\Html;

use Mars\App;
use Mars\Alerts\Errors;
use Mars\Html\TagInterface;
use Mars\Html\Input\FormInputInterface;
use Mars\Http\Request\Input;

/**
 * The Form Class
 * Renders a form
 */
class Form extends Tag
{
    /**
     * {@inheritdoc}
     */
    protected static string $tag = 'form';

    /**
     * @var string $url The form's url
     */
    public string $url = '';

    /**
     * @var array $fields The form's fields
     */
    public array $fields = [];

    /**
     * @var array $columns The form's columns
     */
    public array $columns = [];

    /**
     * @var array $attributes The form's attributes
     */
    public array $attributes {
        set(array $attributes) {
            $attributes['action'] ??= $this->app->url;
            $attributes['method'] ??= 'post';

            $this->attributes = $attributes;
        }
    }

    /**
     * @var array $classes The form's classes
     */
    public array $classes {
        set(array $classes) {
            
            $classes['form-column'] ??= 'form-column';
            $classes['form-column-prefix'] ??= 'form-columns-';
            $classes['form-field'] ??= 'form-field';
            $classes['form-field-label'] ??= 'form-field-label';

            $this->classes = $classes;
        }
    }

    /**
     * @var null|array|Input $data The form's data. If null, the data will be taken from the post request
     */
    public null|array|Input $data;

    /**
     * @var Errors $errors The generated errors, if any
     */
    public protected(set) Errors $errors {
        get {
            if (isset($this->errors)) {
                return $this->errors;
            }

            $this->errors = new Errors($this->app);

            return $this->errors;
        }
    }

    /**
     * Builds the form
     * @param string $url The form's url
     * @param array $fields The form's fields
     * @param array $columns The form's columns
     * @param array $attributes The form's attributes
     * @param array $classes The form's classes for fields, columns, etc
     * @param null|array|Input $data
     * @param App|null $app The app
     */
    public function __construct(string $url = '', array $fields = [], array $columns = [], array $attributes = [], array $classes = [], null|array|Input $data = null, ?App $app = null)
    {
        $this->url = $url;
        $this->fields = $fields;
        $this->columns = $columns;
        $this->attributes = $attributes;
        $this->classes = $classes;
        $this->data = $data;
        $this->app = $app;
    }

    /**
     * @see TagInterface::get()
     * {@inheritdoc}
     */
    public function html(string $url = '', array $attributes = []) : string
    {
        if ($url) {
            $this->url = $url;
        }
        if ($attributes) {
            $this->attributes = $attributes;
        }

        $html = $this->open($this->attributes);
        $html.= $this->getFields();
        $html.= $this->close();

        return $html;
    }

    /**
     * Returns the form's fields
     * @return string The form's columns
     */
    protected function getFields() : string
    {
        $html = '';
        $fields_rendered = [];

        //get the fields assigned to columns
        foreach ($this->columns as $name => $fields) {
            $col_count_class = $this->classes['form-column-prefix'] . count($fields);

            $html.= $this->app->html->tags->get('div')->open(['class' => "{$this->classes['form-column']} {$col_count_class}"]);
            foreach ($fields as $field) {
                $html.= $this->getField($field);

                $fields_rendered[$field] = true;
            }
            $html.= $this->app->html->tags->get('div')->close();
        }

        //get the fields that weren't rendered in a column
        foreach ($this->fields as $name => $attributes) {
            if (isset($fields_rendered[$name])) {
                continue;
            }
            
            $html.= $this->getField($name);
        }

        return $html;
    }

    /**
     * Returns a field
     * @param string $name The name of the field
     * @throws \Exception
     * @return string The field
     */
    protected function getField(string $name) : string
    {
        if (!isset($this->fields[$name])) {
            throw new \Exception("Unknown field name '{$name}'");
        }

        $attributes = $this->fields[$name];
        $type = $attributes['type'] ?? '';
        if (!$type) {
            throw new \Exception("The field '{$name}' doesn't have a type set");
        }
        if (!$this->app->html->tags->exists($type)) {
            throw new \Exception("Unknown field type '{$type}' for field '{$name}'");
        }

        $obj = $this->app->html->tags->get($type);
        $attributes = $this->getFieldAttributes($obj, $name, $attributes);

        $html = $this->app->html->tags->get('div')->open(['class' => $this->classes['form-field']]);
        
        //get the label
        $html.= $this->getFieldLabel($attributes);

        if ($obj instanceof FormInputInterface) {
            //get the input field
            $html.= $this->getInputField($obj, $attributes);
        } else {
            //get the non-input field
            $html.= $this->getHtmlField($obj, $attributes);
        }

        $html.= $this->app->html->tags->get('div')->close();

        return $html;
    }

    /**
     * Returns the field's label
     * @param array $attributes The field's attributes
     * @return string The field's label
     */
    protected function getFieldLabel(array $attributes) : string
    {
        if (empty($attributes['label'])) {
            return '';
        }

        $label_attributes = ['class' => $this->classes['form-field-label']];
        if (!empty($attributes['id'])) {
            $label_attributes['for'] = $attributes['id'];
        }

        return $this->app->html->tags->get('label')->html($attributes['label'], $label_attributes);
    }

    /**
     * Returns the field's attributes
     * @param Tag $obj The field
     * @param string $name The field's name
     * @param array $attributes The field's attributes
     * @return array The field's attributes
     */
    protected function getFieldAttributes(Tag $obj, string $name, array $attributes) : array
    {
        if ($obj instanceof FormInputInterface) {
            //set the name and value attributes, if $obj is a form input
            $input_attributes = [];

            $name_attr = $obj->getNameAttribute();
            $val_attr = $obj->getValueAttribute();
            if ($name_attr) {
                $input_attributes[$name_attr] = $name;
            }
            if ($val_attr) {
                $input_attributes[$val_attr] = $this->getInputValue($obj, $name, $attributes);
            }

            $attributes = $input_attributes + $attributes;

            $attributes = $this->generateIdAttribute($attributes);
        }
        
        return $attributes;
    }

    /**
     * Cleans the field's attributes
     * @param Tag $obj The field
     * @param array $attributes The field's attributes
     * @return array The field's attributes
     */
    protected function cleanFieldAttributes(Tag $obj, array $attributes) : array
    {
        unset($attributes['label'], $attributes['type'], $attributes['validate']);

        if ($obj instanceof FormInputInterface) {
            unset($attributes['value_fixed']);
        } else {
            unset($attributes['text']);
        }

        return $attributes;
    }

    /**
     * Returns the html code of a input field
     * @param FormInputInterface $obj The field
     * @param array $attributes The field's attributes
     * @return string The field's input
     */
    protected function getInputField(FormInputInterface $obj, array $attributes) : string
    {
        return $obj->html('', $this->cleanFieldAttributes($obj, $attributes));
    }

    /**
     * Returns the field's html
     * @param FormInputInterface $obj The field
     * @param array $attributes The field's attributes
     * @return string The field's html
     */
    protected function getHtmlField(Tag $obj, array $attributes) : string
    {
        $text = $attributes['text'] ?? '';

        return $obj->html($text, $this->cleanFieldAttributes($obj, $attributes));
    }

    /**
     * Returns the input field's value
     * @param FormInputInterface $obj The field
     * @param string $name The field's name
     * @param array $attributes The field's attributes
     * @return null|string|array The field's value
     */
    protected function getInputValue(FormInputInterface $obj, string $name, array $attributes) : null|string|array
    {
        //if the value is fixed, like for example for hidden fields, return the value attribute iregardless of the post data
        if (!empty($obj::$value_fixed) || !empty($attributes['value_fixed'])) {
            return $attributes[$obj->getValueAttribute()] ?? null;
        }

        $value = $this->getInputValueFromData($name);

        //check if the value is set in on allowed values list
        if ($obj->isAllowedValues($value, $attributes)) {
            return $value;
        }
    
        return $attributes[$obj->getValueAttribute()] ?? null;
    }

    /**
     * Returns the value of an input field, from data
     * @param string $name The name of the field
     * @return string The value of the field
     */
    protected function getInputValueFromData(string $name) : string|array
    {
        if ($this->data === null) {
            //read from post
            if ($this->app->request->is_post) {
                return $this->app->request->post->get($name);
            }
        } else {
            if ($this->data instanceof Input) {
                return $this->data->get($name);
            } else {
                if (isset($this->data[$name])) {
                    return $this->data[$name];
                }
            }
        }

        return '';
    }

    /**
     * Validates the form
     * @return bool True if the form is valid, false otherwise
     */
    public function validate() : bool
    {
        if (!$this->app->validator->validate($this->getValidationData(), $this->getValidationRules())) {
            $this->errors->add($this->app->validator->getErrors());

            return false;
        }

        return true;
    }

    /**
     * Returns the validation rules, based on the fields with a 'validate' attribute
     * @return array The validation rules
     */
    protected function getValidationRules() : array
    {
        $rules = [];
        foreach ($this->fields as $name => $field) {
            if (empty($field['validate'])) {
                continue;
            }

            $rules[$name] = $field['validate'];
        }

        return $rules;
    }

    /**
     * Returns the data to validate
     * @return array The data to validate
     */
    protected function getValidationData() : array
    {
        $data = [];
        if ($this->data === null) {
            $data = $this->app->request->post->data;
        } else {
            if ($this->data instanceof Input) {
                $data = $this->data->data;
            } else {
                $data = $this->data;
            }
        }

        return $data;
    }
}
