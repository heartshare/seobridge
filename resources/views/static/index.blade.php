@extends('layouts.static')

@section('title', 'Page Structure and SEO Checker Tool — SEO Bridge')
@section('description', 'Analyse your website for free with SEO Bridge and get a personal SEO checklist')

@section('content')
{{-- <header data-cs-00001>
    <div>
        <h3>Check your site's SEO score for free</h3>
        <div class="url-input-wrapper">
            <form method="POST" action="/request-guest-site-analysis">
                @csrf
                <input class="input" name="url" type="text" placeholder="Enter your site's URL">
                <ui-button class="submit-button" icon="&#983881;">Analyse</ui-button>
            </form>
        </div>
    </div>
</header> --}}
<article data-cs-00001>
    <section class="section-1">
        <div class="limiter">
            <h1>
                SEO<br>
                Analysis<br>
                Assistance
            </h1>
            <p>
                Never miss SEO aspects again<br>
                even on the biggest sites!
            </p>
        </div>
    </section>

    <section class="section-2">
        <div class="limiter">
            <div class="flex">
                <div class="item">
                    <div class="icon primary">visibility</div>
                    <h2>Track</h2>
                    <p>
                        Keep track of your website and all the changes happening
                        to it with periodical scans and full page tracking and
                        generate shareable reports from your data.
                    </p>
                </div>
                <div class="item">
                    <div class="icon secondary">data_usage</div>
                    <h2>Analyse</h2>
                    <p>
                        Get individualized report-checklists and see exactly where
                        your site underperforms and what causes it. Get an
                        insight on meta data usually hidden in the code.
                    </p>
                </div>
                <div class="item">
                    <div class="icon gray">share</div>
                    <h2>Share</h2>
                    <p>
                        Communication is key. Share your reports with your team.
                        Assign fixes to specific members and keep track on
                        the progress made to pain points.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-3">
        <div class="limiter">
            <div class="flex">
                <div class="item">
                    <h2>
                        In-House<br>
                        <b>Made Public</b>
                    </h2>
                    <p>
                        SEO Bridge was originally funded by the marketing agency <a href="https://www.werbung-fleischer-dienst.de/" target="_blank">FD Werbung</a>
                        for which it was developed as an in-house SEO solution.<br>
                        Now we decided to make it available to the public.
                    </p>
                </div>
                <div class="item"></div>
            </div>
        </div>
    </section>

    <section class="section-4">
        <div class="limiter">
            <div class="flex">
                <div class="item"></div>
                <div class="item">
                    <h2>
                        Start Small<br>
                        <b>Scale Big</b>
                    </h2>
                    <p>
                        SEO Bridge is built to be scalable. No matter if you're a freelencer,
                        startup-founder or agency marketer – we have you covered!
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- <div class="limiter">
        <h2>Get a feel for how it looks</h2>
        <silent-box :gallery="[{'src':'/images/static/bug_tracking.svg'}, {'src':'/images/static/bug_tracking.svg'}, {'src':'/images/static/bug_tracking.svg'}, {'src':'/images/static/bug_tracking.svg'}, {'src':'/images/static/bug_tracking.svg'}, {'src':'/images/static/bug_tracking.svg'}]"></silent-box>
    </div> --}}
</article>
@endsection
