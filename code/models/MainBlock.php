<?php
/**
 * Display main content of the page
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class MainBlock extends BlockObject
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = 'Main Content';

    /**
     * Define the default values for all the $db fields
     * @var array
     */
    private static $defaults = array(
        'Title' => 'Main'
    );

    /**
     * CMS Page Fields
     * @return FieldList
     */
    public function getCMSPageFields()
    {
        $fields = parent::getCMSPageFields();
        $fields->addFields(
            array(
                ReadonlyField::create('Title')
            )
        );
        $this->extend('updateCMSPageFields', $fields);
        return $fields;
    }

    /**
     * Renders HTML
     *
     * @return string
     **/
    public function getLayout()
    {
        $page = $this->CurrentPage;
        $custom_template = ($this->Style ? '_'.$this->Style : '');
        $page_type = ($page->ClassName ? '_'.$page->ClassName : '');
        $class_name = $this->ClassName;
        $templates = array(
            $class_name.$page_type.$custom_template,
            $class_name.$custom_template,
            $class_name.$page_type,
            $class_name,
            'DefaultPlacement'
        );
        $this->extend('updateLayout', $templates);
        return $page->renderWith($templates);
    }
}
class MainBlock_Controller extends BlockObject_Controller
{
    public function init() {
        parent::init();
    }
}
class MainBlock_Preset extends BlockObject_Preset
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = 'Content';

    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = 'Content Presets';

    /**
     * Sets main(primary) placeable object.
     * Page types must have one main object set.
     *
     * @var boolean
     */
    protected $main = true;
}
