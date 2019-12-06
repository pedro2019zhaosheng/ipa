<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{!! $game->id !!}</p>
</div>

<!-- Is Hot Field -->
<div class="form-group">
    {!! Form::label('is_hot', 'Is Hot:') !!}
    <p>{!! $game->is_hot !!}</p>
</div>

<!-- Type Field -->
<div class="form-group">
    {!! Form::label('type', 'Type:') !!}
    <p>{!! $game->type !!}</p>
</div>

<!-- Tag Field -->
<div class="form-group">
    {!! Form::label('tag', 'Tag:') !!}
    <p>{!! $game->tag !!}</p>
</div>

<!-- Name Field -->
<div class="form-group">
    {!! Form::label('name', 'Name:') !!}
    <p>{!! $game->name !!}</p>
</div>

<!-- Icon Field -->
<div class="form-group">
    {!! Form::label('icon', 'Icon:') !!}
<!--    <p>{!! $game->icon !!}</p>-->
    @php ($icon = json_decode($game->icon,true))
    @if(!empty($icon) && is_array($icon))
    @foreach ($icon as $ic)
    <p><img class='showimage' width="60" height="60" src="{{ $ic['thumb'] }}"></p>
    @endforeach
    @endif
</div>

<!-- Summary Field -->
<div class="form-group">
    {!! Form::label('summary', 'Summary:') !!}
    <p>{!! $game->summary !!}</p>
</div>

<!-- Player Num Field -->
<div class="form-group">
    {!! Form::label('player_num', 'Player Num:') !!}
    <p>{!! $game->player_num !!}</p>
</div>

<!-- Description Field -->
<div class="form-group">
    {!! Form::label('description', 'Description:') !!}
    <p >{!! $game->description !!}</p>
</div>

<!-- Info Field -->
<div class="form-group">
    {!! Form::label('info', 'Info:') !!}
    <p>{!! $game->info !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{!! $game->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{!! $game->updated_at !!}</p>
</div>

