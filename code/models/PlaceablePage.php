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
    private static $singular_name = 'Preset Page';

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
        'Sections' => 'SectionObject'
    );

    /**
     * {@inheritdoc }
     * @var array
     */
    private static $many_many_extraFields = array(
        'Sections' => array(
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
                        '<p class="message warning">'._t('Placeable.DEVNOTE', 'This area is only visible during development and is intended to help debug.').'</p>'
                    ),
                    GridField::create(
                        'Sections',
                        _t('Placeable.SECTIONS', 'Sections'),
                        $this->Sections(),
                        GridFieldConfig_RecordEditor::create()
                    )
                )
            );
        }

        foreach ($this->Sections() as $Section) {
            $fields->addFieldToTab(
                "Root.{$Section->Type}",
                LiteralField::create(
                    "Instance{$Section->Type}",
                    "{$Section->Instance}"
                )
            );
            foreach ($Section->getCMSPageFields() as $Field) {
                $Field->name = "$Section->ID[$Field->name]";
                $fields->addFieldToTab(
                    "Root.{$Section->Type}",
                    $Field
                );
            }
            // Debug::dump($Section->ClassName);
            // Debug::dump($Section->Blocks());

            if ($Section->Blocks()->exists()) {
                foreach ($Section->Blocks() as $Block) {
                    foreach ($Block->getCMSPageFields() as $Field) {
                        $Field->name = "$Block->ID[$Field->name]";
                        $fields->addFieldToTab(
                            "Root.{$Section->Type}",
                            $Field
                        );
                    }
                }
            }
        }
        return $fields;
    }

    /**
     * Settings Tab Fields
     * @return FieldList
     */
    public function getSettingsFields()
    {
        $fields = parent::getSettingsFields();
        $fields->addFieldToTab(
            'Root',
            DropdownField::create(
                'PageTypeID',
                'Placeable page type',
                PlaceablePageType::get()->map('ID','Title'),
                $this->PageType()
            ),
            'ClassName'
        );
        return $fields;
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
            $Section = $this->Sections()->find('PresetID', $Preset->ID);
            // Find existing dataobject if relationship doesn't exist and shares it instance
            if (!$Section && $Preset->Instance == 'shared') {
                $Section = DataObject::get_one(
                    $ClassName,
                    array(
                        'PresetID' => $Preset->ID
                    )
                );
            }
            // Create new dataobject if nothing else exists
            if (!$Section) {
                $Section = $ClassName::create();
            }
            $Section->PresetID = $Preset->ID;
            $Section->write();
            $this->Sections()->add(
                $Section,
                array(
                    'Sort' => $Preset->Sort,
                    'Display' => true
                )
            );
        }
        // Hide Sections that no longer exist due to a change in page type or its settings.
        // We don't delete it just in case its reverted back.
        foreach ($this->Sections() as $Section) {
            $Preset = $this->Presets->find('ID', $Section->PresetID);
            if (!$Preset) {
                $this->Sections()->add(
                    $Section,
                    array(
                        'Display' => false
                    )
                );
            }
        }
    }

    /**
     * Gets preset sections from page type
     *
     * @return arrayList
     **/
    public function getPresets()
    {
        $Presets = new arrayList();
        foreach ($this->PageType()->Sections() as $Section) {
            $Presets->Push($Section);
        }
        return $Presets;
    }

    /**
     * Gets sections for display
     *
     * @return ManyManyList
     **/
    public function getPlacements()
    {
        return $this->Sections()->filter(
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
     * Handles section attached to a page
     * Assumes URLs in the following format: <URLSegment>/placement/<section-id>.
     *
     * @return RequestHandler
     */
    public function placement()
    {
        if ($ID = $this->getRequest()->param('ID')) {
            $sections = $this->data()->Sections();
            if ($section = $sections->find(array('ID' => $ID))) {
                if ($action = $this->getRequest()->param('ACTION')) {
                    return $section->getController()->$action();
                }
            }
        }
    }
}
