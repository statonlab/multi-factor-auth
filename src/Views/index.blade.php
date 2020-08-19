@extends('mfa::layout')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-md-8 offset-2">
                <form action="{{ route('mfa.send') }}" method="post">
                    <div class="card shadow border-0">
                        <div class="card-body">
                            @csrf
                            <p>
                                <strong>Verify Your Identity</strong>
                            </p>

                            <p>
                                We will send you a verification code to verify your identity.
                                Please select an option below to send the verification code to.
                            </p>

                            <div class="d-flex align-items-center">
                                <div class="form-group flex-grow-1">
                                    <div class="custom-control custom-radio">
                                        <input type="radio"
                                               id="mail"
                                               name="channel"
                                               value="mail"
                                               checked
                                               class="custom-control-input">
                                        <label class="custom-control-label font-weight-bold" for="mail">
                                            Send to my email address
                                        </label>
                                        <p class="text-muted">
                                            {{ $obfuscated_email }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <svg fill="none"
                                         stroke-linecap="round"
                                         stroke-linejoin="round"
                                         stroke-width="2"
                                         viewBox="0 0 24 24"
                                         class="text-primary"
                                         style="width: 50px; height: 50px;"
                                         stroke="currentColor">
                                        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>

                            <button class="btn btn-primary" type="submit">
                                Send Code
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection