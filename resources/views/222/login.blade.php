<!doctype html>

<html
  lang="en"
  class="layout-wide customizer-hide"
  data-assets-path="{{ url('dist-dashboard/assets') }}/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Masuk | {{ config('app.name') }}</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-favicon.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- endbuild -->

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/css/pages/page-auth.css') }}" />

    <style>
      :root {
        --bs-primary: #0033A0;
        --bs-primary-rgb: 0, 51, 160;
        --bs-primary-bg-subtle: #E8EEF9;
        --bs-link-color: #0033A0;
        --bs-link-hover-color: #0A1F44;
        --bs-warning: #C8DC00;
        --bs-warning-rgb: 200, 220, 0;
      }

      body {
        background: linear-gradient(135deg, #0A1F44 0%, #0033A0 58%, #4C7DF0 100%);
      }

      .authentication-wrapper.authentication-basic .authentication-inner::before,
      .authentication-wrapper.authentication-basic .authentication-inner::after {
        display: none;
      }

      .card {
        border-top: 0;
      }

      .app-brand-text,
      .text-primary {
        color: #0033A0 !important;
      }

      .auth-logo {
        display: block;
        width: 4.5rem;
        height: 4.5rem;
        object-fit: contain;
        filter: brightness(0) saturate(100%) invert(17%) sepia(97%) saturate(2500%) hue-rotate(210deg) brightness(78%) contrast(110%);
      }

      .app-brand-logo.demo svg {
        display: none;
      }

      .btn-primary {
        --bs-btn-bg: #0033A0;
        --bs-btn-border-color: #0033A0;
        --bs-btn-hover-bg: #0A1F44;
        --bs-btn-hover-border-color: #0A1F44;
      }

      .form-control:focus,
      .input-group:focus-within .form-control,
      .input-group:focus-within .input-group-text {
        border-color: #4C7DF0;
        box-shadow: 0 0 0 .15rem rgba(76, 125, 240, .2);
      }

      .input-group-text {
        color: #0033A0;
      }

      .card .icon-base {
        color: #0033A0 !important;
      }

      .auth-home-link {
        color: #0033A0;
        font-size: .875rem;
        font-weight: 600;
        text-decoration: none;
      }

      .auth-home-link:hover {
        color: #0A1F44;
      }

      .auth-card {
        overflow: hidden;
      }

      .auth-visual {
        display: none;
      }

      @media (min-width: 768px) {
        .authentication-wrapper.authentication-basic .authentication-inner {
          max-width: 980px;
          max-inline-size: 980px;
        }

        .auth-card {
          display: grid;
          grid-template-columns: minmax(280px, .92fr) minmax(360px, 1.08fr);
          min-height: 610px;
          border-radius: 1.25rem;
        }

        .auth-visual {
          display: flex;
          min-height: 100%;
          padding: 2rem;
          align-items: flex-start;
          position: relative;
          overflow: hidden;
          color: #FFFFFF;
          background:
            linear-gradient(180deg, rgba(10, 31, 68, .54), rgba(0, 20, 63, .88)),
            url('https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=1000&q=85') center / cover;
        }

        .auth-visual::after {
          content: '';
          position: absolute;
          inset: 0;
          background: linear-gradient(135deg, rgba(0, 51, 160, .32), transparent 55%);
        }

        .auth-visual-logo {
          position: absolute;
          z-index: 1;
          top: 1.75rem;
          left: 1.75rem;
          width: 7rem;
          height: 7rem;
          object-fit: contain;
          filter: brightness(0) invert(1);
        }

        .auth-visual-name {
          position: absolute;
          z-index: 1;
          top: 9.25rem;
          left: 1.75rem;
          color: #FFFFFF;
          font-size: 1.35rem;
          font-weight: 800;
          letter-spacing: -.04em;
        }

        .auth-card > .card-body {
          display: flex;
          flex-direction: column;
          justify-content: center;
          padding: 3.5rem clamp(2rem, 5vw, 4.5rem) !important;
        }

        .auth-card .app-brand {
          justify-content: flex-start !important;
          margin-bottom: 2rem !important;
        }

        .auth-card .auth-logo {
          display: none;
        }

        .auth-card .app-brand-link {
          gap: 0 !important;
        }

        .auth-card .app-brand-text {
          font-size: 1.5rem;
        }
      }
    </style>

    <!-- Helpers -->
    <script src="{{ asset('dist-dashboard/assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="{{ asset('dist-dashboard/assets/js/config.js') }}"></script>
  </head>
  <body>
    
    <!-- Content -->
    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register -->
          <div class="card auth-card px-sm-6 px-0">
            <div class="auth-visual" aria-hidden="true">
              <img src="{{ asset('images/logo.png') }}" alt="" class="auth-visual-logo">
              <span class="auth-visual-name">{{ config('app.name') }}</span>
            </div>
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center">
                <a href="index.html" class="app-brand-link gap-2">
                  <span class="app-brand-logo demo">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="auth-logo">
                    <span class="text-primary">
                      <svg
                        width="25"
                        viewBox="0 0 25 42"
                        version="1.1"
                        xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink">
                        <defs>
                          <path
                            d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z"
                            id="path-1"></path>
                          <path
                            d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z"
                            id="path-3"></path>
                          <path
                            d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z"
                            id="path-4"></path>
                          <path
                            d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z"
                            id="path-5"></path>
                        </defs>
                        <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                            <g id="Icon" transform="translate(27.000000, 15.000000)">
                              <g id="Mask" transform="translate(0.000000, 8.000000)">
                                <mask id="mask-2" fill="white">
                                  <use xlink:href="#path-1"></use>
                                </mask>
                                <use fill="currentColor" xlink:href="#path-1"></use>
                                <g id="Path-3" mask="url(#mask-2)">
                                  <use fill="currentColor" xlink:href="#path-3"></use>
                                  <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                                </g>
                                <g id="Path-4" mask="url(#mask-2)">
                                  <use fill="currentColor" xlink:href="#path-4"></use>
                                  <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                                </g>
                              </g>
                              <g
                                id="Triangle"
                                transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                                <use fill="currentColor" xlink:href="#path-5"></use>
                                <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                              </g>
                            </g>
                          </g>
                        </g>
                      </svg>
                    </span>
                  </span>
                  <span class="app-brand-text demo text-heading fw-bold">{{ config('app.name') }}</span>
                </a>
              </div>
              <!-- /Logo -->
              <h4 class="mb-1">Selamat datang di {{ config('app.name') }}!</h4>
              <p class="mb-6">Masuk untuk melanjutkan reservasi servis motor Anda.</p>

              <form id="formAuthentication" class="mb-6" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-6">
                  <label for="email" class="form-label">Email or Username</label>
                  <input
                    type="text"
                    class="form-control @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    placeholder="Enter your email or username"
                    value="{{ old('email') }}"
                    required
                    autofocus />
                  @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                </div>
                <div class="mb-6 form-password-toggle">
                  <label class="form-label" for="password">Password</label>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password"
                      class="form-control @error('password') is-invalid @enderror"
                      name="password"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                      aria-describedby="password"
                      required />
                    <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                  </div>
                  @error('password')
                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                </div>
                <div class="mb-8">
                  <div class="d-flex justify-content-between">
                    <div class="form-check mb-0">
                      <input class="form-check-input" type="checkbox" name="remember" id="remember-me" value="1"
                        {{ old('remember') ? 'checked' : '' }} />
                      <label class="form-check-label" for="remember-me"> Remember Me </label>
                    </div>
                    <a href="{{ route('password.request') }}">
                      <span>Forgot Password?</span>
                    </a>
                  </div>
                </div>
                <div class="mb-6">
                  <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
                </div>
              </form>

              <a class="auth-home-link d-flex align-items-center justify-content-center mt-4" href="{{ url('/') }}">
                <i class="icon-base bx bx-arrow-back me-1" aria-hidden="true"></i>
                Kembali ke Beranda
              </a>

              <p class="text-center">
                <span>New on our platform?</span>
                <a href="{{ route('register') }}">
                  <span>Create an account</span>
                </a>
              </p>
            </div>
          </div>
          <!-- /Register -->
        </div>
      </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->

    <script src="{{ asset('dist-dashboard/assets/vendor/libs/jquery/jquery.js') }}"></script>

    <script src="{{ asset('dist-dashboard/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('dist-dashboard/assets/vendor/js/bootstrap.js') }}"></script>

    <script src="{{ asset('dist-dashboard/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('dist-dashboard/assets/vendor/js/menu.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->

    <script src="{{ asset('dist-dashboard/assets/js/main.js') }}"></script>

    <!-- Page JS -->

    <!-- Place this tag before closing body tag for github widget button. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    @include('partials.sweetalert')
  </body>
</html>
