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
        foreach ($pageTypeField->getSource() as $classKey => $classValue) {
            if ($classKey == 'PlaceablePage') {
                foreach (PlaceablePageType::get() as $type) {
                    $html = sprintf('<span class="page-icon class-placeable-%d"></span><strong class="title">%s</strong><span class="description">%s</span>',
                        $type->ID,
                        $type->Title,
                        $type->Description
                    );
                    $PageTypes["$classKey-$type->ID"] = DBField::create_field('HTMLText', $html);
                }
            } else {
                $PageTypes["$classKey-0"] = $classValue;
            }
        }

        $numericLabelTmpl = '<span class="step-label"><span class="flyout">%d</span><span class="arrow"></span><span class="title">%s</span></span>';

        $fields->removeByName(
            array(
                'PageType',
                'RestrictedNote'
            )
        );
        $fields->addFields(
            array(
                OptionsetField::create(
                    'PageTypeFake',
                    sprintf($numericLabelTmpl, 2, _t('CMSMain.ChoosePageType', 'Choose page type')),
                    $PageTypes
                ),
                HiddenField::create('PageType','PageType'),
                HiddenField::create('PageTypeID','PageTypeID')
            )
        );
        return $fields;
    }
}
