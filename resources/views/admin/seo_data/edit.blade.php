@extends('admin.layouts.fancybox')
@section('content')
    <div id="content" class="content">

        <!-- begin page-header -->
        <h1 class="page-header">Edit Seo Data</h1>
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
                        <h4 class="panel-title">Seo Data</h4>
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
                        <form class="form-horizontal" action="{{route('admin::updateSeoData',$info->id)}}" method="post" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label class="col-md-3 control-label">Title</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="title" value="{{$info->title}}" placeholder="Enter seo title." />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Description</label>
                                <div class="col-md-6">
                                    <textarea class="form-control" rows="6" name="description" placeholder="Enter seo description." >{{$info->description}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Keywords</label>
                                <div class="col-md-6">
                                    <textarea class="form-control" rows="6" name="keywords" placeholder="Enter seo keywords." >{{$info->keywords}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12" style="text-align: center">
                                    <button type="submit" class="btn btn-sm btn-success">Update</button>
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
    @endpush
@endsection