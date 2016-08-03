<?php
/**
 * Description
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class SectionObject extends PlaceableObject
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = 'Section';

    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = 'Sections';

    /**
     * Belongs_many_many relationship
     * @var array
     */
    private static $belongs_many_many = array(
        'Pages' => 'PlaceablePage.Sections'
    );

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        return $fields;
    }
}
class SectionObject_Controller extends PlaceableObject_Controller
{
    public function init() {
        parent::init();
    }
}
class SectionObject_Preset extends PlaceableObject_Preset
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = 'Section';

    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = 'Section Presets';

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $none_string = _t('Placeable.NONE', 'None');
        $fields->addFieldsToTab(
            'Root.Main',
            array(
                DropdownField::create(
                    'ClassName',
                    _t('Placeable.TYPE', 'Type'),
                    singleton('SectionObject')->SubClassPresets
                )->setEmptyString($none_string),
                DropdownField::create(
                    'Style',
                    _t('Placeable.STYLE', 'Style'),
                    singleton('SectionObject')->Styles
                )->setEmptyString($none_string)
            )
        );
        $this->extend('updateCMSFields', $fields);
        return $fields;
    }
}
