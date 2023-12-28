<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>:- Paasword Recovery Message -:</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:400,400i,700,700i" rel="stylesheet">

    <!-- Bootstrap -->
    <style>
        body{font-family: 'Roboto Condensed', sans-serif;padding: 0;margin: 0;}
        img{width: 75px;}
        table{width: 700px;margin: 50px auto;border: 1px solid #cccccc;}
        table thead>tr>th{text-align: center;padding: 5px;}
        table thead>tr>th.headline{font-weight: 600;border-bottom: 1px solid #cccccc;color: #191919;
            border-top: 1px solid #cccccc;font-size: 12px;}
        table tbody>tr>td{text-align: center;}
        table .price{text-align: right;}
        table thead>tr.bdr{border-bottom: 1px solid #000;}
        table .mrgn-lft{text-align: left;font-weight: 100;font-size: 14px;}
    </style>
</head>
<body>
<h4 style="text-align:center"> Hello,{{$name}} You Have Recover Paasword Message.</h4>
<table class="table table-bordered">
    <thead>
    <tr>
        <th class="mrgn-lft" colspan="3"> One Time Pasword</th>

    </tr>

    <tr>
         <th class="mrgn-lft" colspan="3">OTP :- {{$otp }}</th>
    </tr>

    </thead>

</table>
</body>
</html>
