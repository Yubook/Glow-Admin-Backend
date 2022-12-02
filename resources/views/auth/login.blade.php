<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Login | Glow</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Multi Vendor Beauty Parlour" name="Glow parlour" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon-32x32.png')}}">

    <!-- Bootstrap Css -->
    <link href="{{ asset('css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('css/app.css')}}" id="app-style" rel="stylesheet" type="text/css" />

    <link href="{{ asset('css/custom.css')}}" id="app-style" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-soft-primary" style="background-color: #723E3D !important;">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-light">Welcome Admin !</h5>
                                        <p class="text-light">Sign in to continue to Glow.</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{asset('images/profile-img.png')}}" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div>
                                <a href="#">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{asset('images/glow-small.png')}}" alt="Logo" class="rounded-circle">
                                        </span>
                                    </div>
                                </a>
                            </div>
                            <div class="">
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <div class="form-group">
                                        <label for="useremail">Email</label>
                                        <input type="email" class="form-control" name="email" id="useremail" placeholder="Enter email" value="{{ old('email', null) }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="userpassword">Password</label>
                                        <input type="password" class="form-control" name="password" id="userpassword" placeholder="Enter password" required>
                                        <i class="far fa-eye" id="togglePassword" style="margin-top:-25px;cursor:pointer;float:right;margin-right:10px;"></i>
                                        <script>
                                            const togglePassword = document.querySelector('#togglePassword');
                                            const password = document.querySelector('#userpassword');
                                            togglePassword.addEventListener('click', function(e) {
                                                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                                                password.setAttribute('type', type);
                                                this.classList.toggle('fa-eye-slash');
                                            });
                                        </script>
                                    </div>

                                    <!--  <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="customControlInline">
                                        <label class="custom-control-label" for="customControlInline">Remember me</label>
                                    </div> -->

                                    @if($errors->has('email'))
                                    <span class="" role="alert" style="color: #ff0033;">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif

                                    @if($errors->has('password'))
                                    <span class="" role="alert" style="color: #ff0033;">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @enderror
                                    <div class="mt-3">
                                        <button class="btn btn-primary btn-block waves-effect waves-light" type="submit" style="background-color: #723E3D;">Log In</button>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <a href="{{route('pwd_recovery')}}" class="text-primary"><i class="mdi mdi-lock mr-1"></i> Forgot your password?</a>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <!-- <p>Don't have an account ? <a href="auth-register.html" class="font-weight-medium text-primary"> Signup now </a> </p> -->
                        <p>©
                            <script type="text/javascript">
                                var year = new Date();
                                document.write(year.getFullYear());
                            </script> GLOW. Crafted with <i class="mdi mdi-heart text-danger"></i> by Multipz
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('libs/jquery/jquery.min.js')}}"></script>
    <script src="{{ asset('libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('libs/metismenu/metisMenu.min.js')}}"></script>
    <script src="{{ asset('libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{ asset('libs/node-waves/waves.min.js')}}"></script>

    <!-- App js -->
    <script src="{{ asset('js/app.js')}}"></script>
</body>

</html>