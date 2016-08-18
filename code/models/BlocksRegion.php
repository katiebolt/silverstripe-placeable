<?php
/**
 * Region that lists blocks
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class BlocksRegion extends RegionObject
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = 'Blocks';

    /**
     * Define the default values for all the $db fields
     * @var array
     */
    private static $defaults = array(
        'Title' => 'Blocks'
    );

    /**
     * Many_many relationship
     * @var array
     */
    private static $many_many = array(
        'Blocks' => 'BlockObject'
    );

    /**
     * {@inheritdoc }
     * @var array
     */
    private static $many_many_extraFields = array(
        'Blocks' => array(
            'Sort' => 'Int'
        )
    );

    /**
     * CMS Page Fields
     * @return FieldList
     */
    public function getCMSPageFields()
    {
        $fields = parent::getCMSPageFields();
        $this->extend('updateCMSPageFields', $fields);
        return $fields;
    }

    /**
     * Event handler called after writing to the database.
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();
        foreach ($this->Preset()->Blocks() as $Preset) {
            $ClassName = $Preset->ObjectClassName;
            // Find existing relationship
            $Block = $this->Blocks()->find('PresetID', $Preset->ID);
            // Find existing dataobject if relationship doesn't exist and shares it instance
            if (!$Block && $Preset->Instance == 'shared') {
                $Block = DataObject::get_one(
                    $ClassName,
                    array(
                        'PresetID' => $Preset->ID
                    )
                );
            }
            // Create new dataobject if nothing else exists
            if (!$Block) {
                $Block = $ClassName::create();
            }
            $Block->PresetID = $Preset->ID;
            $Block->forceChange()->write();
            $this->Blocks()->add(
                $Block,
                array(
                    'Sort' => $Preset->Sort,
                    'Display' => true
                )
            );
            // $this->write();
        }
    }
}
class BlocksRegion_Controller extends RegionObject_Controller
{
    public function init() {
        parent::init();
    }
}
class BlocksRegion_Preset extends RegionObject_Preset
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = 'Block Region Preset';

    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = 'Block Region Presets';

    /**
     * Many_many relationship
     * @var array
     */
    private static $many_many = array(
        'Blocks' => 'BlockObject_Preset'
    );

    /**
     * {@inheritdoc }
     * @var array
     */
    private static $many_many_extraFields = array(
        'Blocks' => array(
            'Sort' => 'Int'
        )
    );

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab(
            'Root.Blocks',
            GridField::create(
                'Blocks',
                'Blocks',
                $this->Blocks(),
                GridFieldConfig_MultiClass::create(
                    $this->Blocks(),
                    singleton('BlockObject')->SubClassPresets
                )
            )
        );
        $this->extend('updateCMSFields', $fields);
        return $fields;
    }
}
