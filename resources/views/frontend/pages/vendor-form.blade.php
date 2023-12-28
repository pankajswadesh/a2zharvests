@extends('frontend.layouts.frontendlayout')
@section('title')
    <title>A2Z Harvests : Vendor Form</title>
@endsection
@section('content')
<!-----------bannar area start------>
<section id="breadcrumb">
    <div class="container">
        <div class="breadcrumb-area">
            <ul>
                <li><a href="{{url("/")}}">Home</a></li>
                <li><span>/</span></li>
                <li>Vendor Registration</li>

            </ul>
        </div>
    </div>
</section>
<!-----------bannar area close------>
<!-- contact area starts -->
<section id="vendor-form">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="form-area">
                    <h3>Vendor Registration</h3>
                    <p>Complete form below to signup as a vendor.</p>
                    <form role="form">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Business Name:</label>
                                    <input type="text" class="form-control" id="business-name" name="name" placeholder=""
                                           required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Business Address:</label>
                                    <input type="text" class="form-control" id="business-address" name="address" placeholder=""
                                           required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Business Id:</label>
                                    <input type="text" class="form-control" id="business-id" name="id"
                                           placeholder="" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">GST registration:</label>
                                    <input type="text" class="form-control" id="gst" name="gst"
                                           placeholder="" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Phone Number:</label>
                                    <input type="text" class="form-control" id="number" name="number"
                                           placeholder="" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Mail Id:</label>
                                    <input type="email" class="form-control" id="mail-id" name="mail-id"
                                           placeholder="" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button type="button" id="submit" name="submit" class="btn btn-primary">Send</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</section>
<!-- contact area ends -->
<div class="clearfix"></div>
<!-- footer area starts -->
@endsection
