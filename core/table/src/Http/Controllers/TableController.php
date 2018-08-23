<?php

namespace Botble\Table\Http\Controllers;

use App\Http\Controllers\Controller;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Table\Http\Requests\BulkChangeRequest;
use Botble\Table\Http\Requests\FilterRequest;
use Botble\Table\TableBuilder;
use Form;
use Illuminate\Http\Request;
use Validator;

class TableController extends Controller
{

    /**
     * @var TableBuilder
     */
    protected $tableBuilder;

    /**
     * TableController constructor.
     * @param TableBuilder $tableBuilder
     * @author Sang Nguyen
     */
    public function __construct(TableBuilder $tableBuilder)
    {
        $this->tableBuilder = $tableBuilder;
    }

    /**
     * @param BulkChangeRequest $request
     * @return array|mixed
     * @throws \Throwable
     * @author Sang Nguyen
     */
    public function getDataForBulkChanges(BulkChangeRequest $request, TableBuilder $tableBuilder)
    {
        $object = $tableBuilder->create($request->input('class'));

        $data = $object->getValueInput(null, null, 'text');
        if (!$request->input('key')) {
            return $data;
        }

        $column = array_get($object->getBulkChanges(), $request->input('key'), null);
        if (empty($column)) {
            return $data;
        }

        $label_class = 'control-label';
        if (!empty($column) && str_contains(array_get($column, 'validate'), 'required')) {
            $label_class .= ' required';
        }

        $label = '';
        if (!empty($column['title'])) {
            $label = Form::label($column['title'], null, ['class' => $label_class])->toHtml();
        }

        if (isset($column['callback']) && method_exists($object, $column['callback'])) {
            $data = $object->getValueInput($column['title'], null, $column['type'], call_user_func([$object, $column['callback']]));
        } else {
            $data = $object->getValueInput($column['title'], null, $column['type'], array_get($column, 'choices', []));
        }

        $data['html'] = $label . $data['html'];
        return $data;
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function postSaveBulkChange(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError(true)
                ->setMessage(__('Please select at least one record to perform this action!'));
        }

        $input_key = $request->input('key');
        $input_value = $request->input('value');

        $object = $this->tableBuilder->create($request->input('class'));
        $columns = $object->getBulkChanges();

        if (!empty($columns[$input_key]['validate'])) {
            $validator = Validator::make($request->input(), [
                'value' => $columns[$input_key]['validate']
            ]);

            if ($validator->fails()) {
                return $response
                    ->setError(true)
                    ->setMessage($validator->getMessageBag()->toArray()['value'][0]);
            }
        }

        foreach ($ids as $id) {
            $item = $object->getRepository()
                ->getModel()
                ->where(['id' => $id])
                ->first();
            if ($item) {
                $item->{$input_key} = $object->prepareBulkChangeValue($input_key, $input_value);
                $object->getRepository()->createOrUpdate($item);
                event(new UpdatedContentEvent($object->getRepository()->getScreen(), $request, $item));
            }
        }

        return $response->setMessage(__('Update data for selected record(s) successfully!'));
    }

    /**
     * @param FilterRequest $request
     * @return array|mixed
     * @throws \Throwable
     * @author Sang Nguyen
     */
    public function getFilterInput(FilterRequest $request, TableBuilder $tableBuilder)
    {
        $object = $tableBuilder->create($request->input('class'));

        $data = $object->getValueInput(null, null, 'text');
        if (!$request->input('key')) {
            return $data;
        }

        $column = array_get($object->getBulkChanges(), $request->input('key'), null);
        if (empty($column)) {
            return $data;
        }

        if (isset($column['callback']) && method_exists($object, $column['callback'])) {
            return $object->getValueInput(null, $request->input('value'), $column['type'], call_user_func([$object, $column['callback']]));
        }

        return $object->getValueInput(null, $request->input('value'), $column['type'], array_get($column, 'choices', []));
    }
}