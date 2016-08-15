<?php
/**
 * A abstract class.  This is extended upon by RegionObject and BlockObject
 *
 * @package silverstripe
 * @subpackage silverstripe-placeable
 */
class PlaceableObject extends DataObject
{
    /**
     * @var PlaceableObject_Controller
     */
    protected $controller;

    /**
     * Database fields
     * @var array
     */
    private static $db = array(
        'Title' => 'Text',
        'UrlSegment' => 'Text'
    );

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = array(
        'Preset' => 'PlaceableObject_Preset',
    );

    /**
     * Cast
     * @var array
     */
    private static $casting = array(
        'Anchor' => 'Text',
        'Classes' => 'Text'
    );

    /**
     * Defines summary fields used in grid fields
     * @var array
     */
    private static $summary_fields = array(
        'Preset.Title' => 'Title',
        'ClassName.Nice' => 'Type'
    );

    /**
     * Define extensions
     * @var array
     */
    private static $extensions = array(
        'Versioned("Stage","Live")'
    );

    /**
     * CMS Page Fields
     * @return FieldList
     */
    public function getCMSPageFields()
    {
        $fields = FieldList::create(
            TextField::create(
                'Title',
                _t('PlaceableObject.TITLE', 'Title')
            )
        );
        $this->extend('updateCMSPageFields', $fields);
        return $fields;
    }

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = $this->scaffoldFormFields(
            array(
                // Don't allow has_many/many_many relationship editing before the record is first saved
                'includeRelations' => ($this->ID > 0),
                'tabbed' => true,
                'ajaxSafe' => true
            )
        );
        $fields->removeByName('PresetID');
        $fields->addFieldToTab(
            'Root.Main',
            ReadonlyField::create(
                'PresetName',
                _t('PlaceableObject.PRESET', 'Preset')
            )->setValue($this->Preset()->Title)
        );
        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    /**
     * Renders HTML
     *
     * @return string
     **/
    public function getLayout()
    {
        $page = $this->CurrentPage;
        $customTemplate = ($this->Style ? '_'.$this->Style : '');
        $pageType = ($page->ClassName ? '_'.$page->ClassName : '');
        $className = $this->ClassName;
        $templates = array(
            $className.$pageType.$customTemplate,
            $className.$customTemplate,
            $className.$pageType,
            $className,
            'DefaultPlacement'
        );
        $this->extend('updateLayout', $templates);
        return $this->renderWith($templates, $this->getController());
    }

    /**
     * Renders HTML comment used to help debug issues during development
     *
     * @return string
     **/
    public function getDebugInfo()
    {
        if (Director::isLive()) {
            return false;
        }
        $Debug = array(
            'ID' => $this->ID,
            'Preset' => $this->Preset()->Title,
            'ClassName' => $this->ClassName,
            'Style' => $this->Style
        );
        $this->extend('updateDebugInfo', $Debug);
        $Debug = implode(";\n", array_map(
            function ($value, $key) { return "$key: $value"; },
            $Debug,
            array_keys($Debug)
        ));
        return "\n<!--\n{$Debug}\n-->\n";
    }

    /**
     * Creates computer friendly name based on the title
     * @return string
     */
    public function getType()
    {
        return str_replace(' ','',ucwords(strtolower($this->Preset()->Title)));
    }

    /**
     * Generates an anchor segment
     *
     * @return string
     */
    public function getAnchor()
    {
        if ($this->UrlSegment) {
            return Convert::raw2url($this->UrlSegment);
        }
        if ($this->Title) {
            return Convert::raw2url($this->Title);
        }
        return false;
    }

    /**
     * Generates an ID attribute for templates.
     *
     * @return string
     */
    public function getAnchorAttr()
    {
        if ($this->Anchor) {
            return " id='$this->Anchor'";
        }
        return false;
    }

    /**
     * Generates css classes
     *
     * @return string
     */
    public function getClasses()
    {
        $classes = array();
        $classes[] = ($this->Style ? $this->Style.'-'.$this->ClassName : $this->ClassName);
        foreach ($classes as $key => $value) {
            $classes[$key] = Convert::raw2url($value);
        }
        $this->extend('updateClasses', $classes);
        return implode(' ', $classes);
    }

    /**
     * Generates an class attribute for templates.
     *
     * @return string
     */
    public function getClassAttr()
    {
        if ($this->Classes) {
            return " class='$this->Classes'";
        }
        return false;
    }

    /**
     * Access current page scope
     *
     * @return Controller
     */
    public function getCurrentPage()
    {
        return Controller::curr();
    }

    /**
     * Get available sub classes from current class
     * @return array
     */
    public function getSubClassNames()
    {
        $classes = array();
        $killAncestors = array();
        // make it easier to unset values
        foreach(ClassInfo::subclassesFor($this->ClassName) as $class) {
            $classes[$class] = $class;
        }

        unset(
            $classes['PlaceableObject'],
            $classes['BlockObject'],
            $classes['RegionObject']
        );

        // figure out if there are any classes we don't want to appear
        foreach($classes as $class) {
            $instance = singleton($class);

            if($instance instanceof HiddenClass) {
                unset($classes[$class]);
                continue;
            };

            // apply Translatable name
            $classes[$class] = $instance->i18n_singular_name();

            // do any of the progeny want to hide an ancestor?
            if($ancestor_to_hide = $instance->stat('hide_ancestor')) {
                // note for killing later
                $killAncestors[] = $ancestor_to_hide;
            }
        }

        // If any of the descendents don't want any of the elders to show up,
        // cruelly render the elders surplus to requirements
        if($killAncestors) {
            $killAncestors = array_unique($killAncestors);
            foreach($killAncestors as $mark) {
                unset($classes[$mark]);
            }
        }

        return $classes;
    }

    /**
     * Get available sub classes from current class
     * @return array
     */
    public function getSubClassPresets()
    {
        $classes = array();
        foreach ($this->SubClassNames as $class => $name) {
            $presetClass = "{$class}_Preset";
            if (!class_exists($presetClass)) {
                throw new Exception("Could not find preset class for $class");
            }
            $classes[$presetClass] = $name;
        }
        return $classes;
    }

    /**
     * @throws Exception
     *
     * @return PlaceableObject_Controller
     */
    public function getController()
    {
        if ($this->controller) {
            return $this->controller;
        }
        foreach (array_reverse(ClassInfo::ancestry($this->class)) as $className) {
            $controllerClass = "{$className}_Controller";
            if (class_exists($controllerClass)) {
                break;
            }
        }
        if (!class_exists($controllerClass)) {
            throw new Exception("Could not find controller class for $this->ClassName");
        }
        $this->controller = Injector::inst()->create($controllerClass, $this);
        $this->controller->init();
        return $this->controller;
    }

    /**
     * Get available template styles from config
     * @return array
     */
    public function getStyles()
    {
        $styles = $this->config()->get('styles');
        $i18nStyles = array();
        if ($styles) {
            foreach ($styles as $key => $label) {
                $i18nStyles[$key] = _t('PlaceableObject.STYLE'.strtoupper($key), $label);
            }
        }
        return $i18nStyles;
    }

    /**
     * Creating Permissions
     * @return boolean
     */
    public function canCreate($member = null)
    {
        return false;
    }

    /**
     * Editing Permissions
     * @return boolean
     */
    public function canEdit($member = null)
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    /**
     * Deleting Permissions
     * @return boolean
     */
    public function canDelete($member = null)
    {
        return false;
    }

    /**
     * Viewing Permissions
     * @return boolean
     */
    public function canView($member = null)
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }
}
