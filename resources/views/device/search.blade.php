<div class="box box-primary">
    <div class="box-body">
        {!! Form::open(['url' => 'device/device','method'=>'GET']) !!}
        <div class="form-group col-sm-5">
            {!! Form::label('name', 'udid:') !!}
            <label class="checkbox-inline">
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
            </label>
        </div>

        <!--<div class="form-group col-sm-3">
            {!! Form::label('is_hot', 'Is Hot:') !!}
            <label class="checkbox-inline">
                {!! Form::select('is_hot', ['1'=>'是','0'=>'否']); !!}
            </label>
        </div>-->
        <div class="form-group col-sm-5">
<!--            class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px"-->
        <label class="checkbox-inline">
            {!! Form::submit('搜索', ['class' => 'btn btn-primary pull-right']) !!}
        </label>
             {{--<a class="btn btn-primary pull-right" style="margin-right:400px;margin-top: 0px;margin-bottom: 5px" href="{!! url('device/create') !!}">添加--}}
             {{--</a>--}}

        </div>

        {!! Form::close() !!}

    </div>
</div>