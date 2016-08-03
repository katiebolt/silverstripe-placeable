<?php
/**
 * Description
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
        $PageTypeField = $fields->fieldByName('PageType');
        $PageTypes = $PageTypeField->getSource();
        $PlaceablePageTypes = PlaceablePageType::get();
        unset($PageTypes['Page']);
        // $PageTypes["PlaceablePage"] = "<span class='page-icon'></span><strong class='title'>Placeable page</strong><span class='description'>Placeable page</span>";
        if ($PlaceablePageTypes->Count() == 0) {
            unset($PageTypes['PlaceablePage']);
        }
        $PageTypeField->setSource($PageTypes);
        $Types = array();
        foreach ($PlaceablePageTypes as $type) {
            $Types[$type->ID] = "<span class='page-icon'></span><strong class='title'>$type->Title</strong><span class='description'>$type->Description</span>";
        }

        $numericLabelTmpl = '<span class="step-label"><span class="flyout">%d</span><span class="arrow"></span><span class="title">%s</span></span>';

        $fields->push(
            DisplayLogicWrapper::create(
                OptionsetField::create(
                    'PlaceablePageTypeID',
                    sprintf($numericLabelTmpl, 3, _t('Placeable.CHOOSEPRESET', 'Choose preset')),
                    $Types
                )
            )->DisplayIf('PageType')->isEqualTo('PlaceablePage')->end()
        );
        return $fields;
    }
}
