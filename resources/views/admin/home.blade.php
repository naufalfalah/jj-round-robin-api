@extends('layouts.admin')
@section('content')
    @livewire('message', ['users' => $users, 'messages' => $messages ?? null])
@endsection
