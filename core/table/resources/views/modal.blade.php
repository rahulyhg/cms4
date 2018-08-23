@include('core.table::partials.modal-item', [
    'type' => 'danger',
    'name' => 'modal-confirm-delete',
    'title' => __('Confirm Delete'),
    'content' => __('Are you sure want to delete this record?'),
    'action_name' => __('Delete'),
    'action_button_attributes' => [],
])

@include('core.table::partials.modal-item', [
    'type' => 'danger',
    'name' => 'delete-many-modal',
    'title' => __('Confirm Delete'),
    'content' => __('Are you sure want to delete selected record(s)?'),
    'action_name' => __('Delete'),
    'action_button_attributes' => [
        'class' => 'delete-many-entry-button',
    ],
])

@include('core.table::partials.modal-item', [
    'type' => 'info',
    'name' => 'modal-bulk-change-items',
    'title' => __('Bulk changes'),
    'content' => '<div class="modal-bulk-change-content"></div>',
    'action_name' => __('Submit'),
    'action_button_attributes' => [
        'class' => 'confirm-bulk-change-button',
        'data-load-url' => route('tables.bulk-change.data'),
    ],
])