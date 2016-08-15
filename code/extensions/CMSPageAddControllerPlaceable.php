<?php
/**
 * Adds page preset options to the add page controller
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class CMSPageAddControllerPlaceable extends Extension
{
    /**
     * Update add page fields
     * @return FieldList
     */
    public function updatePageOptions(FieldList $fields)
    {
        $pageTypeField = $fields->fieldByName('PageType');
        $pageTypes = $pageTypeField->getSource();
        $placeablePageTypes = PlaceablePageType::get();
        // unset($pageTypes['Page'],$pageTypes['PlaceablePage']);
        unset($pageTypes['Page']);
        $pageTypeField->setSource($pageTypes);
        $types = array();
        foreach ($placeablePageTypes as $type) {
            $types[$type->ID] = "<span class='page-icon'></span><strong class='title'>$type->Title</strong><span class='description'>$type->Description</span>";
        }
        $fields->push(
                OptionsetField::create(
                    'PlaceablePageTypeID',
                    _t('Placeable.CHOOSEPRESET', 'Choose preset'),
                    $types
                )
        );
        return $fields;
    }
}
