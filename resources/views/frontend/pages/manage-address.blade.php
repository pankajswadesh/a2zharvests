@extends('frontend.layouts.frontendlayout')
@section('title')
    <title>A2Z Harvests : Manage Address</title>
@endsection
@section('content')
<!-----------bannar area start------>
<section id="breadcrumb">
    <div class="container">
        <div class="breadcrumb-area">
            <ul>
                <li><a href="{{url("/")}}">Home</a></li>
                <li><span>/</span></li>
                <li>Manage Address</li>

            </ul>
        </div>
    </div>
</section>
<!-----------bannar area close------>
<!--==================manange-address-area===============-->
<section id="deliviry-address">
    <h3>Delivery Address</h3>
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                @if(Session::has('error'))
                <div class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                       <strong>Error! </strong>{{Session::get('error')}}
                </div>
                @endif
                @if(!empty($shipping_details))
                <div class="address">
                    <h5 id="name">{{$shipping_details["name"]}}</h5>
                    <p id="address">{{$shipping_details["address"]}}</p>
                    @if($shipping_details["pincode"]!='')
                        <p>Pin code: <span id="pincode">{{$shipping_details["pincode"]}}</span></p>
                    @endif
                    @if($shipping_details["landmark"]!='')
                    <p>Landmark: <span id="landmark">{{$shipping_details["landmark"]}}</span></p>
                    @endif
                    <p>Email: <span id="email">{{$shipping_details["email"]}}</span></p>
                    <p>Phone: <span id="phone">{{$shipping_details["phone_no"]}}</span></p>
                </div>
                @else
                    <div class="address">
                        <h5 id="name">{{Auth::user()->user_name}}</h5>
                        <p id="address">{{Auth::user()->location}}</p>
                        <p>Pin code: <span id="pincode">N/A</span></p>
                        <p>Landmark : <span id="landmark">N/A</span></p>
                        <p>Email: <span id="email">{{Auth::user()->email}}</span></p>
                        <p>Phone: <span id="phone">{{Auth::user()->phone}}</span></p>
                    </div>
                @endif
            </div>
            <div class="col-sm-12 col-md-6">
                <button class="btn new" data-toggle="collapse" data-target="#new-add">+ Add New Address</button>
                <div class="add-new-address collapse" id="new-add">
                    <h5>Add New Address</h5>
                    <form id="shippingForm">
                        {{csrf_field()}}
                    <div class="form-group">
                        <label>Name <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" name="name" id="name1">
                    </div>
                    <div class="form-group">
                        <label>Email <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" name="email" id="email1">
                    </div>
                    <div class="form-group">
                        <label>Phone <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" name="phone_no" id="phone_no1">
                    </div>
                    <div class="form-group">
                        <label>Address <span style="color: red;">*</span></label>
                        <textarea class="form-control add" name="address" rows="3" id="address1" placeholder="Flat/House No./street">
                       </textarea>
                    </div>
                    <div class="form-group">
                        <label>Land mark</label>
                        <input type="text" class="form-control" name="landmark" id="landmark1" >
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" class="form-control" name="city" id="city1">
                    </div>
                    <div class="form-group">
                        <label>State</label>
                        <input type="text" class="form-control" name="state" id="state1">
                    </div>
                    <div class="form-group">
                        <label>Pin Code <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" name="pincode" id="pincode1">
                    </div>
                    <div class="form-group">
                        <button class="btn save" type="submit">Save And Continue</button>
                    </div>
                    </form>
                </div>
            </div>
           </div>
            <div class="row" style="float: right;">
                <div class="pro-btn">
                    <a href="{{route('checkout')}}" class="cstm-btn" style="padding: 11px 4px;">Checkout</a>
                </div>
            </div>
        </div>

</section>
<!--==================manange-address-area-end===============-->
<div class="clearfix"></div>
    @push('scripts')
        <script>
            $("#shippingForm").on("submit", function(event)
            {
                event.preventDefault();
                var url="{{route('saveShippingAddress')}}";
                var name = $("#name1").val();
                var email = $("#email1").val();
                var phone_no = $("#phone_no1").val();
                var address = $("#address1").val();
                var landmark = $("#landmark1").val();
                var city = $("#city1").val();
                var state = $("#state1").val();
                var pincode = $("#pincode1").val();
                $.ajax({
                    url: url,
                    type: "post",
                    data: {_token:'<?php echo csrf_token() ?>',name:name,email:email,phone_no:phone_no,
                        address:address,landmark:landmark,city:city,state:state,pincode:pincode},
                    success: function (resp)
                    {
                        console.log(resp.data);
                          if(resp.status=="success"){
                              $("#name").text(resp.data.name);
                              $("#email").text(resp.data.email);
                              $("#phone").text(resp.data.phone_no);
                              $("#address").text(resp.data.address);
                              $("#landmark").text(resp.data.landmark);
                              $("#pincode").text(resp.data.pincode);
                              $("#shippingForm").find('input').val('');
                              $("#shippingForm").find('textarea').text('');
                              $("#new-add").removeClass('show');
                              $(window).scrollTop(0);
                              toastr.success(resp.msg, {timeOut: 10000});
                          }else{
                              toastr.warning(resp.msg, {timeOut: 10000});
                          }
                    }
                });

            });
        </script>
    @endpush
@endsection
