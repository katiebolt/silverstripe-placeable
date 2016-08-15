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
     * Menu icon for Left and Main CMS
     * @var string
     */
    private static $menu_icon = 'silverstripe-placeable/img/icon.png';

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
     * URL Path for CMS
     * @var string
     */
    private static $url_segment = 'presetmanager';

    /**
     * Menu title for Left and Main CMS
     * @var string
     */
    private static $menu_title = 'Preset manager';
}
