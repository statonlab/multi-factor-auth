@extends('mfa::layout')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-md-8 offset-2">
                <form action="{{ route('mfa.verify') }}" method="post">
                    <div class="card shadow border-0">
                        <div class="card-body">
                            @csrf
                            <p class="text-muted">
                                We've sent you a verification code. Once you receive it, please enter it below. Verification codes
                                automatically expire after {{ $minutes }} minutes.
                            </p>

                            <div class="form-group">
                                <label for="code" class="font-weight-bold">Verification Code</label>
                                <div class="d-flex">
                                    <div class="pr-2 flex-grow-1">
                                        <input type="text"
                                               name="code"
                                               id="code"
                                               class="form-control{{ $errors->has('code') ? ' is-invalid': '' }}"
                                               placeholder="Enter code"
                                               autocomplete="off"
                                               required
                                               autofocus>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <button class="btn btn-primary" type="submit">
                                            Verify
                                        </button>
                                    </div>
                                </div>
                                @if($errors->has('code'))
                                    <p class="form-text text-danger">
                                        {{ $errors->first('code') }}
                                    </p>
                                @endif
                            </div>
                            <div class="form-group mb-0">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           value="1"
                                           id="remember"
                                           name="remember"{{ old('remember') ? ' checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        Remember this device
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection