<?php

namespace Botble\ACL\Tables;

use Botble\ACL\Repositories\Interfaces\UserInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;

class UserTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $has_actions = true;

    /**
     * @var bool
     */
    protected $has_configuration = true;

    /**
     * @var bool
     */
    protected $has_filter = true;

    /**
     * TagTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param UserInterface $userRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, UserInterface $userRepository)
    {
        $this->repository = $userRepository;
        $this->setOption('id', 'table-users');
        parent::__construct($table, $urlGenerator);
    }

    /**
     * Display ajax response.
     *
     * @return \Illuminate\Http\JsonResponse
     * @author Sang Nguyen
     * @since 2.1
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('checkbox', function ($item) {
                return table_checkbox($item->id);
            })
            ->editColumn('username', function ($item) {
                return anchor_link(route('user.profile.view', $item->id), $item->username);
            })
            ->editColumn('created_at', function ($item) {
                return date_from_database($item->created_at, config('core.base.general.date_format.date'));
            })
            ->editColumn('role_name', function ($item) {
                return view('core.acl::users.partials.role', compact('item'))->render();
            })
            ->editColumn('status', function ($item) {
                return table_status(acl_is_user_activated($item) ? 1 : 0);
            })
            ->removeColumn('role_id');
        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, USER_MODULE_SCREEN_NAME)
            ->addColumn('operations', function ($item) {
                return view('core.acl::users.partials.actions', compact('item'))->render();
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * Get the query object to be processed by datatables.
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     * @author Sang Nguyen
     * @since 2.1
     */
    public function query()
    {
        $model = app(UserInterface::class)->getModel();
        $query = $model->leftJoin('role_users', 'users.id', '=', 'role_users.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'role_users.role_id')
            ->select([
                'users.id',
                'users.username',
                'users.email',
                'roles.name as role_name',
                'roles.id as role_id',
                'users.updated_at',
                'users.created_at',
            ])
            ->latest();
        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, USER_MODULE_SCREEN_NAME));
    }

    /**
     * @return array
     * @author Sang Nguyen
     * @since 2.1
     */
    public function columns()
    {
        return [
            'id' => [
                'name' => 'users.id',
                'title' => trans('core.base::tables.id'),
                'width' => '20px',
            ],
            'username' => [
                'name' => 'users.username',
                'title' => trans('core.acl::users.username'),
                'class' => 'text-left',
            ],
            'email' => [
                'name' => 'users.email',
                'title' => trans('core.acl::users.email'),
            ],
            'role_name' => [
                'name' => 'role_name',
                'title' => trans('core.acl::users.role'),
            ],
            'created_at' => [
                'name' => 'users.created_at',
                'title' => trans('core.base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'name' => 'users.status',
                'title' => trans('core.base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * @return array
     * @author Sang Nguyen
     * @since 2.1
     * @throws \Throwable
     */
    public function buttons()
    {
        $buttons = [
            'create' => [
                'link' => route('users.create'),
                'text' => view('core.base::elements.tables.actions.create')->render(),
            ],
        ];
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, USER_MODULE_SCREEN_NAME);
    }

    /**
     * @return array
     * @author Sang Nguyen
     * @since 2.1
     * @throws \Throwable
     */
    public function actions()
    {
        return [
            'activate' => [
                'link' => route('users.change.status', ['status' => 1]),
                'text' => view('core.base::elements.tables.actions.activate')->render(),
            ],
            'deactivate' => [
                'link' => route('users.change.status', ['status' => 0]),
                'text' => view('core.base::elements.tables.actions.deactivate')->render(),
            ],
        ];
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function bulkActions(): array
    {
        $actions = parent::bulkActions();

        $actions['delete-many'] = view('core.table::partials.delete', [
            'href' => route('users.delete.many'),
            'data_class' => get_class($this),
        ]);

        return $actions;
    }

    /**
     * @return mixed
     */
    public function getBulkChanges(): array
    {
        return [
            'users.username' => [
                'title' => trans('core.acl::users.username'),
                'type' => 'text',
                'validate' => 'required|max:120',
                'callback' => 'getUserNames',
            ],
            'users.email' => [
                'title' => trans('core.base::tables.email'),
                'type' => 'text',
                'validate' => 'required|max:120|email',
                'callback' => 'getEmails',
            ],
            'users.created_at' => [
                'title' => trans('core.base::tables.created_at'),
                'type' => 'date',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getUserNames()
    {
        return $this->repository->pluck('users.username', 'users.id');
    }

    /**
     * @return array
     */
    public function getEmails()
    {
        return $this->repository->pluck('users.email', 'users.id');
    }
}
