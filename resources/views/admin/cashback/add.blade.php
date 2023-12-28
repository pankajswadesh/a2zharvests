@extends('admin.layouts.fancybox')
@section('content')
    <div id="content" class="content">
        <!-- begin page-header -->
        <h1 class="page-header">Add CashBack</h1>
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
                        </div>
                        <h4 class="panel-title">CashBack</h4>
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
                    <div class="panel-body">
                        <form class="form-horizontal" action="{{route('admin::saveCashBack')}}" method="post">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label class="col-md-3 control-label">Min. Amount</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control number" name="min_amount" value="{{old('min_amount')}}" placeholder="Enter Min. Amount" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">CashBack Percentage</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control number" name="cashback_percent" value="{{old('cashback_percent')}}" placeholder="Enter CashBack Percentage" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">CashBack Upto</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control number" name="cashback_upto" value="{{old('cashback_upto')}}" placeholder="Enter CashBack Upto" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12" style="text-align: center">
                                    <button type="submit" class="btn btn-sm btn-success">Submit</button>
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
            $(document).ready(function() {
                $('.number').keypress(function (event) {
                    return isNumber(event, this)
                });
            });
            function isNumber(evt, element) {

                var charCode = (evt.which) ? evt.which : event.keyCode

                if (
                    (charCode != 45 || $(element).val().indexOf('-') != -1) &&
                    (charCode != 46 || $(element).val().indexOf('.') != -1) &&
                    (charCode < 48 || charCode > 57))
                    return false;

                return true;
            }
        </script>
    @endpush
@endsection
