<?php
/**
 * Description
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class PlaceableObject_Controller extends Controller
{
    /**
     * @var $PlaceableObject
     */
    protected $PlaceableObject;

    /*
     * @param Section $PlaceableObject
     */
     public function __contruct($PlaceableObject = null)
     {
         if ($PlaceableObject) {
             $this->PlaceableObject = $PlaceableObject;
             $this->failover = $PlaceableObject;
         }
         $this->CurrentPage = Controller::curr();
         parent::__contruct();
     }

     public function index()
     {
         return;
     }

     /**
     * @param string $action
     *
     * @return string
     */
    public function Link($action = null)
    {
        $id = ($this->PlaceableObject) ? $this->PlaceableObject->ID : null;
        $segment = Controller::join_links('PlaceableObject', $id, $action);
        if ($page = Director::get_current_page()) {
            return $page->Link($segment);
        }
        return Controller::curr()->Link($segment);
    }

    /**
     * Access current page scope from PlaceableObject templates with $CurrentPage
     *
     * @return Controller
     */
    public function getCurrentPage()
    {
        return Controller::curr();
    }

    /**
     * @return Placement
     */
    public function getPlacement()
    {
     return $this->PlaceableObject;
    }
}
