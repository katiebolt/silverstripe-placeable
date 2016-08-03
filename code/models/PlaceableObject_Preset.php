<?php
/**
 * Description
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class PlaceableObject_Preset extends DataObject
{
    /**
     * @var PlaceableObject
     */
    protected $PlaceableObject;

    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = 'Preset';

    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = 'Presets';

    /**
     * Database fields
     * @var array
     */
    private static $db = array(
        'Title' => 'Text',
        'Style' => 'Text',
        'Instance' => 'enum("shared, individual", "individual")'
    );

    /**
     * Has_many relationship
     * @var array
     */
    private static $has_many = array(
        'Objects' => 'PlaceableObject',
    );

    /**
     * Defines summary fields commonly used in table columns
     * as a quick overview of the data for this dataobject
     * @var array
     */
    private static $summary_fields = array(
        'Title' => 'Title',
        'ObjectClassName' => 'Type',
        'Style'=> 'Style',
        'Instance' => 'Instance'
    );

    /**
     * Sets main(primary) placeable object.
     * Page types must have one main object set.
     *
     * @var boolean
     */
    protected $main = false;

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = $this->scaffoldFormFields(
            array(
                // Don't allow has_many/many_many relationship editing before the record is first saved
                'includeRelations' => ($this->ID > 0),
                'tabbed' => true,
                'ajaxSafe' => true
            )
        );
        $none_string = _t('Placeable.NONE', 'None');
        $fields->addFieldsToTab(
            'Root.Main',
            array(
                TextField::create(
                    'Title',
                    _t('Placeable.TITLE', 'Title')
                ),
                DropdownField::create(
                    'Instance',
                    'Instance',
                    singleton('PlaceableObject_Preset')->dbObject('Instance')->enumValues()
                )
            )
        );
        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    /**
     * Creates computer friendly name based on the title
     * @return string
     */
    public function getType()
    {
        return str_replace(' ','',ucwords(strtolower($this->Title)));
    }

    /**
     * Checks if current class is main
     * @return boolean
     */
    public function getIsMain()
    {
        return $this->main;
    }

    /**
     * Get classname of PlaceableObject
     * @return string
     */
    public function getObjectClassName()
    {
        return str_ireplace('_preset','',$this->ClassName);
    }

    /**
     * Creating Permissions
     * @return boolean
     */
    public function canCreate($member = null)
    {
        return Permission::check('EDIT_PLACEMENTS', 'any', $member);
    }

    /**
     * Editing Permissions
     * @return boolean
     */
    public function canEdit($member = null)
    {
        return Permission::check('EDIT_PLACEMENTS', 'any', $member);
    }

    /**
     * Deleting Permissions
     * @return boolean
     */
    public function canDelete($member = null)
    {
        return Permission::check('EDIT_PLACEMENTS', 'any', $member);
    }

    /**
     * Viewing Permissions
     * @return boolean
     */
    public function canView($member = null)
    {
        return Permission::check('EDIT_PLACEMENTS', 'any', $member);
    }
}
