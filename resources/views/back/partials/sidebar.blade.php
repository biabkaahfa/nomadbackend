<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <!-- Tableau de bord -->
                <li class="active">
                    <a href="/dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>

                <li class="list-divider"></li>

                <!-- Section Administration -->
                @auth
                @if(Auth::user()->profil->name == 'Admin général')
                <li class="menu-title">
                    <span>Administration</span>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-building"></i>
                        <span>Compagnies</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('compagnies.index') }}"><i class="fas fa-list"></i> Liste des compagnies</a></li>
                        <li><a href="{{ route('compagnies.create') }}"><i class="fas fa-plus-circle"></i> Ajouter</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-user-shield"></i>
                        <span>Permissions</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('permissions.index') }}"><i class="fas fa-list"></i> Toutes les permissions</a></li>
                        <li><a href="{{ route('permissions.create') }}"><i class="fas fa-plus-circle"></i> Ajouter</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-id-card"></i>
                        <span>Profils</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('profils.index') }}"><i class="fas fa-list"></i> Tous les profils</a></li>
                        <li><a href="{{ route('profils.create') }}"><i class="fas fa-plus-circle"></i> Ajouter</a></li>
                    </ul>
                </li>
                @endif
                @endauth

                <!-- Section Utilisateurs -->


                @auth
                @if (Auth::user()->profil->name == 'Admin général' || Auth::user()->profil->name == 'Admin compagnie' || Auth::user()->profil->name == 'Chef de gare')
                <li class="menu-title">
                    <span>Gestion Utilisateurs</span>
                </li>
                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-users-cog"></i>
                        <span>Utilisateurs</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('user.index') }}"><i class="fas fa-list"></i> Tous les utilisateurs</a></li>
                        <li><a href="{{ route('user.create') }}"><i class="fas fa-user-plus"></i> Ajouter</a></li>
                    </ul>
                </li>
                @endif
                @endauth
                 @auth
                @if (Auth::user()->profil->name == 'Admin général' || Auth::user()->profil->name == 'Admin compagnie')
                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-id-card-alt"></i>
                        <span>Abonnements</span>
                        <span class="menu-arrow"></span>
                    </a>
                    {{-- personalisationCard.indexe --}}
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('abonementPublic.index') }}"><i class="fas fa-list"></i> Tous les abonnements public</a></li>
                        <li><a href="{{ route('abonementPublic.createPerso') }}"><i class="fas fa-plus-circle"></i> Ajouter</a></li>
                         <li><a href="{{ route('personalisationCard.indexe') }}"><i class="fas fa-plus-circle"></i> Listes Cards</a></li>
                    </ul>
                </li>
                @endif
                @endauth

                <!-- Section Transport -->


                @auth
                @if (Auth::user()->profil->name != 'Réceptionniste')
                 <li class="menu-title">
                    <span>Gestion Transport</span>
                </li>
                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-bus-alt"></i>
                        <span>Bus</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('buses.index') }}"><i class="fas fa-list"></i> Tous les bus</a></li>
                        <li><a href="{{ route('buses.create') }}"><i class="fas fa-plus-circle"></i> Ajouter</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-route"></i>
                        <span>Trajets</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('trajets.index') }}"><i class="fas fa-list"></i> Tous les trajets</a></li>
                        <li><a href="{{ route('trajets.create') }}"><i class="fas fa-plus-circle"></i> Ajouter</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-map-marked-alt"></i>
                        <span>Voyages</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('voyages.index') }}"><i class="fas fa-list"></i> Tous les voyages</a></li>
                        <li><a href="{{ route('voyages.create') }}"><i class="fas fa-plus-circle"></i> Ajouter</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-clock"></i>
                        <span>Fréquences</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('frequences.index') }}"><i class="fas fa-list"></i> Toutes les fréquences</a></li>
                        <li><a href="{{ route('frequences.create') }}"><i class="fas fa-plus-circle"></i> Ajouter</a></li>
                    </ul>
                </li>
                @endif
                @endauth

                <!-- Section Gares -->
                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Gares</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('garres.index') }}"><i class="fas fa-list"></i> Toutes les gares</a></li>
                        <li><a href="{{ route('garres.create') }}"><i class="fas fa-plus-circle"></i> Ajouter</a></li>
                    </ul>
                </li>

                <!-- Section Tickets -->
                <li class="menu-title">
                    <span>Ventes & Abonnements</span>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Tickets</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('tickets.index') }}"><i class="fas fa-list"></i> Tous les tickets</a></li>
                        @auth
                        @if (Auth::user()->profil->name == 'Réceptionniste')
                        <li><a href="{{ route('tickets.create') }}"><i class="fas fa-plus-circle"></i> Ajouter</a></li>
                        @endif
                        @endauth
                    </ul>
                </li>

                @auth
                @if (Auth::user()->profil->name == 'Admin général' || Auth::user()->profil->name == 'Admin compagnie')
                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-id-card-alt"></i>
                        <span>Abonnements</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('abonnements.index') }}"><i class="fas fa-list"></i> Tous les abonnements</a></li>
                        <li><a href="{{ route('abonnements.create') }}"><i class="fas fa-plus-circle"></i> Ajouter</a></li>
                    </ul>
                </li>
                @endif
                @endauth

                @auth
                @if (Auth::user()->profil->name == 'Admin général')
                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-file-contract"></i>
                        <span>Types Abonnements</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('type-abonements.index') }}"><i class="fas fa-list"></i> Tous les types</a></li>
                        <li><a href="{{ route('type-abonements.create') }}"><i class="fas fa-plus-circle"></i> Ajouter</a></li>
                    </ul>
                </li>
                @endif
                @endauth

                <!-- Section Finances -->
                <li class="menu-title">
                    <span>Finances</span>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Paiements</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('paiements.index') }}"><i class="fas fa-list"></i> Tous les paiements</a></li>
                    </ul>
                </li>

                <!-- Section Communication -->
                <li class="menu-title">
                    <span>Communication</span>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('notifications.index') }}"><i class="fas fa-list"></i> Toutes les notifications</a></li>
                        <li><a href="{{ route('notifications.create') }}"><i class="fas fa-bell"></i> Envoyer</a></li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('notes.index') }}">
                        <i class="fas fa-sticky-note"></i>
                        <span>Notes internes</span>
                    </a>
                </li>

                @auth
                @if (Auth::user()->profil->name == 'Admin général' || Auth::user()->profil->name == 'Admin compagnie')
                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-envelope-open-text"></i>
                        <span>Messages</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('messages.index') }}"><i class="fas fa-inbox"></i> Boîte de réception</a></li>
                    </ul>
                </li>
                @endif
                @endauth


 @php
    $profil = Auth::user()->profil->name;
    $parametre = \App\Models\Parametres::where('idCompagnie', Auth::user()->idCompagnie)->first();
@endphp

@if ($profil === 'Admin général' || $profil === 'Admin compagnie')
    <!-- Section Paramètres -->
    <li class="menu-title">
        <span>Configuration</span>
    </li>

    <li class="submenu">
        <a href="#">
            <i class="fas fa-cogs"></i>
            <span>Paramètres</span>
            <span class="menu-arrow"></span>
        </a>
        <ul class="submenu_class" style="display: none;">
            {{-- Admin général peut voir index et create --}}
            @if ($profil === 'Admin général')
                <li><a href="{{ route('parametres.index') }}"><i class="fas fa-list"></i> Tous les paramètres</a></li>
                <li><a href="{{ route('parametres.create') }}"><i class="fas fa-plus-circle"></i> Ajouter</a></li>
            @endif

            {{-- Admin général et Admin compagnie peuvent voir show/edit --}}
            @if ($parametre)
                <li><a href="{{ route('parametres.edit', $parametre) }}"><i class="fas fa-edit"></i> Modifier</a></li>
                <li><a href="{{ route('parametres.show', $parametre) }}"><i class="fas fa-eye"></i> Voir</a></li>
            @endif
        </ul>
    </li>
@endif


                <li>
                    <a href="#">
                        <i class="fas fa-question-circle"></i>
                        <span>Aide & Support</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
