<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Recover Password | Glow</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Multi Vendor Beauty Parlour System" name="Glow" />
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
    <!-- <div class="home-btn d-none d-sm-block">
        <a href="index.html" class="text-dark"><i class="fas fa-home h2"></i></a>
    </div> -->
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-soft-primary" style="background-color: black !important;">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-light"> Reset Password</h5>
                                        <p class="text-light">Re-Password with Fade.</p>
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
                                            <img src="{{asset('images/glow-small.png')}}" alt="Logo" class="rounded-circle" height="100%">
                                        </span>
                                    </div>
                                </a>
                            </div>

                            <div class="p-2">
                                @if(session('message'))
                                <div class="row mb-2">
                                    <div class="col-lg-12">
                                        <div class="alert alert-success" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="X">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            {{ session('message') }}
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="alert alert-success text-center mb-4" role="alert">
                                    Enter your Email and password will be sent to you!
                                </div>
                                @endif
                                <form class="form-horizontal" action="{{ route('forgot_pwd_mail')}}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="useremail">Email</label>
                                        <input type="email" class="form-control" id="useremail" name="email" placeholder="Enter email" required>
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-12 text-right">
                                            <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Reset</button>
                                        </div>
                                    </div>

                                </form>
                            </div>

                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <p>Remember It ? <a href="{{route('login')}}" class="font-weight-medium text-primary"> Sign In here</a> </p>
                        <p>© <script type="text/javascript">
                                var year = new Date();
                                document.write(year.getFullYear());
                            </script> Glow. Crafted with <i class="mdi mdi-heart text-danger"></i> by FADE</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="asset/libs/jquery/jquery.min.js"></script>
    <script src="asset/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="asset/libs/metismenu/metisMenu.min.js"></script>
    <script src="asset/libs/simplebar/simplebar.min.js"></script>
    <script src="asset/libs/node-waves/waves.min.js"></script>

    <!-- App js -->
    <script src="asset/js/app.js"></script>
</body>

</html>