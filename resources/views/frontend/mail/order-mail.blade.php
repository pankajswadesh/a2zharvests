<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>:: A2Z Harvests Order mail ::</title>
    <!-- favicon icon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('/')}}frontendtheme/images/favicon.ico">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <!-- bootstrap css -->
    <link href="{{asset('/')}}frontendtheme/css/plugins/bootstrap.min.css" rel="stylesheet">
    <!-- fontawesome css -->
    <link href="{{asset('/')}}frontendtheme/css/plugins/fontawesome.min.css" rel="stylesheet">
    <!-- animate css -->
    <link href="{{asset('/')}}frontendtheme/css/plugins/animate.css" rel="stylesheet">
    <!-- slick css -->
    <link href="{{asset('/')}}frontendtheme/css/plugins/slick.css" rel="stylesheet">
    <link href="{{asset('/')}}frontendtheme/css/plugins/slick-theme.css" rel="stylesheet">
    <!-- google font -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">    <!-- custom style css -->
    <link href="{{asset('/')}}frontendtheme/css/style.css" rel="stylesheet">
    <!-- responsive css -->
    <link href="{{asset('/')}}frontendtheme/css/responsive.css" rel="stylesheet">
    <!-- modernizr js -->
    <script src="{{asset('/')}}frontendtheme/js/plugins/modernizr.js"></script>
    <!--[if lt IE 9]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a
            href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<!--order mail-->
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
    <tr>
        <td style="padding: 10px 0 30px 0;">
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="595px"
                   style="border: 1px solid #c8e1b6; border-collapse: collapse;height: 842px;">
                <tbody>
                <tr style="padding: 30px 20px;display: block;">
                    <td class="esd-structure es-p10t es-p10b es-p20r es-p20l" align="left">
                        <table cellpadding="0" cellspacing="0" class="es-left" align="left" style="width: 100%;">
                            <tbody>
                            <tr>
                                <td style="text-align: center;width: 100%;">
                                    <img src="{{asset('/')}}frontendtheme/images/logo.png" alt="logo" style="width: 140px;">
                                    <h2 style="font-size: 22px;text-transform:uppercase;font-weight: 800;color:#2b2b2b;font-family: serif;margin: 24px 0 12px 0;">Thank you for your order</h2>
                                    <p style="font-size: 20px;font-weight:600;color:#2b2b2b;font-family: serif;line-height: inherit;margin-bottom: 0;">Order No: <span style="text-transform: uppercase;letter-spacing: 1px;">{{$order->order_id}}</span></p>
                                    <hr style="border-style: dashed;border-color: #c8e1b6;margin: 30px 0;">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <table cellpadding="0" cellspacing="0" class="es-left" align="left"
                               style="width: 100%;">
                            <tbody>
                            <tr>
                                <td>
                                    <h4 style="font-size: 22px;font-weight: 600;font-family: serif;color: #2b2b2b;margin: 0 0 20px 0;">Order Summary</h4>
                                </td>
                                <td>
                                    <p style="text-align:end;font-size: 12.4px;font-weight:500;color: #a7a7a7;line-height: inherit;margin: 0 0 20px 0;letter-spacing: 2px;"> {{$order->datetime}}</p>
                                </td>
                            </tr>
                            @php $total =0; @endphp
                            @foreach($order->order_details as $row)
                                @php $total = $total + $row->gross_price @endphp
                            <tr style="background-color: #f6ffef;padding: 24px 16px;border: 1px dashed #c8e1b6;border-bottom: none;">
                                <td style="width: 80%;background-color: #f6ffef;padding: 16px;display: flex;justify-content: left;align-items: center;">
                                    <div style="width: 88px;">
                                        <img src="{{$row->product->product_image}}" alt="" style="width: 88px;border: 1px dashed #c8e1b6;">
                                    </div>
                                    <div style="padding-left: 16px;">
                                        <h4 style="font-size: 18px;font-weight: 600;color: #2b2b2b;margin-bottom: 12px;font-family: serif;text-transform: uppercase;">{{$row->product->product_name}}<span style="font-size: 15px;font-family: sans-serif;margin-left: 4px;">x {{$row->qty}}</span></h4>
                                        {{--<h5 style="font-size: 14px;font-weight: 600;color: #777777;margin-bottom: 12px;font-family: sans-serif;text-transform: uppercase;">Combo Pack</h5>--}}
                                    </div>
                                </td>
                                <td style="width: 20%;padding: 16px;text-align: end;">
                                    <h5 style="font-size: 16px;font-weight: 600;color: #05a536;margin-bottom: 8px;font-family: sans-serif;">₹ {{$row->gross_price}}</h5>
                                </td>
                            </tr>
                            @endforeach
                            <tr style="background-color: #f6ffef;padding: 24px 16px;border: 1px dashed #c8e1b6;">
                                <td style="width: 80%;background-color: #f6ffef;padding: 16px;">
                                    <h5 style="font-size: 17px;font-weight: 600;color: #2b2b2b;margin-bottom: 16px;font-family: sans-serif;">Subtotal ({{$order->order_details->count()}} items)</h5>
                                    <h5 style="font-size: 17px;font-weight: 600;color: #2b2b2b;margin-bottom: 16px;font-family: sans-serif;">Delivery charge</h5>
                                    <h5 style="font-size: 17px;font-weight: 600;color: #fb4f1b;margin-bottom: 16px;font-family: sans-serif;">Total</h5>
                                </td>
                                <td style="width: 20%;padding: 16px 16px 16px 8px;text-align: end;">
                                    <h5 style="font-size: 16px;font-weight: 600;color: #2b2b2b;margin-bottom: 16px;font-family: sans-serif;">₹ {{$total}}</h5>
                                    <h5 style="font-size: 16px;font-weight: 600;color: #2b2b2b;margin-bottom: 16px;font-family: sans-serif;">₹ {{$order->delivery_charge}}</h5>
                                    <h5 style="font-size: 16px;font-weight: 600;color: #fb4f1b;margin-bottom: 16px;font-family: sans-serif;">₹ {{$order->gross_amount}}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 80%;">
                                    <h4 style="font-size: 22px;font-weight: 600;font-family: serif;color: #2b2b2b;margin: 20px 0;">Shipping Address</h4>
                                </td>
                            </tr>
                            <tr style="background-color: #f6ffef;padding: 24px 16px;border: 1px dashed #c8e1b6;">
                                <td style="width: 80%;background-color: #f6ffef;padding: 16px;">
                                    <h5 style="font-size: 16px;font-weight: 600;color: #2b2b2b;margin: 0;font-family: sans-serif;line-height: 26px;">
                                        {{$order->shipping->name}},{{$order->shipping->phone_no}}<br>
                                        {{$order->shipping->address}},{{$order->shipping->landmark}}, {{$order->shipping->pincode}}
                                    </h5>
                                </td>
                                <td style="width: 20%;padding: 16px 16px 16px 8px;text-align: end;"></td>
                            </tr>
                            </tbody>
                        </table>
                        <table cellpadding="0" cellspacing="0" class="es-left" align="left"
                               style="width: 100%;">
                            <tbody>
                            <tr>
                                <td style="text-align: center;width: 100%;padding: 30px 0;">
                                    <p style="font-size: 15px;font-weight:600;color: #505050;line-height: 24px;margin-bottom: 16px;">if you need help with anything please don't hesitate to drop us an <br> email: <a href="#" style="color: #fb4f1b;font-weight: 600;">info@a2zharvests.com</a> </p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <table cellpadding="0" cellspacing="0" class="es-left" align="left"
                               style="width: 100%;">
                            <tbody>
                            <tr>
                                <td style="text-align: center;">
                                    <p style="font-size: 15px;font-weight:600;color: #2b2b2b;margin: 0;">© Copyright . All Rights Reserved</p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<!--order mail end-->
</body>
</html>