<?php
/**
 * Description
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class ContentBlock extends BlockObject
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = 'Content';

    /**
     * Define the default values for all the $db fields
     * @var array
     */
    private static $defaults = array(
        'Title' => 'Content'
    );

    /**
     * Database fields
     * @var array
     */
    private static $db = array(
        'Title' => 'Text',
        'Content' => 'HTMLText'
    );

    /**
     * CMS Page Fields
     * @return FieldList
     */
    public function getCMSPageFields()
    {
        $fields = parent::getCMSPageFields();
        $fields->push(
            HtmlEditorField::create(
                'Content',
                _t('Placeable.CONTENT', 'Content')
            )
        );
        $this->extend('updateCMSPageFields', $fields);
        return $fields;
    }
}
class ContentBlock_Controller extends BlockObject_Controller
{
    public function init() {
        parent::init();
    }
}
class ContentBlock_Preset extends BlockObject_Preset
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
}
