<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Fasilkom Hosting | @yield("page_title")</title>
    <link rel="stylesheet" href="{{ asset('/dashboard_panel/css/bootstrap.min.css') }}">
    <link href="{{asset('/dashboard_panel/css/styles.css')}}" rel="stylesheet"/>
    <link href="{{asset('/dashboard_panel/dataTables/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet"/>
    <link href="{{asset('/dashboard_panel/fontAwesome/css/all.min.css')}}" rel="stylesheet" />
    <script src="{{asset('/dashboard_panel/fontAwesome/js/all.min.js')}}"></script>

    @yield('custom_css')
  </head>
  <body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
      <a class="navbar-brand" href="{{ route('dashboard') }}">Web Hosting</a><button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
      <!-- Navbar Search-->
      <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
        <div class="input-group">
          <input class="form-control" type="text" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" />
          <div class="input-group-append">
            <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
          </div>
        </div>
      </form>
      <!-- Navbar-->
      <ul class="navbar-nav ml-auto ml-md-0">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
            {{-- <a class="dropdown-item" href="#">Settings</a>
            <a class="dropdown-item" href="#">Activity Log</a> --}}
            {{-- <div class="dropdown-divider"></div> --}}
            <a class="dropdown-item" id="logout_link" href="{{ route('logout') }}">Logout</a>
          </div>
        </li>
      </ul>
    </nav>

    <div id="layoutSidenav">
      <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
          <div class="sb-sidenav-menu">
            <div class="nav">
              @yield('side_menu')

              <div class="sb-sidenav-menu-heading">Core</div>
              <a class="nav-link" href="{{ route('dashboard') }}"><div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                  Dashboard</a>
            </div>
          </div>
          <div class="sb-sidenav-footer">
            <div class="small">Logged in as: User</div>
            {{-- Start Bootstrap --}}
          </div>
        </nav>
      </div>

      <div id="layoutSidenav_content">
        @yield('content')

        <footer class="py-4 bg-light mt-auto">
          <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between small">
              <div class="text-muted">Copyright &copy; <script>document.write(new Date().getFullYear());</script></div>
              {{-- <div>
                <a href="#">Privacy Policy</a>
                &middot;
                <a href="#">Terms &amp; Conditions</a>
              </div> --}}
            </div>
          </div>
        </footer>
      </div>
    </div>

    <script src="{{asset('/js/jquery-3.4.1.min.js')}}"></script>
    <script src="{{asset('/dashboard_panel/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('/dashboard_panel/js/scripts.js')}}"></script>
    <script src="{{asset('/dashboard_panel/dataTables/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('/dashboard_panel/dataTables/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{asset('/dashboard_panel/assets/demo/datatables-demo.js')}}"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="{{asset('/dashboard_panel/assets/demo/chart-area-demo.js')}}"></script>
    <script src="{{asset('/dashboard_panel/assets/demo/chart-bar-demo.js')}}"></script> --}}
    @yield('script')
  </body>
</html>
