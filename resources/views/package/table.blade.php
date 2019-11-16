<style>
    img{
        width: 60px;
        height: 60px;}
</style>
<table class="table table-responsive" id="games-table">
    <thead>
        <tr>
        <th>ID</th>
        <th>包名</th>
        <th>图标</th>
        <th>版本</th>
        <th>BuddleID</th>
        <th>IPA地址</th>
        <th>证书</th>
        <th>下载地址</th>
        <th>简介</th>
        <th>下载量</th>
        <th>下载二维码</th>
        <th>创建时间</th>

            <th colspan="3">操作</th>
        </tr>
    </thead>
    <tbody>
    @foreach($package as $data)
        <tr>
            <td>{!! $data->id !!}</td>
            <td>{!! $data->name !!}</td>
            <td> @if($data->icon!='')
                            <img class="showimage" style="width: 100px;height: 100px" src="{{$data->icon}}">
                        @endif</td>
            <td>
              {!! $data->version !!}
            </td>
             <td>{!! $data->buddle_id !!}</td>
            <td>{!! $data->ipa_url !!}</td>
            <td>{!! $data->certificate_url !!}</td>
            <td>
              {!! $data->download_url !!}
            </td>
              <td>
              {!! $data->introduction !!}
            </td>
              <td>
              {!! $data->download_num !!}
            </td>
            <td>
{{--                {!! $domain."/udid?package_id=$data->id" !!}--}}
                {!! QrCode::size(100)->color(0,0,0)->backgroundColor(0,255,0)->generate("$domain/udid?package_id=$data->id")!!}
            </td>
            <td>{!! $data->created_at !!}</td>
            
            <td>
                {!! Form::open(['route' => ['package.destroy', $data->id], 'method' => 'get']) !!}
                <div class='btn-group'>
                    <a href="{!! route('package.edit', [$data->id]) !!}" class='btn btn-default btn-xs'>修改</a>
                    {!! Form::button('删除', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                   
                </div>
                {!! Form::close() !!}

            </td>
        </tr>
    @endforeach

    </tbody>

</table>
