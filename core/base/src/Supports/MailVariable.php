<?php

namespace Botble\Base\Supports;

use Carbon\Carbon;

class MailVariable
{
    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @var array
     */
    protected $variableValues = [];

    /**
     * @var string
     */
    protected $module = 'core';

    /**
     * MailVariable constructor.
     */
    public function initVariable()
    {
        $this->variables['core'] = [
            'header' => __('Email template header'),
            'footer' => __('Email template footer'),
            'site_title' => __('Site title'),
            'site_url' => __('Site URL'),
            'site_logo' => __('Site Logo'),
            'date_time' => __('Current date time'),
            'date_year' => __('Current year'),
            'site_admin_email' => __('Site admin email'),
        ];
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function initVariableValues()
    {
        $this->variableValues['core'] = [
            'header' => get_setting_email_template_content('core', 'base', 'header'),
            'footer' => get_setting_email_template_content('core', 'base', 'footer'),
            'site_title' => setting('site_title'),
            'site_url' => route('public.index'),
            'site_logo' => url(theme_option('logo', setting('admin_logo'))),
            'date_time' => Carbon::now()->toDateTimeString(),
            'date_year' => Carbon::now()->format('Y'),
            'site_admin_email' => setting('admin_email'),
        ];
    }

    /**
     * @param $module
     * @return MailVariable
     */
    public function setModule($module): self
    {
        $this->module = $module;
        return $this;
    }

    /**
     * @param $name
     * @param null $description
     * @param string $module
     * @return MailVariable
     */
    public function addVariable($name, $description = null): self
    {
        $this->variables[$this->module][$name] = $description;
        return $this;
    }

    /**
     * @param array $variables
     * @param string $module
     * @return MailVariable
     */
    public function addVariables(array $variables): self
    {
        foreach ($variables as $name => $description) {
            $this->variables[$this->module][$name] = $description;
        }

        return $this;
    }

    /**
     * @param null $module
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getVariables($module = null): array
    {
        $this->initVariable();

        if (!$module) {
            return $this->variables;
        }

        return array_get($this->variables, $module, []);
    }

    /**
     * @param $variable
     * @param $value
     * @param string $module
     * @return MailVariable
     */
    public function setVariableValue($variable, $value): self
    {
        $this->variables[$this->module][$variable] = $value;
        return $this;
    }

    /**
     * @param array $data
     * @param string $module
     * @return MailVariable
     */
    public function setVariableValues(array $data): self
    {
        foreach ($data as $name => $value) {
            $this->variableValues[$this->module][$name] = $value;
        }

        return $this;
    }

    /**
     * @param $variable
     * @param $module
     * @param string $default
     * @return string
     */
    public function getVariableValue($variable, $module, $default = ''): string
    {
        return array_get($this->variableValues, $module . '.' . $variable, $default);
    }

    /**
     * @param $content
     * @param null $module
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function prepareData($content): string
    {
        $this->initVariable();
        $this->initVariableValues();

        $content = $this->replaceVariableValue(array_keys($this->variables['core']), 'core', $content);

        if ($this->module !== 'core') {
            $content = $this->replaceVariableValue(array_keys(array_get($this->variables, $this->module, [])), $this->module, $content);
        }

        return apply_filters(BASE_FILTER_EMAIL_TEMPLATE, $content);
    }

    /**
     * @param array $variables
     * @param $module
     * @param $content
     * @return string
     */
    protected function replaceVariableValue(array $variables, $module, $content): string
    {
        foreach ($variables as $variable) {
            $content = str_replace('{{ ' . $variable . ' }}', $this->getVariableValue($variable, $module), $content);
            $content = str_replace('{{' . $variable . '}}', $this->getVariableValue($variable, $module), $content);
            $content = str_replace('{{ ' . $variable . '}}', $this->getVariableValue($variable, $module), $content);
            $content = str_replace('{{' . $variable . ' }}', $this->getVariableValue($variable, $module), $content);
            $content = str_replace('<?php echo e(' . $variable . '); ?>', $this->getVariableValue($variable, $module), $content);
        }

        return $content;
    }
}