@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">{{ __('OAuth 2.0 Dashboard') }}</div>

          <div class="card-body">
            @if (session('status'))
              <div class="alert alert-success" role="alert">
                {{ session('status') }}
              </div>
            @endif

            <div class="p-1">
              <clients></clients>
            </div>
            <div class="p-1">
              <authorized-clients></authorized-clients>
            </div>
            <div class="p-1">
              <access-tokens></access-tokens>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
