@extends('frontend.layouts.frontendlayout')
@section('title')
    <title>A2Z Harvests : Contact Us</title>
@endsection
@section('content')
<!-----------bannar area start------>
<section id="banner-area">
    <div class="image">
        <img src="{{asset('/')}}/frontendtheme/images/about-pic2.jpg" alt="">
        <div class="pic-layer">
        </div>
        <div class="top-heading">
            <h3><a href="{{url('/')}}">Home</a><span>/</span>Contact Us</h3>
        </div>
    </div>
</section>
<!-----------bannar area close------>
<!-- contact area starts -->
<section id="contact">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <div class="form-area">
                    <h3>Get in touch</h3>
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
                    <form role="form" method="post" action="{{route("contactSubmit")}}">
                        {{csrf_field()}}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="name" name="name" value="{{old('name')}}" placeholder="Name*"
                                           required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="email" name="email" value="{{old('email')}}" placeholder="Email*"
                                           required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="mobile" name="phone" value="{{old('phone')}}"
                                           placeholder="Phone number*" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="subject" name="subject" value="{{old('subject')}}"
                                           placeholder="Subject">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                <textarea class="form-control custom-msg" id="message" name="message" placeholder="Message*"
                                          maxlength="140" rows="7">{{old('message')}}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" id="submit" name="submit" class="btn btn-primary">Send</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-5">
                <section id="contact-text">
                    <div class="contact-add-text">
                        <h3>Contact us</h3>
                        <ul class="address-list">
                            <li class="media">
                                <div class="media-left">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="media-body">
                                    <a href="#">{{$contact_page["address"]}}</a>
                                </div>
                            </li>
                            <li class="media">
                                <div class="media-left">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="media-body">
                                    <a href="#">{{$contact_page["email"]}}
                                    </a>
                                </div>
                            </li>
                            <li class="media">
                                <div class="media-left">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="media-body">
                                    <a href="#">{{$contact_page["phone"]}}
                                    </a>
                                </div>
                            </li>
                        </ul>
                        <ul class="social-links">
                            <li><a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fab fa-google-plus-g"></i></a></li>
                            <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                        </ul>
                    </div>
                </section>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</section>
<!-- contact area ends -->
<div class="clearfix"></div>
@endsection
