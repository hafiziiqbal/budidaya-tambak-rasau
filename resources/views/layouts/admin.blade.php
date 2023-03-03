<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title ?? env('APP_NAME', null) }}</title>

    {{-- font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

    {{-- style --}}
    <link rel="stylesheet" href="{{ asset('/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">

    {{-- fontawesome --}}
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    {{-- datatables --}}
    <link rel="stylesheet" href="{{ asset('/vendor/datatables/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/datatables/css/responsive.bootstrap.min.css') }}">

    {{-- select2 --}}
    <link rel="stylesheet" href="{{ asset('/vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/select2/css/select2-bootstrap-5-theme.min.css') }}">

    {{-- datepicker --}}
    <link rel="stylesheet" href="{{ asset('/vendor/datapicker/bootstrap-datepicker.min.css') }}">
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="index.html">Admin Panel</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>

        <!-- Navbar-->
        <ul class="navbar-nav ms-auto me-0 me-md-3 my-2 my-md-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#!">Settings</a></li>
                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="{{ route('logout') }}">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Beranda</div>
                        <a class="nav-link {{ $title == 'DASHBOARD' ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>

                        <div class="sb-sidenav-menu-heading">Produk Kami</div>
                        <a class="nav-link {{ $title == 'PRODUK' || $title == 'TAMBAH PRODUK' || $title == 'EDIT PRODUK' ? 'active' : '' }}"
                            href="{{ route('produk') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-briefcase"></i></div>
                            Produk
                        </a>

                        <div class="sb-sidenav-menu-heading">Transaksi Kami</div>
                        <a class="nav-link {{ isset($transaksi_toogle) ? 'active' : 'collapsed' }}" href="#"
                            data-bs-toggle="collapse" data-bs-target="#collapseTransaksi" aria-expanded="false"
                            aria-controls="collapseTransaksi">
                            <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                            Transaksi
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse {{ isset($transaksi_toogle) ? 'show' : '' }}" id="collapseTransaksi"
                            aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link {{ $title == 'PEMBELIAN' || $title == 'TAMBAH PEMBELIAN' || $title == 'EDIT PEMBELIAN' ? 'active' : '' }}"
                                    href="{{ route('pembelian') }}">Pembelian</a>

                            </nav>
                        </div>


                        <div class="sb-sidenav-menu-heading">Database Kami</div>
                        <a class="nav-link {{ isset($masterdata_toogle) ? 'active' : 'collapsed' }}" href="#"
                            data-bs-toggle="collapse" data-bs-target="#collapseMasterData" aria-expanded="false"
                            aria-controls="collapseMasterData">
                            <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                            Master Data
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse {{ isset($masterdata_toogle) ? 'show' : '' }}" id="collapseMasterData"
                            aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link {{ $title == 'SUPPLIER' || $title == 'TAMBAH SUPPLIER' || $title == 'EDIT SUPPLIER' ? 'active' : '' }}"
                                    href="{{ route('supplier') }}">Supplier</a>
                                <a class="nav-link {{ $title == 'KATEGORI' || $title == 'TAMBAH KATEGORI' || $title == 'EDIT KATEGORI' ? 'active' : '' }}""
                                    href="{{ route('kategori') }}">Kategori</a>
                            </nav>
                        </div>


                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    {{ auth()->user()->name }}
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    @yield('content')
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; {{ env('APP_NAME') }} 2023</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('/js/script.js') }}"></script>
    <script src="{{ asset('/vendor/jquery/jquery-3.5.1.js') }}"></script>
    <script src="{{ asset('/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/vendor/datatables/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('/vendor/datatables/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('/vendor/datatables/js/responsive.bootstrap.min.js') }}"></script>
    <script src="{{ asset('/vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('/vendor\datapicker\bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('/js/custom.js') }}"></script>
    <script>
        function getCookie(cname) {
            let name = cname + "=";
            let ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }
    </script>
    @stack('script')
</body>

</html>
