@extends('frontend.layouts.frontendlayout')
@section('title')
<title>A2Z Harvests : Order</title>
@endsection
@section('content')
<!-----------bannar area start------>
<section id="breadcrumb">
    <div class="container">
        <div class="breadcrumb-area">
            <ul>
                <li><a href="{{url('/')}}">Home</a></li>
                <li><span>/</span></li>
                <li>Order</li>
            </ul>
        </div>
    </div>
</section>
<!-----------bannar area close------>
<!--==============term-condition=============-->
<section id="term-condition-area">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <!--order confirmation-->
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tbody>
                        <tr>
                            <td style="padding: 10px 0 30px 0;">
                                <table align="center" border="0" cellpadding="0" cellspacing="0" width="595px" style="border: 1px solid #c8e1b6; border-collapse: collapse;height: 842px;">
                                    <tbody>
                                        <tr style="padding: 30px 20px;display: block;">
                                            <td class="esd-structure es-p10t es-p10b es-p20r es-p20l" align="center">
                                                <table cellpadding="0" cellspacing="0"  align="center" style="width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td style="text-align: center;width: 100%;">
                                                                <a href="{{url('/')}}">
                                                                    <img src="{{asset('/')}}frontendtheme/images/logo.png" alt="logo" style="width: 140px;">
                                                                </a>
                                                                @if($order_id!="")
                                                                <h2 style="font-size: 28px;font-weight: 800;color:#2b2b2b;font-family: serif;margin: 24px 0 4px 0;">Order Confirmation</h2>
                                                                <p style="font-size: 15px;font-weight:500;color: #505050;line-height: inherit;margin-bottom: 12px;">Thank for your order, Your order placed successfully.</p>
                                                                <p style="font-size: 15px;font-weight:500;color: #505050;line-height: inherit;margin-bottom: 12px;">We send an email to you about your order.</p>
                                                                <h5 style="font-size: 18px;font-weight: 600;color: #fb4f1b;border-bottom: 1px dashed #c8e1b6;padding-bottom: 32px;margin-bottom: 16px;font-family: sans-serif;">Order No: {{$order->order_id}}</h5>
                                                                @endif
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
                <!--order confirmation end-->
            </div>
        </div>
    </div>
</section>
<!--==============term-condition-end=============-->
<div class="clearfix"></div>
@endsection