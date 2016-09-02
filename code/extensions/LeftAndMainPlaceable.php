<?php
/**
 * Assigns new placeable pages to a page type
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class LeftAndMainPlaceable extends Extension
{
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
