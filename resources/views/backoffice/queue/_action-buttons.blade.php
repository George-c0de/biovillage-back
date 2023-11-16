<form method="POST" action="{{ route('actionFailedJob') }}" style="display: inline-block;">
    <input type="hidden" name="action" value="{{ \App\Http\Controllers\BackOffice\QueueController::ACTION_RETRY  }}">
    <input type="hidden" name="ids[]" value="{{ $job->id }}">
    <button class="btn btn-xs btn-warning" title="Повторить">
        <i class="fa  fa-repeat"></i>
    </button>
</form>

<form method="POST" action="{{ route('actionFailedJob') }}" style="display: inline-block;" >
    <input type="hidden" name="action" value="{{ \App\Http\Controllers\BackOffice\QueueController::ACTION_DELETE  }}">
    <input type="hidden" name="ids[]" value="{{ $job->id }}">
    <button class="btn btn-xs btn-danger btn-delete" title="Удалить">
        <i class="fa fa-trash-o"></i>
    </button>
</form>