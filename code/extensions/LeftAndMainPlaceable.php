<?php
/**
 * Assigns new placeable pages to a page type
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class LeftAndMainPlaceable extends Extension
{
    public function init() {
        $selector = '.icon.icon-16.icon-presetmanageradmin';
        $icon = 'placeable/img/icon@2x.png';
        $css = "$selector { background: transparent url('$icon') 0 0 no-repeat !important; background-size: 16px auto !important; }";
        Requirements::customCSS($css);
    }

    public function augmentNewSiteTreeItem(&$item)
    {
        if (isset($_POST['PageTypeFake'])) {
            $item->PageTypeFake = $_POST['PageTypeFake'];
        }
        if (isset($_POST['PageTypeID'])) {
            $item->PageTypeID = $_POST['PageTypeID'];
        }
    }
}
