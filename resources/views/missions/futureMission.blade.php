@extends('templates.main')
@section('title', $mission->name)

@section('content')
    <body class="future-mission">

        @include('templates.header', array('backgroundImage' => !is_null($mission->featuredImage) ? $mission->featuredImage->media : ''))

        <div class="content-wrapper" ng-controller="futureMissionController" ng-strict-di>
            <h1>{{ $mission->name }}</h1>
            <main>
                <nav class="in-page sticky-bar">
                    <ul class="container">
                        <li class="gr-1">
                            <a href="#countdown">Countdown</a>
                        </li>
                        <li class="gr-1">
                            <a href="#details">Details</a>
                        </li>
                        <li class="gr-1">
                            <a href="#timeline">Timeline</a>
                        </li>
                        <li class="gr-1">
                            <a href="#articles">Articles</a>
                        </li>

                        <li class="gr-2 float-right actions">
                            @if (Auth::isAdmin())
                                <a class="link" href="/missions/{{ $mission->slug }}/edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endif
                            <i class="fa fa-twitter"></i>
                            @if (Auth::isMember())
                                <a class="link" href="/users/{{Auth::user()->username}}/edit#email-notifications"><i class="fa fa-envelope-o"></i></a>
                            @else
                                <a class="link" href="/docs#email-notifications"><i class="fa fa-envelope-o"></i></a>
                            @endif
                            <a class="link" href="/calendars/{{ $mission->slug }}">
                                <i class="fa fa-calendar"></i>
                            </a>
                            <a href="http://www.google.com/calendar/render?cid={{ Request::url() }}">
                                <i class="fa fa-google"></i>
                            </a>
                            <a href="">
                                <i class="fa fa-rss"></i>
                            </a>
                        </li>
                        <li class="gr-1 float-right">
                            @if ($mission->status == 'Complete')
                                <span class="status complete"><i class="fa fa-check"></i> Complete</span>
                            @elseif ($mission->status == 'In Progress')
                                <span class="status in-progress"><i class="fa fa-check"></i> In Progress</span>
                            @else
                                <span class="status upcoming"><i class="fa fa-cross"></i> Upcoming</span>
                            @endif
                        </li>
                    </ul>
                </nav>

                <section class="highlights">
                    <div class="webcast-status" ng-class="webcast.status" ng-if="isLaunchExact == true" ng-show="webcast.status != 'webcast-inactive'">
                        <span>@{{ webcast.publicStatus }}</span><span class="live-viewers" ng-show="webcast.status === 'webcast-live'">@{{ webcast.publicViewers }}</span>
                    </div>

                    @if(isset($pastMission))
                        <a href="/missions/{{ $pastMission->slug }}">
                            <div class="mission-link past-mission-link">
                                <span class="placeholder">Previous Mission</span>
                                <span class="link"><i class="fa fa-arrow-left"></i> {{ $pastMission->name }}</span>
                            </div>
                        </a>
                    @endif
                    @if(isset($futureMission))
                        <a href="/missions/{{ $futureMission->slug }}">
                            <div class="mission-link future-mission-link">
                                <span class="link">{{ $futureMission->name }} <i class="fa fa-arrow-right"></i></span>
                                <span class="placeholder">Next Mission</span>
                            </div>
                        </a>
                    @endif

                    <div class="display-date-time">
                        <div class="launch">@{{ displayDateTime() }}</div>
                        <div class="timezone" ng-if="isLaunchExact == true">
                            <span class="timezone-current">@{{ currentTimezoneFormatted }}</span>
                            <ul class="timezone-list">
                                <li class="timezone-option" ng-click="setTimezone('local')">Local (@{{ localTimezone }})</li>
                                <li class="timezone-option" ng-click="setTimezone('ET')">Eastern</li>
                                <li class="timezone-option" ng-click="setTimezone('PT')">Pacific</li>
                                <li class="timezone-option" ng-click="setTimezone('UTC')">UTC</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section class="hero" id="countdown">
                    <countdown specificity="launchSpecificity" countdown-to="launchDateTime" callback="requestFrequencyManager" type="classic"></countdown>
                </section>
                <p>{{ $mission->summary }}</p>

                <h2>Details</h2>
                <section class="scrollto" id="details">

                    @include('templates.missionCard', ['size' => 'large', 'mission' => $mission])

                    @if ($mission->isNextToLaunch())
                        <h3>SpaceX Stats Live</h3>
                        <div>
                            <p>Watch & follow the launch in real time with SpaceX Stats Live</p>
                        </div>
                    @endif

                    <h3>Recent Tweets</h3>
                    <div id="recent-tweets">

                    </div>

                    <h3>Satellites</h3>
                </section>

                <h2>Timeline</h2>
                <section class="scrollto" id="timeline">
                    <canvas></canvas>
                </section>
                <h2>Articles</h2>
                <section class="scrollto" id="articles">
                    @foreach ($mission->articles() as $article)
                    @endforeach
                </section>
            </main>
        </div>
    </body>
@stop