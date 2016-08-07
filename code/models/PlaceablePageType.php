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
        'Title' => 'text',
        'Description' => 'text',
        'HasMain' => 'boolean'
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
        'Sections' => 'SectionObject_Preset'
    );

    /**
     * {@inheritdoc }
     * @var array
     */
    private static $many_many_extraFields = array(
        'Sections' => array(
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
                TextField::create('Title'),
                TextField::create('Description'),
                UploadField::create('Icon')
            )
        );

        $fields->addFieldToTab(
            'Root.Pages',
            GridField::create(
                'Pages',
                'Pages',
                $this->Pages()
            )
        );

        $fields->addFieldToTab(
            'Root.Sections',
            GridField::create(
                'Sections',
                'Sections',
                $this->Sections(),
                GridfieldHelper::MultiClass(
                    $this->Sections(),
                    singleton('SectionObject')->SubClassPresets
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
        foreach ($this->Sections() as $Section) {
            if ($Section->IsMain) {
                $this->HasMain = true;
                continue;
            }
            if ($Section->Blocks()->exists() && $Section->Blocks()) {
                foreach ($Section->Blocks() as $Block) {
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
