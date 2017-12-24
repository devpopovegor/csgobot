<ol>
@foreach($items as $item)
    <li>{{$item->full_name}}</li>
@endforeach
</ol>