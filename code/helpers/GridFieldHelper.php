<?php
/**
 *
 */
class GridfieldHelper
{

    public static function MultiClass($Relationship, $Classes)
    {
        $Grid = GridFieldConfig_RelationEditor::create()
            ->removeComponentsByType('GridFieldAddNewButton')
            ->addComponent(new GridFieldAddNewMultiClass());

        if ($Relationship->Count() > 1) {
            $Grid->addComponent(new GridFieldOrderableRows());
        }

        $Grid->getComponentByType('GridFieldAddNewMultiClass')
            ->setClasses($Classes);

        return $Grid;
    }
}
