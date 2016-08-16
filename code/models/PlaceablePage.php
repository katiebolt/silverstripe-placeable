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
    private static $singular_name = 'Placeable Page';

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
        foreach ($this->PageFields as $Region) {
            $fields->addFieldToTab(
                "Root.{$Region->Type}",
                HeaderField::create(
                    "header[$Region->Type]",
                    $Region->Title
                )
            );
            foreach ($Region->Fields as $Field) {
                $fields->addFieldToTab(
                    "Root.{$Region->Type}",
                    $Field
                );
            }
            foreach ($Region->Blocks as $Block) {
                $blockfields = CompositeField::create(
                    HeaderField::create(
                        "header[$Block->Type]",
                        $Block->Title
                    )
                );
                foreach ($Block->Fields as $Field) {
                    $blockfields->push($Field);
                }
                $fields->addFieldToTab(
                    "Root.{$Region->Type}",
                    $blockfields
                );
            }
        }
        return $fields;
    }

    public function getPageFields()
    {
        $allfields = arrayList::create();
        foreach ($this->Regions()->sort('Sort ASC') as $Region) {
            $newRegionFields = arrayList::create();
            $origRegionFields = $Region->getCMSPageFields();
            if (!$origRegionFields->count() && !$Region->Blocks()->count()) {
                continue;
            }
            foreach ($origRegionFields as $Field) {
                $Field->value = $Region->{$Field->name};
                $Field->original_name = $Field->name;
                $Field->name = "{$Field->name}_{$Region->ID}";
                $newRegionFields->push($Field);
            }
            $newRegionBlocks = arrayList::create();
            if ($Region->hasMethod('Blocks') && $Region->Blocks()->exists()) {
                foreach ($Region->Blocks()->sort('Sort ASC') as $Block) {
                    $newBlockFields = arrayList::create();
                    $origBlockFields = $Block->getCMSPageFields();
                    if (!$origBlockFields->count()) {
                        continue;
                    }
                    foreach ($origBlockFields as $Field) {
                        $Field->value = $Block->{$Field->name};
                        $Field->original_name = $Field->name;
                        $Field->name = "{$Field->name}_{$Block->ID}";
                        $newBlockFields->push($Field);
                    }
                    $newRegionBlocks->push(
                        arrayData::create(
                            array(
                                'DataObject' => $Block,
                                'Type' => $Block->Preset()->Type,
                                'Title' => $Block->Preset()->Title,
                                'Fields' => $newBlockFields
                            )
                        )
                    );
                }
            }
            $allfields->push(
                arrayData::create(
                    array(
                        'DataObject' => $Region,
                        'Type' => $Region->Preset()->Type,
                        'Title' => $Region->Preset()->Title,
                        'Fields' => $newRegionFields,
                        'Blocks' => $newRegionBlocks
                    )
                )
            );
        }
        return $allfields;
    }

    /**
     * Event handler called after writing to the database.
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();
        // build relationships
        foreach ($this->Presets as $Preset) {
            $ClassName = $Preset->ObjectClassName;
            // Find existing relationship
            $Region = $this->Regions()->find('PresetID', $Preset->ID);
            // Find existing dataobject if relationship doesn't exist and shares it instance
            if (!$Region && $Preset->Instance == 'shared') {
                $Region = DataObject::get_one(
                    $ClassName,
                    array(
                        'PresetID' => $Preset->ID
                    )
                );
            }
            // Create new dataobject if nothing else exists
            if (!$Region) {
                $Region = $ClassName::create();
            }
            $Region->PresetID = $Preset->ID;
            $Region->forceChange()->write();
            $this->Regions()->add(
                $Region,
                array(
                    'Sort' => $Preset->Sort,
                    'Display' => true
                )
            );
        }
        // Hide Regions that no longer exist due to a change in page type or its settings.
        // We don't delete it just in case its reverted back.
        foreach ($this->Regions() as $Region) {
            $Preset = $this->Presets->find('ID', $Region->PresetID);
            if (!$Preset) {
                $this->Regions()->add(
                    $Region,
                    array(
                        'Display' => false
                    )
                );
            }
        }
        // Save fields to related regions and blocks
        foreach ($this->PageFields as $Region) {
            $RegionObject = $Region->DataObject;
            foreach ($Region->Fields as $Field) {
                if (isset($_POST["$Field->name"])) {
                    $RegionObject->{$Field->original_name} = $_POST["$Field->name"];
                }
            }
            $RegionObject->forceChange()->write();
            foreach ($Region->Blocks as $Block) {
                $BlockObject = $Block->DataObject;
                foreach ($Block->Fields as $Field) {
                    if (isset($_POST["$Field->name"])) {
                        $BlockObject->{$Field->original_name} = $_POST["$Field->name"];
                    }
                }
                $BlockObject->forceChange()->write();
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
        $Presets = arrayList::create();
        foreach ($this->PageType()->Regions()->sort('Sort ASC') as $Region) {
            $Presets->Push($Region);
        }
        return $Presets;
    }

    /**
     * Gets regions for display
     *
     * @return ManyManyList
     **/
    public function getPlacements()
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
     * Handles region attached to a page
     * Assumes URLs in the following format: <URLSegment>/placement/<region-id>.
     *
     * @return RequestHandler
     */
    public function placement()
    {
        if ($ID = $this->getRequest()->param('ID')) {
            $regions = $this->data()->Regions();
            if ($region = $regions->find(array('ID' => $ID))) {
                if ($action = $this->getRequest()->param('ACTION')) {
                    return $region->getController()->$action();
                }
            }
        }
    }
}
