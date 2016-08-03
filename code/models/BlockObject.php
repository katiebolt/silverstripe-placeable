<?php
/**
 * Description
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class BlockObject extends PlaceableObject
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = 'Block';

    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = 'Blocks';

    /**
     * Define the default values for all the $db fields
     * @var array
     */
    private static $defaults = array(
        'Title' => 'Block'
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
class BlockObject_Controller extends PlaceableObject_Controller
{
    public function init() {
        parent::init();
    }
}
class BlockObject_Preset extends PlaceableObject_Preset
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = 'Block';

    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = 'Block Presets';

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
                    singleton('BlockObject')->SubClassPresets
                )->setEmptyString($none_string),
                DropdownField::create(
                    'Style',
                    _t('Placeable.STYLE', 'Style'),
                    singleton('BlockObject')->Styles
                )->setEmptyString($none_string)
            )
        );
        $this->extend('updateCMSFields', $fields);
        return $fields;
    }
}
