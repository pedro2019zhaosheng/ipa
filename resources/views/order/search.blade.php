<div class="box box-primary">
    <div class="box-body">
        {!! Form::open(['url' => 'order/order','method'=>'GET']) !!}
        <div class="form-group col-sm-5">
            {!! Form::label('keyword', '订单号/手机号/购买人姓名:') !!}
            <label class="checkbox-inline">
            {!! Form::text('keyword', null, ['class' => 'form-control']) !!}
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

        </div>

        {!! Form::close() !!}

    </div>
</div>