<?php
/**
 * Description
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class GridFieldConfig_MultiClass extends GridFieldConfig_RelationEditor
{
    public function __construct($relationship, $classes)
    {
        parent::__construct();
        $this->removeComponentsByType('GridFieldAddNewButton');
        $this->addComponent(new GridFieldAddNewMultiClass());
        $this->getComponentByType('GridFieldAddNewMultiClass')
            ->setClasses($classes);
        if ($relationship->Count() > 1) {
            $this->addComponent(new GridFieldOrderableRows());
        }
    }
}

class GridFieldConfig_MultiClassRecord extends GridFieldConfig_RecordEditor
{
    public function __construct($relationship, $classes)
    {
        parent::__construct();
        $this->removeComponentsByType('GridFieldAddNewButton');
        $this->addComponent(new GridFieldAddNewMultiClass());
        $this->getComponentByType('GridFieldAddNewMultiClass')
            ->setClasses($classes);
        if ($relationship->Count() > 1) {
            $this->addComponent(new GridFieldOrderableRows());
        }
    }
}
