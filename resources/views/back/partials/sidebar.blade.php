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
                        <i class="fas fa-edit"></i>
                        <span>Logement</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="submenu_class" style="display: none;">
                        <li><a href="{{ route('article.index') }}">Tous les logements</a></li>
                        <li><a href="{{ route('article.create') }}">Ajouter un logement</a></li>
                    </ul>
                </li>
                @can('admin-access')
                    <li class="submenu">
                        <a href="#">
                            <i class="fas fa-book"></i>
                            <span>Catégories</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="submenu_class" style="display: none;">
                            <li><a href="{{ route('category.index') }}">Toutes les catégories</a></li>
                            <li><a href="{{ route('category.create') }}">Ajouter une catégorie</a></li>
                        </ul>
                    </li>


                    <li class="submenu">
                        <a href="#">
                            <i class="fas fa-user"></i>
                            <span>Personnels</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="submenu_class" style="display: none;">
                            <li><a href="{{ route('author.index') }}">Tout le personnel</a></li>
                            <li><a href="{{ route('author.create') }}">Ajouter un personnel</a></li>
                        </ul>
                    </li>
                @endcan

                <li>
                    <a href="{{ route('comment.index') }}">
                        <i class="fe fe-table"></i>
                        <span>Commentaires</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('location.index') }}">
                        <i class="fe fe-table"></i>
                        <span>Location</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('rent.index') }}">
                        <i class="fe fe-table"></i>
                        <span>Vente</span>
                    </a>
                </li>
                @can('admin-access')
                    <li class="submenu">
                        <a href="#">
                            <i class="far fa-money-bill-alt"></i>
                            <span>Médias Sociaux</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="submenu_class" style="display: none;">
                            <li><a href="{{ route('social.index') }}">Tous les médias</a></li>
                            <li><a href="{{ route('social.create') }}">Ajouter un média</a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                        <a href="#">
                            <i class="far fa-money-bill-alt"></i>
                            <span>Roles</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul class="submenu_class" style="display: none;">
                            <li><a href="{{ route('role.index') }}">Tous les Roles</a></li>
                            <li><a href="{{ route('role.create') }}">Ajouter un Role</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{ route('contact.index') }}">
                            <i class="fe fe-table"></i>
                            <span>Contacts</span>
                        </a>
                    </li>


                    <li>
                        <a href="{{ route('Setting.index') }}">
                            <i class="fas fa-cog"></i>
                            <span>Paramètres</span>
                        </a>
                    </li>
                @endcan


            </ul>
        </div>
    </div>
</div>
