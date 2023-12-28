@extends('frontend.layouts.frontendlayout')
@section('title')
    <title>A2Z Harvests : Change Password</title>
@endsection
@section('content')
<!-----------bannar area start------>
<section id="breadcrumb">
    <div class="container">
        <div class="breadcrumb-area">
            <ul>
                <li><a href="{{url('/')}}">Home</a></li>
                <li><span>/</span></li>
                <li>Change Password</li>

            </ul>
        </div>
    </div>
</section>
<!-----------bannar area close------>
<!---change password area---->
<section id="password-change">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="password-set">
                    <h2>Change Password</h2>
                    <form action="">
                        <div class="form-group">
                            <label>Current Password:</label><br>
                            <input type="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>New Password:</label><br>
                            <input type="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password:</label><br>
                            <input type="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <button class="btn change">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</section>
<!--change password area close-->



<div class="clearfix"></div>
@endsection
