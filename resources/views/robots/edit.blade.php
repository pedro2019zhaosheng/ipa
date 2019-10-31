@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            更新机器人
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($robot, ['route' => ['robots.update', $robot->id], 'method' => 'patch']) !!}

                        @include('robots.fields',['robot'=>$robot])

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection