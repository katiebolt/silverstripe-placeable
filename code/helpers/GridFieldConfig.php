<?php
/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class GridFieldConfig_MultiClass extends GridFieldConfig_RelationEditor
{
    public function __construct($relationship, $classes)
    {
        parent::__construct();
        $this->removeComponentsByType('GridFieldAddNewButton');
        $this->addComponent(new GridFieldAddNewMultiClass());

        if ($relationship->Count() > 1) {
            $this->addComponent(new GridFieldOrderableRows());
        }

        $this->getComponentByType('GridFieldAddNewMultiClass')
            ->setClasses($classes);
    }
}
