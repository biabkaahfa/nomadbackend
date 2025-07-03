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
                        <i class="fas fa-building"></i>
                        <span>Compagnies</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('compagnies.index') }}">Toutes les compagnies</a></li>
                        <li><a href="{{ route('compagnies.create') }}">Ajouter une compagnie</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-user-shield"></i>
                        <span>Permissions</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('permissions.index') }}">Toutes les permissions</a></li>
                        <li><a href="{{ route('permissions.create') }}">Ajouter une permission</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-users"></i>
                        <span>Utilisateurs</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('users.index') }}">Tous les utilisateurs</a></li>
                        <li><a href="{{ route('users.create') }}">Ajouter un utilisateur</a></li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('notes.index') }}">
                        <i class="fas fa-sticky-note"></i>
                        <span>Notes</span>
                    </a>
                </li>
                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('notifications.index') }}">Tous les Notifications</a></li>
                        <li><a href="{{ route('notifications.create') }}">Envoyer un Notifications</a></li>
                    </ul>
                </li>

               

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-wallet"></i>
                        <span>Paiements</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('paiements.index') }}">Tous les paiements</a></li>
                        {{-- <li><a href="{{ route('paiements.create') }}">Ajouter un paiement</a></li> --}}
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Garres</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('garres.index') }}">Toutes les garres</a></li>
                        <li><a href="{{ route('garres.create') }}">Ajouter une garre</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-id-badge"></i>
                        <span>Profils</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('profils.index') }}">Tous les profils</a></li>
                        <li><a href="{{ route('profils.create') }}">Ajouter un profil</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-route"></i>
                        <span>Voyages</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('voyages.index') }}">Tous les voyages</a></li>
                        <li><a href="{{ route('voyages.create') }}">Ajouter un voyage</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-bus"></i>
                        <span>Bus</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('buses.index') }}">Tous les bus</a></li>
                        <li><a href="{{ route('buses.create') }}">Ajouter un bus</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-map-signs"></i>
                        <span>Trajets</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('trajets.index') }}">Tous les trajets</a></li>
                        <li><a href="{{ route('trajets.create') }}">Ajouter un trajet</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-clock"></i>
                        <span>Fréquences</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('frequences.index') }}">Toutes les fréquences</a></li>
                        <li><a href="{{ route('frequences.create') }}">Ajouter une fréquence</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Tickets</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('tickets.index') }}">Tous les tickets</a></li>
                        <li><a href="{{ route('tickets.create') }}">Ajouter un ticket</a></li>
                    </ul>
                </li>

                <li>
                    <a href="#">
                        <i class="fas fa-envelope"></i>
                        <span>Contacts</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
