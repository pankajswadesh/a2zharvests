@extends('frontend.layouts.frontendlayout')
@section('title')
<title>A2Z Harvests : Delivery Slots</title>
@endsection
@section('content')
<!-----------bannar area start------>
<section id="breadcrumb">
    <div class="container">
        <div class="breadcrumb-area">
            <ul>
                <li><a href="{{url('/')}}">Home</a></li>
                <li><span>/</span></li>
                <li>Delivery Slots</li>
            </ul>
        </div>
    </div>
</section>
<!-----------bannar area close------>
<!-- -----------date time slot page start-------- -->
<section class="date-time-slot">
<form action="{{ route('pay_order_payment')}}" method="POST" enctype="multipart/form-data" id="pay_order_amount_form">
{!! csrf_field() !!}
    <div class="container">
        <div class="time-slot-area">
            @if($data["showSlot"])
            <div class="area-1">
                <h5>Choose Delivery Slot For This</h5>
                <div class="box-area">
                    <ul>
                        @for($i=0;$i<count($data["data"]);$i++) <li>
                            <a href="javascript:setDate('{{$data["data"][$i]["date"]}}','{{$i}}');" id="slot_{{$i}}" class="date">
                                <span>{{$data["data"][$i]["date"]}}</span>
                                <span>{{$data["data"][$i]["day"]}}</span>
                            </a>
                            </li>
                            @endfor
                    </ul>
                </div>
            </div>
            <div class="area-2">
                <h5>Choose Delivery Slot For This</h5>
                <div class="box-area">
                    <ul>
                        <li>
                            <a href="javascript:setTime('09 AM - 10 AM','1');" id="time_1" class="time">09 AM - 10 AM</a>
                        </li>
                        <li>
                            <a href="javascript:setTime('10 AM - 02 PM','2');" id="time_2" class="time">10 AM - 02 PM</a>
                        </li>
                        <li>
                            <a href="javascript:setTime('03 PM - 09 PM','3');" id="time_3" class="time">03 PM - 09 PM</a>
                        </li>

                        <li>
                            <a href="javascript:setTime('Full Day','4');" id="time_4" class="time">Full Day</a>
                        </li>
                    </ul>
                </div>
            </div>
            @endif
            <div class="area-2">
                <h5>Choose Payment Method</h5>
                <div class="box-area">
                    <ul>
                        <li>
                            <a href="javascript:setMethod('cod');" id="cod" class="payment active">Cod</a>
                        </li>
                        <li>
                            <a href="javascript:setMethod('online');" id="online" class="payment">Online</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="button-area">
            <input type="hidden" name="deliveryDate" id="order_delivery_date" value="" class="form-control">
            <input type="hidden" name="deliverySlot" id="order_delivery_slot" value="" class="form-control">
            <input type="hidden" name="amount" value="{{$pay_amount}}" class="form-control">
            <!-- <a href="javascript:void(0);" id="place_order" class="btn">Place Order</a> -->
            <button type="submit" id="place_order" class="btn">Place Order</button>
        </div>
    </div>
</form>
</section>
<!-- -----------date time slot page close-------- -->
<div class="clearfix"></div>
@push('scripts')
@if(env('PAYTM_ENVIRONMENT')=='production')
<script type="application/javascript" crossorigin="anonymous" src="https:\\securegw.paytm.in\merchantpgpui\checkoutjs\merchants\<?php echo env('PAYTM_MERCHANT_ID') ?>.js"></script>
@else
<script type="application/javascript" crossorigin="anonymous" src="https:\\securegw-stage.paytm.in\merchantpgpui\checkoutjs\merchants\<?php echo env('PAYTM_MERCHANT_ID') ?>.js"></script>
@endif
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var payment_method = 'cod';
    var use_wallet = false;
    var only_wallet = false;
    var slotDate = '';
    var slotTime = '';
    var pay_amount = "{{$pay_amount}}";

    function setDate(date, index) {
        $(".date").removeClass('active');
        slotDate = date;
        $('#order_delivery_date').val(slotDate);
        $("#slot_" + index).addClass('active');
    }

    function setTime(time, index) {
        $(".time").removeClass('active');
        slotTime = time;
        $('#order_delivery_slot').val(slotTime);
        $("#time_" + index).addClass('active');
    }

    function setMethod(pay) {
        $(".payment").removeClass('active');
        payment_method = pay;
        $("#" + pay).addClass('active');
    }
    $("#place_order").on("click", function(e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            url: "{{route('checkSlot')}}",
            data: {
                _token: '<?php echo csrf_token(); ?>',
                deliveryDate: slotDate,
                deliverySlot: slotTime
            },
            success: function(data) {
                console.log(data);
                if (data.status == "error") {
                    $("#msg").html('<h3 style="color:#0a7007">' + data.msg + '</h3>');
                    toastr.warning(data.msg, {
                        timeOut: 2000
                    });
                } else {
                    $("#loader").show();
                    $("#place_order").attr("disabled", true);
                    if (payment_method == "online") {
                        $("#pay_order_amount_form").submit();
                    } else {
                        $.ajax({
                            type: "post",
                            url: "{{route('place_order')}}",
                            data: {
                                _token: '<?php echo csrf_token(); ?>',
                                payment_method: payment_method,
                                deliveryDate: slotDate,
                                deliverySlot: slotTime,
                                use_wallet: use_wallet,
                                only_wallet: only_wallet
                            },
                            success: function(res) {
                                if (res.status == "success") {
                                    toastr.success(res.msg, {
                                        timeOut: 2000
                                    });
                                    $("html, body").animate({
                                        scrollTop: 0
                                    }, "slow");
                                    setTimeout(function() {
                                        window.location.href ="{{route('orderConfirmation', '')}}"+"/"+res.data.order_id;
                                    }, 1000);
                                } else {
                                    $("#msg").html('<h3 style="color:#0a7007">' + res.msg + '</h3>');
                                    toastr.warning(res.msg, {
                                        timeOut: 2000
                                    });
                                }
                                $("#loader").hide();
                                $("#place_order").attr("disabled", false);
                            }
                        });
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection