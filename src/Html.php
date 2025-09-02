<?php
/**
* The HTML Class
* @package Mars
*/

namespace Mars;

use Mars\App\Kernel;
use Mars\App\Handlers;
use Mars\Html\TagInterface;
use Mars\Html\Tag;
use Mars\Html\Form;
use Mars\Html\Div;
use Mars\Html\Input\Select;
use Mars\Http\Request\Input;

/**
 * The HTML Class
 * Html generating methods
 */
class Html
{
    use Kernel;

    /**
     * @var array $supported_tags The list of supported tags
     */
    public protected(set) array $supported_tags = [
        'a' => \Mars\Html\A::class,
        'div' => \Mars\Html\Div::class,
        'p' => \Mars\Html\P::class,
        'img' => \Mars\Html\Img::class,
        'picture' => \Mars\Html\Picture::class,
        'video' => \Mars\Html\Video::class,
        'ul' => \Mars\Html\Lists\UL::class,
        'ol' => \Mars\Html\Lists\OL::class,
        'label' => \Mars\Html\Label::class,
        'form' => \Mars\Html\Form::class,
        'input' => \Mars\Html\Input\Input::class,
        'text' => \Mars\Html\Input\Text::class,
        'hidden' => \Mars\Html\Input\Hidden::class,
        'email' => \Mars\Html\Input\Email::class,
        'password' => \Mars\Html\Input\Password::class,
        'phone' => \Mars\Html\Input\Phone::class,
        'textarea' => \Mars\Html\Input\Textarea::class,
        'button' => \Mars\Html\Input\Button::class,
        'submit' => \Mars\Html\Input\Submit::class,
        'checkbox' => \Mars\Html\Input\Checkbox::class,
        'checkbox_group' => \Mars\Html\Input\CheckboxGroup::class,
        'radio' => \Mars\Html\Input\Radio::class,
        'radio_group' => \Mars\Html\Input\RadioGroup::class,
        'options' => \Mars\Html\Input\Options::class,
        'select' => \Mars\Html\Input\Select::class,
        'datetime' => \Mars\Html\Input\Datetime::class,
        'date' => \Mars\Html\Input\Date::class,
        'time' => \Mars\Html\Input\Time::class
    ];

    /**
     * @var Handlers $handlers The tags object
     */
    public protected(set) Handlers $tags {
        get {
            if (isset($this->tags)) {
                return $this->tags;
            }

            $this->tags = new Handlers($this->supported_tags, TagInterface::class, $this->app);

            return $this->tags;
        }
    }

    /**
     * Returns a tag
     * @param string $type The tag's type
     * @param string $text The tag's text
     * @param array $attributes The tag's attributes
     * @return string The html code
     */
    public function getTag(string $type, string $text = '', array $attributes = []) : string
    {
        try {
            return $this->tags->get($type)->html($text, $attributes);
        } catch (\Exception $e) {
            throw new \Exception("Invalid html tag {$type}");
        }
    }

    /**
     * Merges the attributes and returns the html code
     * @param array $attributes The attributes in the format name => value
     * @param array $empty_attributes List of attributes which will be added even if empty
     * @return string The attribute's html code
     */
    public function getAttributes(array $attributes, array $empty_attributes = []) : string
    {
        if (!$attributes) {
            return '';
        }

        $attributes_array = [];
        foreach ($attributes as $name => $value) {
            if (is_array($value)) {
                //don't escape if $value is an array
                $value = reset($value);
            } else {
                if (!is_bool($value)) {
                    $value = $this->app->escape->html($value);
                }
            }

            if ($value || in_array($name, $empty_attributes)) {
                if (is_bool($value)) {
                    $attributes_array[] = $name;
                } else {
                    $attributes_array[] = $name . '="' . $value . '"';
                }
            }
        }

        if (!$attributes_array) {
            return '';
        }

        return ' ' . implode(' ', $attributes_array);
    }

    /**
     * Creates an img tag
     * @param string $url The image's url
     * @param int $width The image's width
     * @param int $height The image's height
     * @param alt $alt The alt attribute.If empty it will be determined from the basename of the source
     * @param array $attributes The image's attributes
     * @return string The html code
     */
    public function img(string $url, int $width = 0, int $height = 0, string $alt = '', array $attributes = []) : string
    {
        $attributes = ['src' => $url, 'alt' => $alt, 'width' => $width, 'height' => $height] + $attributes;

        return $this->getTag('img', '', $attributes);
    }

    /**
     * Returns the width and height attributes of an image
     * @param int $width The image's width
     * @param int $height The image's height
     * @return string The html code
     */
    public function imgWH(int $width = 0, int $height = 0) : string
    {
        return $this->getAttributes(['width' => $width, 'height' => $height]);
    }

    /**
     * Creates a picture tag
     * @param string $url The image's url
     * @param array $source_images Array listing the source images in the format [['url' => <url>, 'min' => <min_width>, 'max' => 'max_width']]. Both min and max can be specified, or just one of it
     * @param int $width The image's width
     * @param int $height The image's height
     * @param alt $alt The alt attribute.If empty it will be determined from the basename of the source
     * @param array $attributes The image's attributes
     * @return string The html code
     */
    public function picture(string $url, array $source_images, int $width = 0, int $height = 0, string $alt = '', array $attributes = []) : string
    {
        $attributes = ['src' => $url, 'alt' => $alt, 'width' => $width, 'height' => $height, 'images' => $source_images] + $attributes;

        return $this->getTag('picture', '', $attributes);
    }

    /**
     * Creates a video tag
     * @param string|array $url The video's source url(s)
     * @param int $width The video's width
     * @param int $height The video's height
     * @param array $attributes The video's attributes
     * @return string The html code
     */
    public function video(string|array $url, int $width = 0, int $height = 0, array $attributes = []) : string
    {
        $attributes = ['width' => $width, 'height' => $height, 'urls' => (array)$url] + $attributes;

        return $this->getTag('video', '', $attributes);
    }

    /**
     * Creates a link
     * @param string $url The link's url
     * @param string $text The link text.If empty $url will be displayed insteed
     * @param array $attributes The link's attributes
     * @return string The html code
     */
    public function a(string $url, string $text = '', array $attributes = []) : string
    {
        if (!$text) {
            $text = $url;
        }

        $attributes = ['href' => $url] + $attributes;

        return $this->getTag('a', $text, $attributes);
    }

    /**
     * Alias for a()
     * @see Html::a()
     */
    public function link(string $url, string $text = '', array $attributes = []) : string
    {
        return $this->a($url, $text, $attributes);
    }

    /**
     * Creates a div
     * @param string $text The paragraph's text
     * @return string The html code
     */
    public function div(string $text, array $attributes = []) : string
    {
        return $this->getTag('div', $text, $attributes);
    }

    /**
     * Creates a text paragraph
     * @param string $text The paragraph's text
     * @return string The html code
     */
    public function p(string $text, array $attributes = []) : string
    {
        return $this->getTag('p', $text, $attributes);
    }

    /**
     * Builds an unordered list
     * @param array $items The lists's items
     * @param array $attributes The list's attributes
     * @return string The html code
     */
    public function ul(array $items, array $attributes = []) : string
    {
        $attributes = $attributes + ['items' => $items];

        return $this->getTag('ul', '', $attributes);
    }

    /**
     * Builds an ordered list
     * @param array $items The lists's items
     * @param array $attributes The list's attributes
     * @return string The html code
     */
    public function ol(array $items, array $attributes = []) : string
    {
        $attributes = $attributes + ['items' => $items];

        return $this->getTag('ol', '', $attributes);
    }

    /**
     * Returns checked if $checked is true, empty if false
     * @param bool $checked The checked flag
     * @return string
     */
    public function checked(bool $checked = true) : string
    {
        return $checked ? ' checked' : '';
    }

    /**
     * Returns checked if $value is found in $array
     * @param string $value The value to look for
     * @param array $array The arrach to search for the value
     * @return string
     */
    public function checkedInArray($value, array $array) : string
    {
        return $this->checked(in_array($value, $array));
    }

    /**
     * Returns disabled if $disabled is true, empty if false
     * @param bool $disabled The disabled flag
     * @return string
     */
    public function isDisabled(bool $disabled = true) : string
    {
        return $disabled ? ' disabled' : '';
    }

    /**
     * Returns style="display:none" if $hidden is true, empty if false
     * @param bool $hidden The hidden flag
     * @return string
     */
    public function isHidden(bool $hidden = true) : string
    {
        return $hidden ? ' hidden' : '';
    }

    /**
     * Returns required if $required is true
     * @param bool $required The required flag
     * @return string
     */
    public function isRequired(bool $required = true) : string
    {
        return $required ? ' required' : '';
    }

    /**
     * Returns a label
     * @param string $text The label's text
     * @param string $for The id of the element the label is for
     * @param array $attributes Extra attributes in the format name => value
     * @return string The html code
     */
    public function label(string $text, string $for = '', array $attributes = []) : string
    {
        $attributes = ['for' => $for] + $attributes;

        return $this->getTag('label', $text, $attributes);
    }

    /**
     * Returns the opening tag of a form
     * @param string $url The url used as the form's action
     * @param array $attributes Extra attributes in the format name => value
     * @param string $method The form's method
     * @return string The html code
     */
    public function formOpen(string $url, array $attributes = [], string $method = 'post') : string
    {
        $attributes = ['action' => $url, 'method' => $method] + $attributes;

        return new Form(app: $this->app)->open($attributes);
    }

    /**
     * Returns the closing tag of a form
     * @return string The html code
     */
    public function formClose() : string
    {
        return new Form(app: $this->app)->close();
    }

    /**
     * Builds a form
     * @param string $url The form's url
     * @param array $fields The form's fields
     * @param array $columns The form's columns
     * @param array $attributes The form's attributes
     * @param array $classes The form's classes for fields, columns, etc
     * @param null|array|Input $data
     */
    public function form(string $url, array $fields, array $columns, array $attributes = [], array $classes = [], null|array|Input $data = null) : string
    {
        return new Form($url, $fields, $columns, $attributes, $classes, $data, $this->app)->html();
    }

    /**
     * Builds an input field
     * @param string $name The name of the field
     * @param string $value The value of the field
     * @param string $placeholder Placeholder text
     * @param bool $required If true, this is a required field
     * @param array $attributes Extra attributes in the format name => value
     * @param string $type The type of the input field
     * @return string The html code
     */
    public function input(string $name, string $value = '', string $placeholder = '', bool $required = false, array $attributes = [], string $type = 'input') : string
    {
        $attributes = ['name' => $name, 'value'=> $value, 'placeholder' => $placeholder, 'required' => $required] + $attributes;

        return $this->getTag($type, '', $attributes);
    }

    /**
     * Alias for input()
     * @see Html::input()
     */
    public function text(string $name, string $value = '', string $placeholder = '', bool $required = false, array $attributes = []) : string
    {
        return $this->input($name, $value, $placeholder, $required, $attributes);
    }

    /**
     * Builds a hidden input field
     * @param string $name The name of the hidden field
     * @param string $value The value of the hidden field
     * @param array $attributes Extra attributes in the format name => value
     * @return string The html code
     */
    public function hidden(string $name, string $value, array $attributes = []) : string
    {
        $attributes = ['name' => $name, 'value'=> $value] + $attributes;

        return $this->getTag('hidden', '', $attributes);
    }

    /**
     * Builds an email input field
     * @param string $name The name of the hidden field
     * @param string $value The value of the hidden field
     * @param string $placeholder Placeholder text
     * @param bool $required If true, this is a required field
     * @param array $attributes Extra attributes in the format name => value
     * @return string The html code
     */
    public function email(string $name, string $value = '', string $placeholder = '', bool $required = false, array $attributes = []) : string
    {
        return $this->input($name, $value, $placeholder, $required, $attributes, 'email');
    }

    /**
     * Builds a password input field
     * @param string $name The name of the hidden field
     * @param string $value The value of the hidden field
     * @param bool $required If true, this is a required field
     * @param array $attributes Extra attributes in the format name => value
     * @return string The html code
     */
    public function password(string $name, string $value = '', bool $required = false, array $attributes = []) : string
    {
        return $this->input($name, $value, '', $required, $attributes, 'password');
    }

    /**
     * Builds a phone input field
     * @param string $name The name of the hidden field
     * @param string $value The value of the hidden field
     * @param string $placeholder Placeholder text
     * @param bool $required If true, this is a required field
     * @param array $attributes Extra attributes in the format name => value
     * @return string The html code
     */
    public function phone(string $name, string $value = '', string $placeholder = '', bool $required = false, array $attributes = []) : string
    {
        return $this->input($name, $value, $placeholder, $required, $attributes, 'phone');
    }

    /**
     * Builds a textarea
     * @param string $name The name of the textarea
     * @param string $value The value of the field
     * @param array $attributes Extra attributes in the format name => value
     * @return string The html code
     */
    public function textarea(string $name, string $value = '', array $attributes = []) : string
    {
        $attributes = ['name' => $name] + $attributes;

        return $this->getTag('textarea', $value, $attributes);
    }

    /**
     * Builds a button field
     * @param string $value The value of the field
     * @param array $attributes Extra attributes in the format name => value
     * @return string The html code
     */
    public function button(string $value, array $attributes = []) : string
    {
        $attributes = ['value'=> $value] + $attributes;

        return $this->getTag('button', '', $attributes);
    }

    /**
     * Builds a submit button field
     * @param string $value The value of the field
     * @param array $attributes Extra attributes in the format name => value
     * @return string The html code
     */
    public function submit(string $value = '', array $attributes = []) : string
    {
        $attributes = ['type' => 'submit', 'value'=> $value] + $attributes;

        return $this->getTag('submit', '', $attributes);
    }

    /**
     * Returns a form checkbox field
     * @param string $name The name of the field
     * @param string $label The label of the checkbox
     * @param string $value The value of the checkbox
     * @param bool $checked If true the checkbox will be checked
     * @param array $attributes Extra attributes in the format name => value
     * @return string The html code
     */
    public function checkbox(string $name, string $label = '', string $value = '1', bool $checked = true, array $attributes = []) : string
    {
        $attributes = ['name' => $name, 'value' => $value, 'checked' => $checked, 'label' => $label] + $attributes;

        return $this->getTag('checkbox', '', $attributes);
    }

    /**
     * Returns a form radio field
     * @param string $name The name of the field
     * @param string $label The label of the radio
     * @param string $value The value of the radio button
     * @param bool $checked If true the radio button will be checked
     * @param array $attributes Extra attributes in the format name => value
     * @return string The html code
     */
    public function radio(string $name, string $label = '', string $value = '1', bool $checked = true, array $attributes = []) : string
    {
        $attributes = ['name' => $name, 'value' => $value, 'checked' => $checked, 'label' => $label] + $attributes;

        return $this->getTag('radio', '', $attributes);
    }

    /**
     * Returns a radios group field
     * @param string $name The name of the field
     * @param array $values The values, in the format $value => $label
     * @param string $checked The checked value
     * @param array $attributes Extra attributes in the format name => value, which will be applied to all radios
     * @return string The html code
     */
    public function radioGroup(string $name, array $values, string $checked = '', array $attributes = []) : string
    {
        $attributes = ['name' => $name, 'values' => $values, 'checked' => $checked] + $attributes;

        return $this->getTag('radio_group', '', $attributes);
    }

    /**
     * Builds a <select> tag
     * @param string $name The name of the select control
     * @param array $attributes Extra attributes in the format name => value
     * @return string The html code
     */
    public function selectOpen(string $name, array $attributes = []) : string
    {
        $attributes = ['name' => $name] + $attributes;

        return new Select($this->app)->open($attributes);
    }

    /**
     * Builds a </select> tag
     * @return string The html code
     */
    public function selectClose() : string
    {
        return new Select($this->app)->close();
    }

    /**
     * Builds a select control
     * @param string $name The name of the select control
     * @param array $options Array containing the options [$name=>$value]. If $value is an array the first element will be the actual value. The second is a bool value determining if the field is an optgroup rather than a option
     * @param string|array $selected The name of the option that should be selected [string or array if $multiple =  true]
     * @param bool $required If true,it will be a required control
     * @param array $attributes Extra attributes in the format name => value
     * @param bool $multiple If true multiple options can be selected
     * @return string The html code
     */
    public function select(string $name, array $options, string|array $selected = '', bool $required = false, array $attributes = [], bool $multiple = false) : string
    {
        $attributes = ['name' => $name, 'required' => $required, 'multiple' => $multiple, 'options' => $options, 'selected' => $selected] + $attributes;

        return $this->getTag('select', '', $attributes);
    }

    /**
     * Builds multiple options tags - used in drop-down boxes.
     * @param array $options Array containing the options [$value => $name]. Eg: ['option1' => 'Option 1', 'option2' => 'Option 2']. If $name is an array, an opgroup will be created. Eg: ['Foo' => ['option1' => 'Option 1', 'option2' => 'Option 2'], 'Bar' => ['option3' => 'Option 3', 'option4' => 'Option 4']]
     * @param string $selected The name of the option that should be selected
     * @return string The html code
     */
    public function options(array $options, string|array $selected = '') : string
    {
        return $this->getTag('options', '', ['options' => $options, 'selected' => $selected]);
    }

    /**
     * Returns controls from where the user will be able to select the date and time
     * @param string $name The name of the control.
     * @param string $date The value of the date control in the yyyy-mm-dd hh:mm:ss format
     * @param bool $required If true the datetime control will be a required control
     * @param array $attributes Extra attributes in the format name => value
     * @return string The html code
     */
    public function datetime(string $name, string $date = '', bool $required = false, array $attributes = []) : string
    {
        if (!$date) {
            $date = time();
        }

        $attributes = ['name' => $name, 'required' => $required, 'value' => $this->app->datetime->get($date, 'Y-m-d H:i:s')] + $attributes;

        return $this->getTag('datetime', '', $attributes);
    }

    /**
     * Returns a control from where the user will be able to select the date
     * @param string $name The name of the control.
     * @param string $date The value of the date control in the yyyy-mm-dd format
     * @param bool $required If true the date control will be a required control
     * @param array $attributes Extra attributes in the format name => value
     * @return string The html code
     */
    public function date(string $name, string $date = '', bool $required = false, array $attributes = []) : string
    {
        if (!$date) {
            $date = time();
        }

        $attributes = ['name' => $name, 'required' => $required, 'value' => $this->app->date->get($date, 'Y-m-d')] + $attributes;

        return $this->getTag('date', '', $attributes);
    }

    /**
     * Returns a control from where the user will be able to select the time of the day
     * @param string $name The name of the control.
     * @param string $date The value of the date control in the hh:mm:ss format
     * @param bool $required If true the date control will be a required control
     * @param array $attributes Extra attributes in the format name => value
     * @return string The html code
     */
    public function time(string $name, string $date = '', bool $required = false, array $attributes = []) : string
    {
        if (!$date) {
            $date = time();
        }

        $attributes = ['name' => $name, 'required' => $required, 'value' => $this->app->time->get($date, 'H:i:s')] +  $attributes;

        return $this->getTag('time', '', $attributes);
    }
}
