@extends('back.app')

@section('title', 'Dashboard-des articles')
@section('dashboard-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="mt-5">
                <h4 class="card-title float-left mt-2">Articles</h4>
                <a href="{{ route('article.create') }}" class="btn btn-primary float-right viewbutton">Ajouter un profils</a>
            </div>
        </div>
    </div>

@endsection
@section('dashboard-content')

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body booking_card">
                    <div class="table-responsive">
                        <table class="datatable table table-stripped table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>ID Profils</th>
                                    <th>name profils</th>
                                    <th>descriptions</th>
                                    <th>Permissions</th>
                                     <th>Actions</th>
                                  
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($articles as $item) --}}
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        {{-- <td><img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" width="100"
                                                height="100"></td>
                                        <td>{{ $item->title }}</td> --}}
                                        <td>{{ $item->name }}</td>
                                        {{-- <td>21-03-2020</td> --}}
                                        
                                           <td>{{  $item->description }}</td>

                                @foreach ( as )
                                <td>
                                            {{-- @if ($item->isSharable == 1)
                                                <div class="actions"><a href="#"
                                                        class="btn btn-sm bg-success-light mr-2">
                                                        Active</a></div>
                                            @else
                                                <div class="actions"><a href="#"
                                                        class="btn btn-sm bg-success-light mr-2">
                                                        Desactive</a></div>
                                            @endif --}}

                                        </td>
                                    
                                @endforeach
                                        
                                        
{{--                                          
                                        <td>
                                            <h2 class="table-avatar">
                                                <a href="profile.html" class="avatar avatar-sm mr-2"><img
                                                        class="avatar-img rounded-circle"
                                                        src="{{ asset('back_auth/assets/profile/' . $item->author->image) }}"
                                                        alt="User Image">
                                                </a>
                                                <a
                                                    href="">{{ $item->author->name }}<span>{{ $item->author->id }}</span></a>
                                            </h2>
                                        </td> --}}
                                        <td class="text-right">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown"
                                                    aria-expanded="false"><i class="fas fa-ellipsis-v ellipse_color"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="{{ route('article.show', $item) }}">
                                                        <i class="fas fa-pencil-alt m-r-5"></i> Voir
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('article.edit', $item) }}">
                                                        <i class="fas fa-pencil-alt m-r-5"></i> Modifier
                                                    </a>
                                                    <form action="{{ route('article.destroy', $item) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger"> <i
                                                                class="fas fa-trash-alt m-r-5"></i> Supprimer
                                                        </button>
                                                    </form>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>




@endsection
