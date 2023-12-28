@extends('admin.layouts.adminlayout')
@section('title', 'Notification')
@section('content')
    <div id="content" class="content">

        <!-- begin page-header -->
        <h1 class="page-header">@yield('title')</h1>
        <!-- end page-header -->

        <!-- begin row -->
        <div class="row">
            <!-- begin col-6 -->
            <div class="col-md-12">
                <!-- begin panel -->
                <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
                    <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
                        </div>
                        <h4 class="panel-title">@yield('title')</h4>
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div><br />
                    @endif
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>{{Session::get('success')}}</strong>
                        </div>
                    @endif
                    @if(Session::has('error'))
                        <div class="alert alert-danger">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>{{Session::get('error')}}</strong>
                        </div>
                    @endif
                    <div class="panel-body">
                        <form class="form-horizontal" action="{{route('admin::sendNotification')}}" method="post" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label class="col-md-3 control-label">Notification Heading</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="notification_heading" value="{{old('notification_heading')}}" placeholder="Enter Notification Heading." />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Notification Description</label>
                                <div class="col-md-6">
                                    <textarea class="summernote_description" name="notification_description" style="margin: 0px; width: 510px; height: 148px;" placeholder="Enter Notification Description">{!! old('notification_description') !!}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12" style="text-align: center">
                                    <button type="submit" class="btn btn-sm btn-success">Send Notification</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- end panel -->
            </div>
            <!-- end col-6 -->
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function () {
                var image_string=$('#old_image_string').val();
                if(image_string!=''){
                    $('#img_show').show();
                    $('#preview_image').attr('src',image_string);
                }


            });


            var input = document.querySelector('input[type=file]');

            // You will receive the Base64 value every time a user selects a file from his device
            // As an example I selected a one-pixel red dot GIF file from my computer
            function base64_logo_image_encode(file) {
                reader = new FileReader();

                reader.onloadend = function () {
                    // Since it contains the Data URI, we should remove the prefix and keep only Base64 string
                    var b64 = reader.result.replace(/^data:image.+;base64,/, '');
                    console.log(b64); //-> "R0lGODdhAQABAPAAAP8AAAAAACwAAAAAAQABAAACAkQBADs="
                    $('#img_show').show();
                    $('#preview_image').attr('src',reader.result);
                    $('#old_image_string').val(reader.result);
                    $('#image_string').val(b64);
                };
                reader.readAsDataURL(file);

            };





        </script>
    @endpush
@endsection
