@extends('back.app')
@section('title', 'Notes de voyages')
@section('dashboard-header')
<h4 class="mt-5">Notes et commentaires</h4>
@endsection
@section('dashboard-content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Ticket</th>
                    <th>Note</th>
                    <th>Commentaire</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notes as $note)
                <tr>
                    <td>{{ $note->idTicket }}</td>
                    <td>{{ $note->note }}/5</td>
                    <td>{{ $note->commentaire }}</td>
                    <td>{{ $note->dateNote }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
