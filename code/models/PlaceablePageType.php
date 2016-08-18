<?php
/**
 * Description
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class PlaceablePageType extends DataObject
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = 'Page Type';

    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = 'Page Types';

    /**
     * Database fields
     * @var array
     */
    private static $db = array(
        'Title' => 'Varchar(17)',
        'Description' => 'Text',
        'HasMain' => 'Boolean'
    );

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = array(
        'Icon' => 'Image'
    );

    /**
     * Has_many relationship
     * @var array
     */
    private static $has_many = array(
        'Pages' => 'PlaceablePage',
    );

    /**
     * Many_many relationship
     * @var array
     */
    private static $many_many = array(
        'Regions' => 'RegionObject_Preset'
    );

    /**
     * {@inheritdoc }
     * @var array
     */
    private static $many_many_extraFields = array(
        'Regions' => array(
            'Sort' => 'Int'
        )
    );

    /**
     * Defines summary fields commonly used in table columns
     * as a quick overview of the data for this dataobject
     * @var array
     */
    private static $summary_fields = array(
        'Title' => 'Title',
        'Description' => 'Title',
        'HasMain.Nice' => 'Main is set'
    );

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

        $fields->addFieldsToTab(
            'Root.Main',
            array(
                TextField::create(
                    'Title',
                    _t('PlaceablePageType.TITLE', 'Title')
                )->setMaxLength(17),
                TextField::create(
                    'Description',
                    _t('PlaceablePageType.DECRIPTION', 'Description')
                ),
                UploadField::create(
                    'Icon',
                    _t('PlaceablePageType.ICON', 'Icon')
                )
            )
        );

        $fields->addFieldToTab(
            'Root.Pages',
            GridField::create(
                'Pages',
                _t('PlaceablePageType.PAGES', 'Pages'),
                $this->Pages()
            )
        );

        $fields->addFieldToTab(
            'Root.Regions',
            GridField::create(
                'Regions',
                _t('PlaceablePageType.SECTIONS', 'Regions'),
                $this->Regions(),
                GridFieldConfig_MultiClass::create(
                    $this->Regions(),
                    singleton('RegionObject')->SubClassPresets
                )
            )
        );
        return $fields;
    }

    /**
     * Event handler called before writing to the database.
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        // Check that PlaceableObject with is_main has been set.
        $this->HasMain = false;
        foreach ($this->Regions() as $Region) {
            if ($Region->IsMain) {
                $this->HasMain = true;
                continue;
            }
            if ($Region->Blocks()->exists() && $Region->Blocks()) {
                foreach ($Region->Blocks() as $Block) {
                    if ($Block->IsMain) {
                        $this->HasMain = true;
                        continue;
                    }
                }
            }
        }
    }

    /**
     * Event handler called after writing to the database.
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();
        // Update all pages of this page type.
        foreach ($this->Pages() as $Page){
            $Page->forceChange()->write();
        }
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
