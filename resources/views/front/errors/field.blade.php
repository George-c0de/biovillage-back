@if (!empty($field ?? ''))
    @if(!is_array($field))
        @php($field = [$field])
    @endif

    @foreach($field as $f)
        @if($errors->has($f))
            <div class="has-error">
                <div class="help-block with-errors">
                    <ul class="list-unstyled"><li>{!! $errors->first($f) !!}</li></ul>
                </div>
            </div>
            @break
        @endif
    @endforeach

@endif
