@extends('layouts.static')

@section('title', 'Our pricing: simple, fair and scalable — SEO Bridge')
@section('description', 'We offer SEO tools for businesses of all sizes. Choose your payment plan today!')
@section('page-classes', 'no-header')

@section('content')
<article data-cs-00002>
    <div class="block font-size" data-cs-02001>
        <div class="limiter">
            <h1 style="text-align: center">Pricing that's fair, simple and scalable!</h1>
            <p style="text-align: center">
                No matter if you're a freelencer, startup-founder or agency marketer <b>— we have the SEO tools you need!</b>
            </p>
        </div>
    </div>

    <div class="limiter pricing" data-cs-00004>
        <div class="price-plan">
            <div class="header">
                <h2>Freelencer</h2>
                <h4>5€</h4>
                <span>per month</span>
            </div>

            <div class="features">
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">2 Websites</div>
                </div>
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">Unlimited Scans</div>
                </div>
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">100 URLs / Website</div>
                </div>
                <div class="divider"></div>
                <div class="feature-point">
                    <div class="icon">&#983385;</div>
                    <div class="text"><b>No Additional Websites</b></div>
                </div>
            </div>

            <a href="#" class="button">Get SEO Bridge Freelencer</a>
        </div>

        <div class="price-plan featured">
            <div class="header">
                <h2>Startup</h2>
                <h4>15€</h4>
                <span>per month</span>
            </div>

            <div class="features">
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">3 Websites</div>
                </div>
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">Unlimited Scans</div>
                </div>
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">500 URLs / Website</div>
                </div>
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text"><b>5€ / Additional Website</b></div>
                </div>
            </div>

            <a href="#" class="button">Get SEO Bridge Startup</a>
        </div>

        <div class="price-plan">
            <div class="header">
                <h2>Agency</h2>
                <h4>25€</h4>
                <span>per month</span>
            </div>

            <div class="features">
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">4 Websites</div>
                </div>
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">Unlimited Scans</div>
                </div>
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">1000 URLs / Website</div>
                </div>
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text"><b>7€ / Additional Website</b></div>
                </div>
            </div>

            <a href="#" class="button">Get SEO Bridge Agency</a>
        </div>
    </div>

    <div class="limiter point-wrapper">
        <h3 style="text-align: center">Included in the package</h3>
        <h4 class="poligon-point">Unlimited scans</h4>
        <h4 class="poligon-point">Daily page audits</h4>
        <h4 class="poligon-point">Report sharing</h4>
        <h4 class="poligon-point">Structure analysis tool</h4>
        <h4 class="poligon-point">Open Graph preview</h4>
        <h4 class="poligon-point">Twitter card preview</h4>
    </div>
</article>
@endsection
