@extends('layouts.web_master')

@section('content')
    <section class="login-inner">
        <div class="container">
            <div class="login-inner-box">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-6">
                        <div class="login-inner-box-left">
                            <div class="login-inner-box-left-round"></div>

                            <div class="login-logo"></div>

                            <h2>SIGN UP</h2>

                            <form  role="form" data-action={{ route("web-user-register") }} id="adminFrm" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-12">
                                        <div class="form-group">
                                            <input type="text" name="first_name" class="form-control" placeholder="Enter Your First Name*"
                                                id="first_name" required="" data-error="Please enter your first name">
                                            <div class="form-icon"><i class="fa fa-user" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-12">
                                        <div class="form-group">
                                            <input type="text" name="last_name" class="form-control" placeholder="Enter Your Last Name*"
                                                id="last_name" required="" data-error="Please enter your last name">
                                            <div class="form-icon"><i class="fa fa-user" aria-hidden="true"></i> </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-12">
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control" placeholder="Email"
                                                id="email" required="" data-error="Enter Your Email ID*">
                                            <div class="form-icon"><i class="fa fa-envelope-o" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-12">
                                        <div class="form-group">
                                            {{-- <label>Select Country<sup>*</sup></label> --}}
                                            {{-- <input type="text" name="country" class="form-control" placeholder="Country*"
                                                id="country" required="" data-error="text"> --}}
                                                <select placeholder="Select Country" class="form-control requiredCheck" name="country_id" id="exampleFormControlSelect1" data-error="Select Country">
                                                    <option value="">Select Country</option>
                                                     @if(isset($countryList))
                                                      @foreach ($countryList as $key => $value)
                                                      <option value="{{$value->id}}" {{(isset($data) && $data->country_id == $value->id)?'selected':''}}>
                                                      {{$value->name}}
                                                      </option>
                                                       @endforeach
                                                      @else
                                                      <option value="">No Country Available!</option>
                                                    @endif
                                                  </select>
                                            {{-- <div class="form-icon"><i class="fa fa-building-o" aria-hidden="true"></i>
                                            </div> --}}
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-12">
                                        <div class="form-group">
                                            <input type="number" name="phone" class="form-control"
                                                placeholder="Enter Your Phone No." id="phone" required=""
                                                data-error="Please enter your phone number">
                                            <div class="form-icon"><i class="fa fa-mobile" aria-hidden="true"></i></i>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-sm-12 col-12">
                                            <button class="default-button disabled submit-now" type="submit"
                                                style="pointer-events: all; cursor: pointer;"><span>SUBMIT
                                                    NOW</span></button>
                                            <div id="msgSubmit" class="h6 text-center hidden"></div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-6">
                        <div class="login-inner-box-right">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
