<?php

namespace Botble\ACL\Forms;

use Botble\ACL\Http\Requests\ApiClientRequest;
use Botble\Base\Forms\FormAbstract;

class ApiClientForm extends FormAbstract
{
    /**
     * @return mixed|void
     */
    public function buildForm()
    {
        $this
            ->setFormOption('template', 'core.base::forms.form-modal')
            ->setFormOption('class', 'form-xs')
            ->setValidatorClass(ApiClientRequest::class)
            ->add('name', 'text', [
                'label' => trans('core.acl::api.name'),
                'label_attr' => [
                    'class' => 'control-label required',
                ],
            ])
            ->add('close', 'button', [
                'label' => trans('core.acl::api.cancel'),
                'attr' => [
                    'class' => 'btn btn-warning',
                    'data-fancybox-close' => true,
                ],
            ])
            ->add('submit', 'submit', [
                'label' => trans('core.acl::api.save'),
                'attr' => [
                    'class' => 'btn btn-info pull-right',
                ],
            ]);
    }
}