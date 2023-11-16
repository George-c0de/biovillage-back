@if (!empty($field ?? ''))
    @if(!is_array($field))
        @php($field = [$field])
    @endif

    @foreach($field as $f)
        @if($errors->has($f))
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {!! $errors->first($f) !!}
            </div>
            @break
        @endif
    @endforeach

@endif
