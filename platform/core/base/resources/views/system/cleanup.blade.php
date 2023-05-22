@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="container">
        <h1 class="text-center pt-5">{{ trans('core/base::system.cleanup.title') }}</h1><br>
        <div class="updater-box" dir="ltr">
            <div class="note note-warning">
                <p>{{ trans('core/base::system.cleanup.backup_alert') }}</p>
            </div>
            <div class="content">
                <p class="fw-bold">{{ trans('core/base::system.cleanup.messenger_choose_without_table') }}:</p>
                <form action="{{ route('system.cleanup') }}" method="POST" id="form-cleanup-database">
                    @csrf
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('core/base::system.cleanup.table.name') }}</th>
                                <th>{{ trans('core/base::system.cleanup.table.count') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tables as $table)
                                <tr @class(['table-secondary' => in_array($table, $disabledTables['disabled'])])>
                                    <td>
                                        <input class="form-check-input"
                                            @disabled(in_array($table, $disabledTables['disabled']))
                                            @checked(in_array($table, $disabledTables['disabled']) || in_array($table, $disabledTables['checked']))
                                            type="checkbox"
                                            value="{{ $table }}"
                                            name="tables[]">
                                    </td>
                                    <td>{{ $table }}</td>
                                    <td>{{ DB::table($table)->count() }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="3">
                                    <button class="btn btn-danger btn-trigger-cleanup" type="button">{{ trans('core/base::system.cleanup.submit_button') }}</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

    {!! Form::modalAction('cleanup-modal',trans('core/base::system.cleanup.title'), 'danger', trans('core/base::system.cleanup.messenger_confirm_cleanup'), 'cleanup-submit-action', trans('core/base::system.cleanup.submit_button')) !!}
@stop
