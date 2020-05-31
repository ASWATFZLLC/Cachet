<div class="container">
    <div class="row">
        <div class="col-md-12 text-center">
                <h1 style="font-weight:bold;color:#ad3278">Having an issue that is not reported above ?</h1>
                <a style="font-weight:bold;color:#1f73b7;font-size:30px" href="mailto:support@ziwo.io">Tell us</a>
            </div>
        </div>
</div>
@if($appFooter)
{!! $appFooter !!}
@else
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-sm-4">
                @if($showSupport)
                <p>
                    {!! trans('cachet.powered_by') !!}
                    @if($showTimezone)
                    {{ trans('cachet.timezone', ['timezone' => $timezone]) }}
                    @endif
                </p>
                @endif
            </div>
            <div class="col-sm-8">
                <ul class="list-inline">
                    @if($currentUser || $dashboardLink)
                    <li>
                        <a class="btn btn-link" href="{{ cachet_route('dashboard') }}">{{ trans('dashboard.dashboard') }}</a>
                    </li>
                    @endif
                    @if($currentUser)
                    <li>
                        <a class="btn btn-link" href="{{ cachet_route('auth.logout') }}">{{ trans('dashboard.logout') }}</a>
                    </li>
                    @endif
                    @if($enableSubscribers)
                    <li>
                        <a class="btn btn-success btn-outline" href="{{ cachet_route('subscribe') }}">{{ trans('cachet.subscriber.button') }}</a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</footer>
@endif

@include("partials.analytics")
