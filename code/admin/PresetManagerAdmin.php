<?php
/**
 * Description
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class PresetManagerAdmin extends ModelAdmin
{
    /**
     * Managed data objects for CMS
     * @var array
     */
    private static $managed_models = array(
        'PlaceablePageType',
        'RegionObject_Preset',
        'BlockObject_Preset'
    );

    /**
     * Remove all data objects from being imported
     * @var array
     */
    private static $model_importers = array();

    /**
     * URL Path for CMS
     * @var string
     */
    private static $url_segment = 'presetmanager';

    /**
     * Menu title for Left and Main CMS
     * @var string
     */
    private static $menu_title = 'Preset manager';

    /**
     * @param Int $id
     * @param FieldList $fields
     * @return Form
     */
    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);
        $form->Fields()
            ->fieldByName($this->sanitiseClassName($this->modelClass))
            ->getConfig()
            ->removeComponentsByType('GridFieldExportButton')
            ->removeComponentsByType('GridFieldPrintButton');
        return $form;
    }
}
