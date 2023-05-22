<?php

namespace Botble\ACL\Tables;

use BaseHelper;
use Html;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Botble\ACL\Repositories\Interfaces\RoleInterface;
use Botble\ACL\Repositories\Interfaces\UserInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;

class RoleTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    protected UserInterface $userRepository;

    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        RoleInterface $roleRepository,
        UserInterface $userRepository
    ) {
        parent::__construct($table, $urlGenerator);

        $this->repository = $roleRepository;
        $this->userRepository = $userRepository;

        if (! Auth::user()->hasAnyPermission(['roles.edit', 'roles.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                if (! Auth::user()->hasPermission('roles.edit')) {
                    return BaseHelper::clean($item->name);
                }

                return Html::link(route('roles.edit', $item->id), BaseHelper::clean($item->name));
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('description', function ($item) {
                return $item->description;
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('created_by', function ($item) {
                return BaseHelper::clean($item->author->name);
            })
            ->addColumn('operations', function ($item) {
                return $this->getOperations('roles.edit', 'roles.destroy', $item);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()
            ->with('author')
            ->select([
                'id',
                'name',
                'description',
                'created_at',
                'created_by',
            ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'title' => trans('core/base::tables.name'),
            ],
            'description' => [
                'title' => trans('core/base::tables.description'),
                'class' => 'text-start',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'created_by' => [
                'title' => trans('core/acl::permissions.created_by'),
                'width' => '100px',
            ],
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('roles.create'), 'roles.create');
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('roles.deletes'), 'roles.destroy', parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            'name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
        ];
    }
}
