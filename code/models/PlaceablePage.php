<?php
/**
 * Description
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class PlaceablePage extends Page
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = 'Page';

    /**
     * @var Boolean
     */
    protected $updatepresets = false;

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = array(
        'PageType' => 'PlaceablePageType',
    );

    /**
     * Many_many relationship
     * @var array
     */
    private static $many_many = array(
        'Regions' => 'RegionObject'
    );

    /**
     * {@inheritdoc }
     * @var array
     */
    private static $many_many_extraFields = array(
        'Regions' => array(
            'Sort' => 'Int',
            'Display' => 'Boolean'
        )
    );

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        if (Director::isDev()) {
            $fields->addFieldsToTab(
                'Root.Developer',
                array(
                    LiteralField::create(
                        'DeveloperNote',
                        '<p class="message warning">'._t('Placeable.DEVNOTE', 'This tab is only visible during development and is intended to help debug.').'</p>'
                    ),
                    GridField::create(
                        'Regions',
                        _t('PlaceablePage.SECTIONS', 'Regions'),
                        $this->Regions(),
                        GridFieldConfig_RecordEditor::create()
                    )
                )
            );
        }
        foreach ($this->PageFields as $region) {
            $fields->addFieldToTab(
                "Root.{$region->Type}",
                HeaderField::create(
                    "header[$region->Type]",
                    $region->Title
                )
            );
            foreach ($region->Fields as $field) {
                $fields->addFieldToTab(
                    "Root.{$region->Type}",
                    $field
                );
            }
            foreach ($region->Blocks as $Block) {
                $blockfields = CompositeField::create(
                    HeaderField::create(
                        "header[$Block->Type]",
                        $Block->Title
                    )
                );
                foreach ($Block->Fields as $field) {
                    $blockfields->push($field);
                }
                $fields->addFieldToTab(
                    "Root.{$region->Type}",
                    $blockfields
                );
            }
        }
        return $fields;
    }

    public function getPageFields()
    {
        $allfields = arrayList::create();
        foreach ($this->CurrentRegions as $region) {
            $newRegionFields = arrayList::create();
            $origRegionFields = $region->getCMSPageFields();
            if (!$origRegionFields->count() && !$region->Blocks()->count()) {
                continue;
            }
            foreach ($origRegionFields as $field) {
                $newRegionFields->push($this->BuildPageField($field, $region));
            }
            $newRegionBlocks = arrayList::create();
            if ($region->hasMethod('getCurrentBlocks') && $region->CurrentBlocks->exists()) {
                foreach ($region->CurrentBlocks as $block) {
                    $newBlockFields = arrayList::create();
                    $origBlockFields = $block->getCMSPageFields();
                    if (!$origBlockFields->count()) {
                        continue;
                    }
                    foreach ($origBlockFields as $field) {
                        $newBlockFields->push($this->BuildPageField($field, $block));
                    }
                    $newRegionBlocks->push(
                        arrayData::create(
                            array(
                                'DataObject' => $block,
                                'Type' => $block->Preset()->Type,
                                'Title' => $block->Preset()->Title,
                                'Fields' => $newBlockFields
                            )
                        )
                    );
                }
            }
            $allfields->push(
                arrayData::create(
                    array(
                        'DataObject' => $region,
                        'Type' => $region->Preset()->Type,
                        'Title' => $region->Preset()->Title,
                        'Fields' => $newRegionFields,
                        'Blocks' => $newRegionBlocks
                    )
                )
            );
        }
        return $allfields;
    }

    public function BuildPageField($field, $record)
    {
        $field->value = $record->{$field->name};
        $field->original_name = $field->name;
        $field->record = $record; // assign parent record if it accepts it.  Used in uploadfields
        $field->name = "{$field->name}_{$record->ID}";
        return $field;
    }

    /**
     * Event handler called before writing to the database.
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (!$this->ID) {
            // first write
            $this->updatepresets = true;
        }
    }

    /**
     * Event handler called after writing to the database.
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();
        if ($this->ClassName == 'PlaceablePage') {
            $this->writePresets();

            // Save fields to related regions and blocks
            foreach ($this->PageFields as $region) {
                $regionObject = $region->DataObject;
                foreach ($region->Fields as $field) {
                    if (isset($_POST["$field->name"])) {
                        $regionObject->{$field->original_name} = $_POST["$field->name"];
                    }
                }
                $regionObject->forceChange()->write();
                foreach ($region->Blocks as $block) {
                    $blockObject = $block->DataObject;
                    foreach ($block->Fields as $field) {
                        if (isset($_POST["$field->name"])) {
                            $blockObject->{$field->original_name} = $_POST["$field->name"];
                        }
                    }
                    $blockObject->forceChange()->write();
                }
            }
        }
    }

    public function writePresets($presets = null)
    {
        if (!($this->isChanged('PageTypeID') || $this->updatepresets)) {
            return $this;
        }

        if (!$presets) {
            $presets = $this->Presets;
        }
        // build relationships
        foreach ($presets as $preset) {
            $className = $preset->ObjectClassName;
            // Find existing relationship
            $region = $this->Regions()->find('PresetID', $preset->ID);
            // Find existing dataobject if relationship doesn't exist and shares it instance
            if (!$region && $preset->Instance == 'shared') {
                $region = DataObject::get_one(
                    $className,
                    array(
                        'PresetID' => $preset->ID
                    )
                );
            }
            // Create new dataobject if nothing else exists
            if (!$region) {
                $region = $className::create();
            }
            $region->PresetID = $preset->ID;
            $region->forceChange()->write();
            $this->Regions()->add(
                $region,
                array(
                    'Sort' => $preset->Sort,
                    'Display' => true
                )
            );
        }
        // Hide Regions that no longer exist due to a change in page type or its settings.
        // We don't delete it just in case its reverted back.
        foreach ($this->Regions() as $region) {
            $preset = $this->Presets->find('ID', $region->PresetID);
            if (!$preset) {
                $this->Regions()->add(
                    $region,
                    array(
                        'Display' => false
                    )
                );
            }
        }

    }

    /**
     * Gets preset regions from page type
     *
     * @return arrayList
     **/
    public function getPresets()
    {
        $presets = arrayList::create();
        foreach ($this->PageType()->Regions()->sort('Sort ASC') as $region) {
            $presets->Push($region);
        }
        return $presets;
    }

    /**
     * Gets regions for display
     *
     * @return ManyManyList
     **/
    public function getCurrentRegions()
    {
        return $this->Regions()->filter(
           array(
               'Display' => true
           )
        )->sort('Sort ASC');
    }
}
class PlaceablePage_Controller extends Page_Controller
{
    /**
     * Defines methods that can be called directly
     * @var array
     */
    private static $allowed_actions = array(
        'placement'
    );

    public function init() {
        parent::init();
    }

    /**
     * Handles PlaceableObject attached to a page
     * Assumes URLs in the following format: <URLSegment>/placement/<placeableObject-id>.
     *
     * @return RequestHandler
     */
    public function placement()
    {
        if ($ID = $this->getRequest()->param('ID')) {
            $regions = $this->Regions();
            $placeableObjects = arrayList::create();
            foreach ($regions as $region) {
                $placeableObjects->push($region);
                if ($region->hasMethod('Blocks') && $region->Blocks()->exists()) {
                    foreach ($region->Blocks() as $block) {
                        $placeableObjects->push($block);
                    }
                }
            }

            if ($placeableObjects = $placeableObjects->find('ID', $ID)) {
                if ($action = $this->getRequest()->param('ACTION')) {
                    return $placeableObjects->getController()->$action();
                }
            }
        }
    }
}
