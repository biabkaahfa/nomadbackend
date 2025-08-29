@auth
    <!-- tout ton code header ici -->

    <div class="header">
        @if(isset($theme) && $theme->logo)
            <img src="{{ asset('storage/' . $theme->logo) }}" alt="Logo compagnie" width="80" height="80">

        @endif

        @if(isset($theme) && $theme->slogan)
            <span class="text-dark ms-2">{{ $theme->slogan }}</span>
        @endif

        <div class="header-left">
            <a href="{{ route('profile.edit', ['profile' => Auth::user()->id]) }}" class="logo">
                <img src="{{ Auth::user()->image ? asset('storage/' . Auth::user()->image) : asset('back_auth/assets/img/logo.png') }}"
                     width="50" height="70" alt="logo4" />
                <span class="logoclass">{{ Auth::user()->name ?? "non connecter" }}</span>
            </a>
            <a href="index.html" class="logo logo-small">
                {{-- <img src="{{ asset('back_auth/assets/img/logo.png') }}"
                     alt="logo3" width="30" height="30" /> --}}
            </a>
        </div>

        <a href="javascript:void(0);" id="toggle_btn">
            <i class="fe fe-text-align-left"></i>
        </a>

        <a class="mobile_btn" id="mobile_btn">
            <i class="fas fa-bars"></i>
        </a>

        <ul class="nav user-menu">
            <li class="nav-item dropdown has-arrow">
                <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="user-img">
                        <img class="rounded-circle"
                             src="{{ Auth::user()->image ? asset('storage/' . Auth::user()->image) : asset('back_auth/assets/img/logo.png') }}"
                             width="31" alt="image profil" />

                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="user-header">
                        <div class="avatar avatar-sm">
                            <img src="{{ Auth::user()->image ? asset('storage/' . Auth::user()->image) : asset('back_auth/assets/img/logo.png') }}"
                                 alt="User Image" class="avatar-img rounded-circle">
                        </div>
                        <div class="user-text">
                            <h6>{{ Auth::user()->name }}</h6>
                            <p class="text-muted mb-0">
                                {{ Auth::user()->profil->name }}
                            </p>
                        </div>
                    </div>
                    <a class="dropdown-item" href="{{ route('profile.edit', ['profile' => Auth::user()->id]) }}">Profile</a>
                    <a class="dropdown-item" href="">
                        {{-- {{ route('Setting.index') }} --}}
                        Paramètres
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn dropdown-item">Déconnexion</button>
                    </form>
                </div>
            </li>
        </ul>

        <div class="top-nav-search">
            <form>
                <input type="text" class="form-control" placeholder="Search here" />
                <button class="btn" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
@else
    <script>
        // window.location.href = "{{ route('login') }}";
    </script>
@endauth
