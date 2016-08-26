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
     * @var $placeableobject
     */
    protected $placeableobject;

    /*
     * @param dataobject $placeableobject
     */
    public function __construct($placeableobject = null)
    {
        if ($placeableobject) {
            $this->placeableobject = $placeableobject;
            $this->failover = $placeableobject;
        }
        parent::__construct();
    }

     public function index()
     {
         return;
     }

     public function init() {
     }

     /**
     * @param string $action
     *
     * @return string
     */
    public function Link($action = null)
    {
        $id = ($this->placeableobject) ? $this->placeableobject->ID : null;
        $segment = Controller::join_links('placement', $id, $action);
        if ($page = Director::get_current_page()) {
            return $page->Link($segment);
        }
        return Controller::curr()->Link($segment);
    }

    /**
     * Access current page scope from placeableobject templates with $CurrentPage
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
    public function getPlaceableObject()
    {
     return $this->placeableobject;
    }
}
