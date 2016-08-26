<?php
/**
 * A abstract class.
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class RegionObject extends PlaceableObject
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = 'Region';

    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = 'Regions';

    /**
     * Belongs_many_many relationship
     * @var array
     */
    private static $belongs_many_many = array(
        'Pages' => 'PlaceablePage.Regions'
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
class RegionObject_Controller extends PlaceableObject_Controller
{
    public function init()
    {
        parent::init();
    }
}
class RegionObject_Preset extends PlaceableObject_Preset
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = 'Region';

    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = 'Region Presets';

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
                    singleton('RegionObject')->SubClassPresets
                )->setEmptyString($none_string),
                DropdownField::create(
                    'Style',
                    _t('Placeable.STYLE', 'Style'),
                    singleton('RegionObject')->Styles
                )->setEmptyString($none_string)
            )
        );
        $this->extend('updateCMSFields', $fields);
        return $fields;
    }
}
