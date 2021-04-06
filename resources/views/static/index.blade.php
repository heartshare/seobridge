@extends('layouts.static')

@section('title', 'Page Structure and SEO Checker Tool — SEO Bridge')
@section('description', 'Analyse your website for free with SEO Bridge and get a personal SEO checklist')

@section('content')
<header data-cs-00001>
    <div>
        <h3>Check your site's SEO score for free</h3>
        {{-- <div class="url-input-wrapper">
            <form method="POST" action="/request-guest-site-analysis">
                @csrf
                <input class="input" name="url" type="text" placeholder="Enter your site's URL">
                <ui-button class="submit-button" icon="&#983881;">Analyse</ui-button>
            </form>
        </div> --}}
    </div>
</header>
<article data-cs-00007>
    <div class="limiter">
        <h1>SEO tools made to make your life easier!</h1>
        <p>
            Search engine optimization is an integral part of todays web development and content creation cycle.
            It's unavoidable to optimize your website perhaps just a little bit to improve your search ranking.
            But with the ever increasing technical aspects one has to master it may be difficult to efficiently optimize
            just one of your pages, not to mention 10 or even 100.
        </p>
        <p>
            <b>Let us introduce SEO Bridge</b> —
            Why don't you let the computer do the tedious work while you focus on what really matter: creating awesome content!
            SEO Bridge can mass analyse every page of your website and give you personalized tips on what and how to improve it.
            Perhaps you work in a team and want to share your reports with your peers; no problem for SEO Bridge.
            You can easily create your team, invite your colleagues and share your findings with them.
        </p>
        {{-- <p>
            <b>Want to try it?</b> Just give our free demo above a spin!
        </p> --}}
    </div>

    <div class="limiter">
        <div class="flex-50-50">
            <div class="item">
                <img src="/images/static/checklist.svg" alt="A checklist of SEO tips">
            </div>
            <div class="item">
                <h2>Get a personalized SEO checklist</h2>
                <p>
                    Let's face it, general tips and knowledge about optimizing your page are great but nothing beats
                    an in-depth list of improvements made specifically just for your site. 
                </p>
            </div>
        </div>
    </div>

    <div class="gray-polygon">
        <div class="limiter">
            <div class="flex-50-50">
                <div class="item">
                    <h2>Analyse together with your team</h2>
                    <p>
                        We focus on bringing collaboration and communication to your SEO tools.
                        Get invited by your team or create your very own and start working together
                        in analyzing, optimizing and finalizing the pages that matter. From simply sharing the latest reports
                        with your team to assigning flawed reports to members - SEO Bridge got you covered!
                    </p>
                </div>
                <div class="item">
                    <img src="/images/static/collaboration.svg" alt="A team checing datapoints">
                </div>
            </div>
        </div>
    </div>

    <div class="limiter">
        <div class="flex-50-50">
            <div class="item">
                <img src="/images/static/bug_tracking.svg" alt="Man tracking bugs">
            </div>
    
            <div class="item">
                <h2>Scan your site for technical errors</h2>
                <p>
                    Quickly iron out even the last technical errors holding you back from reaching #1 in the search results.
                    It's easy to miss small things like adding "alt"-tags to your images or forgetting to scale stock images
                    down to an appropriate resolution. Just scan your page with SEO Bridge and see all the things you might have missed.
                </p>
            </div>
        </div>
    </div>
</article>
@endsection
