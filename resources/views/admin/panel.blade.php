@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="admin-panel">
                <div class="admin-panel__notification">
                    @if(session('global_message_sent'))
                        <div class="alert alert-success">
                            {{ __('Global message sent') }}
                        </div>
                    @endif
                </div>
                <div class="admin-panel__global-message">
                    <form action="{{ route('admin.global_message') }}">
                        <div class="form-group">
                            <label for="global_message">Global Message</label>
                            <textarea id="global_message" name="message" type="text" class="form-control"
                                      placeholder="Message..." cols="30" rows="3"></textarea>
                        </div>
                        <button class="btn btn-dark" type="submit">{{ __('Send') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
