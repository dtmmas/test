@props(['title','color','total'])
<div class="white-box analytics-info">
    <h3 class="box-title">{{$title}}</h3>
    <ul class="list-inline two-part d-flex align-items-center mb-0">
        <li></li>
        <li class="ms-auto"><span class="counter text-{{$color}}">{{$total}}</span></li>
    </ul>
</div>