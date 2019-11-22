@extends('layouts.app')
@include('adminlte-templates::common.errors')
@include('flash::message')
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
                   {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'patch']) !!}

                        @include('users.fields',['user'=>$user])

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection