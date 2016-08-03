<?php
/**
 * Description
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class PlacementsAdmin extends ModelAdmin
{
    /**
     * Managed data objects for CMS
     * @var array
     */
    private static $managed_models = array(
        'PlaceablePageType',
        'SectionObject_Preset',
        'BlockObject_Preset'
    );

    /**
     * URL Path for CMS
     * @var string
     */
    private static $url_segment = 'placeable';

    /**
     * Menu title for Left and Main CMS
     * @var string
     */
    private static $menu_title = 'Placeable';
}
