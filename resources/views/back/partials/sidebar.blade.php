<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="active">
                    <a href="/dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="list-divider"></li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-bus-alt"></i>
                        <span>Compagnie</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="
                            {{ route('compagnies.index') }}
                            ">Tous les compagnies</a></li>
                        <li><a href="
                            {{ route('compagnies.create') }}
                             ">Ajouter une compagnie</a></li>
                    </ul>
                </li>
                {{-- @can('admin-access') --}}
                    <li class="submenu">
                        <a href="#">
                            <i class="fas fa-user-shield"></i>
                            <span>Permissions</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="submenu_class" style="display: none;">
                            <li><a href="
                                 {{ route('permissions.index') }} 
                                 ">Toutes les Permissions</a></li>
                            <li><a href="
                                {{ route('permissions.create') }}
                                ">Ajouter une Permission</a></li>
                        </ul>
                    </li>


                    <li class="submenu">
                        <a href="#">
                            <i class="fas fa-users-cog"></i>
                            <span>Utilisateur</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="submenu_class" style="display: none;">
                            <li><a href="
                                {{ route('users.index') }}
                                 ">Tout les utilisateur</a></li>
                            <li><a href="
                                {{ route('users.create') }}
                                 ">Ajouter un utilisateur</a></li>
                        </ul>
                    </li>
                {{-- @endcan --}}

                <li>
                    <a href="
                    {{-- {{ route('') }} --}}
                     ">
                        <i class="fas fa-sticky-note"></i>
                        <span>Notes</span>
                    </a>
                </li>
                <li>
                    <a href="
                    {{-- {{ route('') }} --}}
                     ">
                        <i class="fe fe-table"></i>
                        <span>Notifications</span>
                    </a>
                </li>
                <li>
                    <a href="
                    {{-- {{ route('') }} --}}
                     ">
                        <i class="fas fa-money-check-alt"></i>
                        <span>Paiements</span>
                    </a>
                </li>
                {{-- @can('admin-access') --}}
                    <li class="submenu">
                        <a href="#">
                            <i class="fas fa-warehouse"></i>
                            <span>Garres</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="submenu_class" style="display: none;">
                            <li><a href="
                                {{-- {{ route('') }} --}}
                                 ">Tous les Garres</a></li>
                            <li><a href="
                                {{-- {{ route('') }} --}}
                                 ">Ajouter une Garre</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                        <a href="#">
                            <i class="far fa-money-bill-alt"></i>
                            <span>Profils</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="submenu_class" style="display: none;">
                            <li><a href="{{ route('profils.index') }}">Tous les Profils</a></li>
                            <li><a href="{{ route('profils.create') }}">Ajouter un Profil</a></li>
                        </ul>
                    </li>
                     <li class="submenu">
                        <a href="#">
                            <i class="	fas fa-route"></i>
                            <span>Voyages</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="submenu_class" style="display: none;">
                            <li><a href="
                                {{ route('voyages.index') }}
                                ">Tous les Voyages</a></li>
                            <li><a href="
                                {{ route('voyages.create') }}
                                 ">Ajouter un voyage</a></li>
                        </ul>
                    </li>
                     </li>
                     <li class="submenu">
                        <a href="#">
                            <i class="fas fa-bus"></i>
                            <span>Buses</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="submenu_class" style="display: none;">
                            <li><a href="
                                {{ route('buses.index') }}
                                ">Tous les Buses</a></li>
                            <li><a href="
                                {{ route('buses.create') }}
                                 ">Ajouter un Buse</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                        <a href="#">
                            <i class="fas fa-map-marked-alt"></i>
                            <span>Trajets</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="submenu_class" style="display: none;">
                            <li><a href="
                                {{ route('trajets.index') }}
                                ">Tous les Trajets</a></li>
                            <li><a href="
                                {{ route('trajets.create') }}
                                 ">Ajouter un Trajets</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                        <a href="#">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Frequences</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="submenu_class" style="display: none;">
                            <li><a href="
                                {{ route('frequences.index') }}
                                ">Tous les Frequences</a></li>
                            <li><a href="
                                {{ route('frequences.create') }}
                                 ">Ajouter une Frequence</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                        <a href="#">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Tickets</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="submenu_class" style="display: none;">
                            <li><a href="
                                {{ route('tickets.index') }}
                                ">Tous les Tickets</a></li>
                            <li><a href="
                                {{ route('tickets.create') }}
                                 ">Ajouter un Ticket</a></li>
                        </ul>
                    </li>


                    <li>
                        <a href="
                        {{-- {{ route('') }} --}}
                         ">
                            <i class="fas fa-envelope"></i>
                            <span>Contacts</span>
                        </a>
                    </li>


                    <li>
                        <a href="
                        {{-- {{ route('') }} --}}
                         ">
                            <i class="fas fa-cog"></i>
                            <span>Paramètres</span>
                        </a>
                    </li>
                {{-- @endcan --}}


            </ul>
        </div>
    </div>
</div>
