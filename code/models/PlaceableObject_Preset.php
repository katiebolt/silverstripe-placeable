<?php
/**
 * Creates a preset model to define placeableobjects
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class PlaceableObject_Preset extends DataObject
{
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
        'singular_name' => 'Type',
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
     * Stores an class instance of PlaceableObject
     *
     * @var static The singleton instance
     */
    protected $placeableobject = null;

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
        $none_string = _t('PlaceableObject_Preset.NONE', 'None');
        $fields->addFieldsToTab(
            'Root.Main',
            array(
                TextField::create(
                    'Title',
                    _t('PlaceableObject_Preset.TITLE', 'Title')
                ),
                DropdownField::create(
                    'Instance',
                    _t('PlaceableObject_Preset.INSTANCE', 'Instance'),
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
     * Gets a class instance of PlaceableObject
     * @return string
     */
    public function getPlaceableObject()
    {
        if ($this->placeableobject) {
            return $this->placeableobject;
        }
        $instance = Injector::inst()->get($this->ObjectClassName);
        $this->placeableobject = $instance;
        return $instance;
    }

    /**
     * Get the user friendly singular name of this preset.
     *
     * @return string
     */
    public function singular_name() {
        if ($this->PlaceableObject->i18n_singular_name()) {
            return $this->PlaceableObject->i18n_singular_name();
        } else {
            return parent::singular_name();
        }
    }

    /**
     * Get the user friendly plural name of this preset
     *
     * @return string
     */
    public function plural_name() {
        if ($this->PlaceableObject->i18n_plural_name()) {
            return $this->PlaceableObject->i18n_plural_name();
        } else {
            return parent::plural_name();
        }
    }

    /**
     * Creating Permissions
     * @return boolean
     */
    public function canCreate($member = null)
    {
        return Permission::check('CMSACCESSPresetManagerAdmin', 'any', $member);
    }

    /**
     * Editing Permissions
     * @return boolean
     */
    public function canEdit($member = null)
    {
        return Permission::check('CMSACCESSPresetManagerAdmin', 'any', $member);
    }

    /**
     * Deleting Permissions
     * @return boolean
     */
    public function canDelete($member = null)
    {
        return Permission::check('CMSACCESSPresetManagerAdmin', 'any', $member);
    }

    /**
     * Viewing Permissions
     * @return boolean
     */
    public function canView($member = null)
    {
        return Permission::check('CMSACCESSPresetManagerAdmin', 'any', $member);
    }
}
