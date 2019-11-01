/**
 * author: cty@20131127
 * desc: 普通上传插�? * 基于jquery
 *
*/
;(function($){
    var tpl_upload = ''
        + '<div anyuploader-item="1" style="display:inline-block;border:0 solid #f00;overflow:hidden;position:relative;width:61px;height:61px;">'
        + '<iframe id="--iframename--" width="0" height="40" frameborder="0" src="about:blank" marginwidth="0" name="--iframename--" style="display:none"></iframe>'
        + '<form action="--action--" --attr-- method="POST" enctype="multipart/form-data" target="--iframename--" iform-ignored="1" style="position:absolute;overflow:hidden;left:0;top:0;bottom:0;right:0;">'
        + '<input type="hidden" name="_token" value="--token--"/>'
        + '<input type="file" name="Filedata[]" multiple="multiple" onchange="this.form.submit()" style="opacity:0;filter:alpha(opacity=0);cursor:pointer;position:absolute;top:0;bottom:0;right:0;width:129px;height:61px;"/>'
        + '</form>'
        + '</div>';

    $.fn.extend({
        Anyupload: function(options){
            var self      = this;
            var uploaders = $(this);
            options = options || {};
            uploaders.each(function(){
                var upbox    = $(this);
                var attr     = options.attr || '';
                var action   = upbox.attr('any-upload-action');
                var width    = upbox.attr('any-upload-width')    || 61;
                var height   = upbox.attr('any-upload-height')   || 21;
                var preview  = upbox.attr('any-upload-type')     || 'image';
                var src      = '';//
                var text     = upbox.attr('any-upload-text')     || '选择文件'; //默认文字
                var bg       = upbox.attr('any-upload-bg')       || '#666'; //背景
                var single   = upbox.attr('any-upload-single')   || false;
                var border   = upbox.attr('any-upload-border')   || 0;
                var rootid   = upbox.attr('any-upload-root')     || null;
                var defaults = $.trim(upbox.attr('any-upload-defaults')) || null;
                var name     = upbox.attr('any-upload-name')         || 'uploads';

                var csrf_token = options.csrf_token || '';
                
                var target  = 'anyupload_' + new Date().getTime() + ('_'+ Math.random()).replace('0.','');
                var _tpl    = tpl_upload.replace(/\-\-iframename\-\-/g, target).replace('--attr--',attr).replace('--token--',csrf_token);
                upbox.css({position:'relative', display:'inline-block',cursor:'pointer',border:(border+'px solid #ddd'), width:width,height:height,'line-height':height+'px',margin:0,padding:0,'border-radius':6, 'background':'url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAQAAADZc7J/AAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAAAmJLR0QA/4ePzL8AAAAHdElNRQfgBRIJOBDtmTZWAAABP0lEQVRIS+2UTW7CMBCFc6xepAfhWhykq0kCtIKEKj+oqVigqFkQFRbu6zMUiu02TipVYsF7EnJmxh8aT+IgGCAY/oOuAyD3gv8HtAghlkO0kJGM5YFPY/quA7BgeWk5wqIvYMP/e4WtNaMbdLSwRoL5wTEeoRzAB56YmTMnrKxgAUqGp0we3TjbtZpzfsbq0iSEWP6wZc9NDX9dpTwTAyDIrZJ3Fp0mkPLJVM5oJ2DLfiOs8EavuIoZGQBQ7DLm3E9q+TQzDtYDqI8ju5Aebd0foGdiDlIdzr03IGPXtiJGv+UBVEzvjO07Rqr+gC3ThQEoGLmcg3eMGQtevs5BcSVGAz0Aim+mYIJnesLV0jpUL0Cr5hs4pVNjgAMAXXIAodWjT5kNSHgHFc4t9JtzVqf2jZKcvz2/9ccf3HRF+gQAlQSvtIVmfQ==) center no-repeat'});
                if(single){
                    _tpl = _tpl.replace('multiple','single');
                }
                if('text' == preview){
                    _tpl = _tpl.replace('--preview--', text);
                }else{
                    var _img = '<img src="" style="width:61px;height:21px;border:0 solid #f00;" />'.replace('src=""', 'src="'+src+'"');
                    _tpl = _tpl.replace('--preview--', '');
                }
                _tpl = _tpl.replace(/width\:61px/g, 'width:'+width+'px').replace(/height\:61px/g, 'height:'+height+'px').replace('--bg--',bg);
                var _dom   = $("<anonymous>").html(_tpl).find('div[anyuploader-item]').eq(0); //模板(form)
                if(!action) action = options.action;
                if(!action){
                    upbox.attr('title','Error:没有上传路径');
                    return false;
                }else{
                    _dom.find("form").attr('action', action);
                }
                upbox.html(_dom.clone());
                upbox.find("input[type='file']").on("change", function(){
                    //换个图片显示正在上传
                    upbox.css({'background':'url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAA+AAAAPgBz8HmZQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAJOSURBVFiFxZdLaxRBFIW/MyOKRgVBogz4QDdqxiyC4CIbQYLiUnzgIwj5B4KO/oDgb5CQhRBwkY0ElwoKEcQ44kZdihKySETUBAU1el10D+nprppM18y0BQVTU7fOOX1fVGFmhEzgFPAcWAHeATWgnBsnkPw8YI45WZSABY8AAwbzYJXIOSTtASotTI7nwXMKkFSRNCFpTtKMpJHE9so6mMt5BLjcWwGWaHbrX2A0YTOL2/3LwO6OcgCY8IB/AUqxzSGyefATuOrAOwfUgVq7AuY8Agw4kLDbAdwCpoA7QNWTsB/js6vAznYEzHjI/wB9ARVzLz7/GlA7AkbimHdc4wnMKp4m5TswGse88eWTwJZQAa2mYsLMkFQC9gOLZvbdadSF4RVQ1NjQ+CHpGHAa+AZMm9liIQpiD1wninUj4T4DQ72IOVH3PZpYsw/4TTbrX/SAfBvwnrWyLAOcdZAb8AtH3XYo4EqK42QJ+OCJzoJ1P0OfEfUYgB9AvaHsgcMD13qUA1XgJnFbl5khaSNwAzhDVAV3zexhl7/eOf57H8h9IypUgKQjkjYVLkDSsKSXwFtgXlItlEDSgKRxSSecBp5MfUJzRawCewMyfiCFM5a2yXhAUj+QVlsGLgQ44FJqfTltkBFgZkvAGwfYIxeDpM0tBDxeZ+0NwUXgK2s34imHzS7gPtFldBYY9mCNxcS3ga2Z/Rbx6yNy2UHP/jjN8a2HdMbgRiTpFTCU+rvfzD7lwemkEU2n1k/zkgNhj9PYa9uJ3gPzRG+DwyE4/wBpZQi5onsMZQAAAABJRU5ErkJggg==) center no-repeat'});
                    value = this.value;
                    if('function' == typeof(options.fcbBefore)){
                        options.fcbBefore(upbox);
                    }
                });
                var inputbox  = upbox.parent().find("input[any-upload-urls]");
                if(rootid){
                    var imgbox = $('#'+rootid).find("[any-upload-imgs]");
                }else{
                    var imgbox = upbox.parent().find("[any-upload-imgs]");
                }
                if(defaults){
                    try{
                        var dft_urls = new Array();
                        var trr = defaults.split(';');
                        for(var i=0,length=trr.length; i<length; i++){
                            if(trr[i].indexOf('|') > -1){
                                var _r = trr[i].split('|');
                            }else{
                                var _r = trr[i].split(':');
                            }
                            if(_r[0].length > 0){
                                dft_urls.push({url:_r[0],thumb:_r[1],width:_r[2],height:_r[3]});
                            }
                        }
                        // console.log(dft_urls);
                        self._set_defalut(name,imgbox, inputbox, dft_urls, preview, single);
                    }catch(e){}
                }
                upbox.find("iframe[name='"+target+"']").on("load", function(){
                    //图片换回来
                    upbox.css({'background':'url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAQAAADZc7J/AAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAAAmJLR0QA/4ePzL8AAAAHdElNRQfgBRIJOBDtmTZWAAABP0lEQVRIS+2UTW7CMBCFc6xepAfhWhykq0kCtIKEKj+oqVigqFkQFRbu6zMUiu02TipVYsF7EnJmxh8aT+IgGCAY/oOuAyD3gv8HtAghlkO0kJGM5YFPY/quA7BgeWk5wqIvYMP/e4WtNaMbdLSwRoL5wTEeoRzAB56YmTMnrKxgAUqGp0we3TjbtZpzfsbq0iSEWP6wZc9NDX9dpTwTAyDIrZJ3Fp0mkPLJVM5oJ2DLfiOs8EavuIoZGQBQ7DLm3E9q+TQzDtYDqI8ju5Aebd0foGdiDlIdzr03IGPXtiJGv+UBVEzvjO07Rqr+gC3ThQEoGLmcg3eMGQtevs5BcSVGAz0Aim+mYIJnesLV0jpUL0Cr5hs4pVNjgAMAXXIAodWjT5kNSHgHFc4t9JtzVqf2jZKcvz2/9ccf3HRF+gQAlQSvtIVmfQ==) center no-repeat'});
                    upbox.find(".uploading").remove();
                    var doc = $(this).get(0).contentDocument || $(this).get(0).contentWindow.document;
                    if(0 == $(doc).find("body").html().length) {
                        return;
                    }
                    console.log($(doc).find("body").html());
                    eval("var json = " + $(doc).find("body").html().replace(/(<embed.*)|(<pre.*>)|(<\/pre>)/g, ''));
                    if(0 == parseInt(json.status)) {
                        return;
                    }
                    console.log(json);
                    if('function' == typeof(options.uploaded)){
                        options.uploaded(uploader, json);
                    }
                    //
                    var urls   = json.data;
                    if(imgbox.length > 0 || inputbox.length > 0){
                        self._set_values(name,imgbox, inputbox, json.data, preview, single, options.cb);
                    }
                    if('function' == typeof(options.fcbAfter)){
                        options.fcbAfter(upbox, json);
                    }
                });
            });
        },_set_defalut: function(name,imgbox, inputbox, urls, preview, single){
            this._set_values(name,imgbox, inputbox, urls, preview, single);
        },_set_values: function(name,imgbox, inputbox, urls, preview, single, cb){
            //private function
            console.log(urls);
            var local_preview = "";
            for(var i=0; i<urls.length; i++){
                var ro = urls[i];
                var url = decodeURIComponent(ro.url);
                var thumb = ro.thumb;
                var width = ro.width;
                var height = ro.height;
                var showname = ro.name?ro.name:url;
                var ext = url.replace(/.+\./,"");
                console.log(ext);
                if(ext === "mp4"){
                    local_preview="video";
                    showname = '<video src="'+url+'" width="96" height="96" controls="controls"></video>';
                }else{
                    local_preview = preview;
                }
                console.log(showname);
                if(single){
                    inputbox.empty();
                    imgbox.empty();
                }
                inputbox.val(url);
                var url_html = "<input type='hidden' name='"+name+"["+url+"][url]' value='"+url+"'/>";
                var thumb_html = "<input type='hidden' name='"+name+"["+url+"][thumb]' value='"+thumb+"'/>";
                var width_html = "<input type='hidden' name='"+name+"["+url+"][width]' value='"+width+"'/>";
                var height_html = "<input type='hidden' name='"+name+"["+url+"][height]' value='"+height+"'/>";
                imgbox.css({position:'relative',display:'inline-block'}).append($("<span style='display:inline-block;position:relative;border:1px solid #eee;margin:1px;padding:1px;min-height:48px;float:left;line-height:48px;'>"+url_html+thumb_html+width_html+height_html+('image'==local_preview?("<img src='"+url+"' style='max-height:96px;max-width:96px;float:left;'/>"):showname)+"<a onclick='javascript:$(this).parent().remove();' style='position:absolute;top:2px;right:4px;font-family:arial;font-size:16px;cursor:pointer;line-height:14px;'>×</a></span>"));
                if('function' == typeof(cb)){
                    cb(url,showname);
                }
            }
        }
    });
})(jQuery);