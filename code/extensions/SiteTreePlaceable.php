<?php
/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class SiteTreePlaceable extends DataExtension
{
    /**
     * Database fields
     * @var array
     */
    private static $db = array(
         // stores text version of page type so we can default to it in the page type dropdown in settings
        'PageTypeFake' => 'Text'
    );
    
    /**
     * Update Settings Tab Fields
     * @return FieldList
     */
    public function updateSettingsFields(FieldList $fields)
    {
        $fields->removeByName('ClassName');
        $PageTypes = array();
        foreach ($this->owner->ClassDropdown as $classKey => $classValue) {
            if ($classKey == 'PlaceablePage') {
                foreach (PlaceablePageType::get()->map('ID','Title') as $placeableKey => $placeableValue) {
                    $PageTypes["$classKey-$placeableKey"] = $placeableValue;
                }
            } else {
                $PageTypes["$classKey-0"] = $classValue;
            }

        }
        $fields->addFieldsToTab(
            'Root.Settings',
            array(
                DropdownField::create(
                    'PageTypeFake',
                    _t('PlaceablePage.PAGETYPE', 'Page type'),
                    $PageTypes
                ),
                HiddenField::create('ClassName','ClassName'),
                HiddenField::create('PageTypeID','PageTypeID'),

            ),
            'ParentType'
        );
        return $fields;
    }
}
