@props(['value'=>null,'old'=>"",'data'=>''])



<option value="{{ $value }}" @if ($old == $value) selected @elseif($data == $value) selected @endif>
    {{ $value }}
</option>
