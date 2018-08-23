<?php

namespace Botble\Base\Forms;

use Assets;
use Botble\Base\Forms\Fields\ColorField;
use Botble\Base\Forms\Fields\CustomRadioField;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\TimeField;
use Botble\Slug\Forms\Fields\PermalinkField;
use JsValidator;
use Kris\LaravelFormBuilder\Fields\FormField;
use Kris\LaravelFormBuilder\Form;

abstract class FormAbstract extends Form
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var null
     */
    protected $title = null;

    /**
     * @var null
     */
    protected $module_name = null;

    /**
     * @var null
     */
    protected $validatorClass = null;

    /**
     * @var array
     */
    protected $meta_boxes = [];

    /**
     * @var null
     */
    protected $action_buttons = null;

    /**
     * @var null
     */
    protected $break_field_point = null;

    /**
     * @var bool
     */
    protected $useInlineJs = false;

    /**
     * FormAbstract constructor.
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function __construct()
    {
        $this->setMethod('POST');
        $this->setFormOption('template', 'core.base::forms.form');
        $this->action_buttons = view('core.base::elements.form-actions')->render();
        $this->setFormOption('id', 'form_' . md5(get_class($this)));
    }

    /**
     * @return mixed
     * @author Sang Nguyen
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param mixed $options
     * @return $this
     * @author Sang Nguyen
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return null
     */
    public function getTitle(): string
    {
        return (string)$this->title;
    }

    /**
     * @param null $title
     * @return $this
     */
    public function setTitle($title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return null
     */
    public function getModuleName(): string
    {
        return (string)$this->module_name;
    }

    /**
     * @param null $module
     * @return $this
     */
    public function setModuleName($module): self
    {
        $this->module_name = $module;
        return $this;
    }

    /**
     * @return array
     */
    public function getMetaBoxes(): array
    {
        return $this->meta_boxes;
    }


    /**
     * @param $name
     * @return null|string
     * @throws \Throwable
     */
    public function getMetaBox($name): string
    {
        if (!array_get($this->meta_boxes, $name)) {
            return null;
        }

        $meta_box = $this->meta_boxes[$name];
        return view('core.base::forms.partials.meta-box', compact('meta_box'))->render();
    }

    /**
     * @param array $boxes
     * @return $this
     */
    public function addMetaBoxes($boxes): self
    {
        if (!is_array($boxes)) {
            $boxes = [$boxes];
        }
        $this->meta_boxes = array_merge($this->meta_boxes, $boxes);
        return $this;
    }

    /**
     * @param $name
     * @return FormAbstract
     */
    public function removeMetaBox($name): self
    {
        unset($this->meta_boxes[$name]);
        return $this;
    }

    /**
     * @return null
     */
    public function getActionButtons(): string
    {
        return (string)$this->action_buttons;
    }

    /**
     * @return $this
     */
    public function removeActionButtons(): self
    {
        $this->action_buttons = null;
        return $this;
    }

    /**
     * @param null $action_buttons
     * @return $this
     */
    public function setActionButtons($action_buttons): self
    {
        $this->action_buttons = $action_buttons;
        return $this;
    }

    /**
     * @return null
     */
    public function getValidatorClass(): string
    {
        return (string)$this->validatorClass;
    }

    /**
     * @param null $validatorClass
     * @return $this
     */
    public function setValidatorClass($validatorClass): self
    {
        $this->validatorClass = $validatorClass;
        return $this;
    }

    /**
     * @return null
     */
    public function getBreakFieldPoint(): string
    {
        return (string)$this->break_field_point;
    }

    /**
     * @param null $break_field_point
     * @return $this
     */
    public function setBreakFieldPoint(string $break_field_point): self
    {
        $this->break_field_point = $break_field_point;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUseInlineJs(): bool
    {
        return $this->useInlineJs;
    }

    /**
     * @param bool $useInlineJs
     * @return $this
     */
    public function setUseInlineJs(bool $useInlineJs): self
    {
        $this->useInlineJs = $useInlineJs;
        return $this;
    }

    /**
     * @param $model
     * @return FormAbstract|Form
     */
    public function setModel($model): self
    {
        parent::setupModel($model);
        $this->rebuildForm();
        return $this;
    }

    /**
     * @return $this
     * @author Sang Nguyen
     * @return $this
     */
    public function withCustomFields(): self
    {
        if (!$this->formHelper->hasCustomField('editor')) {
            $this->addCustomField('editor', EditorField::class);
        }
        if (!$this->formHelper->hasCustomField('onOff')) {
            $this->addCustomField('onOff', OnOffField::class);
        }
        if (!$this->formHelper->hasCustomField('customRadio')) {
            $this->addCustomField('customRadio', CustomRadioField::class);
        }
        if (!$this->formHelper->hasCustomField('mediaImage')) {
            $this->addCustomField('mediaImage', MediaImageField::class);
        }
        if (!$this->formHelper->hasCustomField('color')) {
            $this->addCustomField('color', ColorField::class);
        }
        if (!$this->formHelper->hasCustomField('time')) {
            $this->addCustomField('time', TimeField::class);
        }
        if (!$this->formHelper->hasCustomField('permalink')) {
            $this->addCustomField('permalink', PermalinkField::class);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function hasTabs(): self
    {
        $this->setFormOption('template', 'core.base::forms.form-tabs');
        return $this;
    }

    /**
     * @return int
     * @author Sang Nguyen
     */
    public function hasMainFields()
    {
        if (!$this->break_field_point) {
            return count($this->fields);
        }

        $main_fields = [];

        /**
         * @var FormField $field
         */
        foreach ($this->fields as $field) {
            if ($field->getName() == $this->break_field_point) {
                break;
            }

            $main_fields[] = $field;
        }

        return count($main_fields);
    }

    /**
     * @return $this
     */
    public function disableFields()
    {
        parent::disableFields();
        return $this;
    }

    /**
     * @param array $options
     * @param bool $showStart
     * @param bool $showFields
     * @param bool $showEnd
     * @return string
     * @author Sang Nguyen
     */
    public function renderForm(array $options = [], $showStart = true, $showFields = true, $showEnd = true): string
    {
        Assets::addAppModule(['form-validation']);
        Assets::addJavascript(['are-you-sure']);

        return parent::renderForm($options, $showStart, $showFields, $showEnd);
    }

    /**
     * @return string
     * @throws \Exception
     * @throws \Proengsoft\JsValidation\Facades\FormRequestArgumentException
     * @author Sang Nguyen
     */
    public function renderValidatorJs(): string
    {
        $element = null;
        if ($this->getFormOption('id')) {
            $element = '#' . $this->getFormOption('id');
        } elseif ($this->getFormOption('class')) {
            $element = '.' . $this->getFormOption('class');
        }

        return JsValidator::formRequest($this->getValidatorClass(), $element);
    }
}