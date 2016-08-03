<?php
/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class LeftAndMainPlaceable extends Extension
{
    public function augmentNewSiteTreeItem(&$item)
    {
        if (isset($_POST['PlaceablePageTypeID'])) {
            $item->PageTypeID = $_POST['PlaceablePageTypeID'];
        }
    }
}
