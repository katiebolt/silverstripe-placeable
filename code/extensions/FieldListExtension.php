<?php
/**
 * Adds push methods to help add multiple fields to a fieldlist
 *
 * @package silverstripe
 * @subpackage mysite
 */
class FieldListExtension extends Extension
{
    /**
    * Inserts a fields into a FieldList before a particular field if specified
    *
    * @param array $fields An array of {@link FormField} objects.
    * @param FormField $item The form field to insert
    */
    public function addFields($fields, $insertBefore = null)
    {
        $this->addFieldsBefore($fields, $insertBefore = null);
    }

    /**
    * Inserts a fields into a FieldList before a particular field if specified
    *
    * @param array $fields An array of {@link FormField} objects.
    * @param FormField $item The form field to insert
    */
    public function addFieldsBefore($fields, $insertBefore = null)
    {
        // $this->owner->flushFieldsCache();

        // Add the fields to the list
        foreach ($fields as $field) {
            if ($insertBefore) {
                $this->owner->insertBefore($insertBefore, $field);
            } elseif (($name = $field->getName()) && $this->owner->fieldByName($name)) {
                // It exists, so we need to replace the old one
                $this->owner->replaceField($field->getName(), $field);
            } else {
                $this->owner->push($field);
            }
        }
    }

    /**
    * Inserts a fields into a FieldList after a particular field if specified.
    *
    * @param array $fields An array of {@link FormField} objects.
    * @param FormField $item The form field to insert
    */
    public function addFieldsAfter($fields, $insertAfter = null)
    {
        // $this->owner->flushFieldsCache();

        // Add the fields to the list
        foreach ($fields as $field) {
            if ($insertBefore) {
                $this->owner->insertAfter($insertBefore, $field);
            } elseif (($name = $field->getName()) && $this->owner->fieldByName($name)) {
                // It exists, so we need to replace the old one
                $this->owner->replaceField($field->getName(), $field);
            } else {
                $this->owner->push($field);
            }
        }
    }
}
