@extends('backend.app')
@section('content')
    <div class="first-section">
        @for($i=1; $i<=48; $i++)
            <div class="hoverWrapper">
                <div class="zaman-2 firstchild" data-toggle="tooltip" data-placement="top" data-original-title="08:00-08:10"><span>{{$i}}</span></div>
                <div class="hoverShow1"><p>Qısa məzmun{{$i}}</p></div>
            </div>
        @endfor
        <div style="clear:both;"></div>
    </div>
    <div style="clear:both;"></div>
@endsection

@section('css')

@endsection

@section('js')

@endsection