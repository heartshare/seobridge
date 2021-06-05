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
                No matter if you're a freelancer, startup-founder or agency marketer <b>— we have the SEO tools you need!</b>
            </p>
        </div>
    </div>

    <div class="limiter pricing" data-cs-00004>
        <div class="price-plan">
            <div class="header">
                <h2>Starter</h2>
                <h4>10€</h4>
                <span>per month</span>
            </div>

            <div class="features">
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">3 Domains</div>
                </div>
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">Unlimited Scans</div>
                </div>
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">150 URLs / Domain</div>
                </div>
                <div class="feature-point">
                    <div class="icon">&#983385;</div>
                    <div class="text"><b>No Additional Domains</b></div>
                </div>
            </div>

            <a href="/go-pro/freelancer" class="button">Get SEO Bridge Starter</a>
        </div>

        <div class="price-plan featured">
            <div class="header">
                <h2>Growing</h2>
                <h4>25€</h4>
                <span>per month</span>
            </div>

            <div class="features">
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">3 Domains</div>
                </div>
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">Unlimited Scans</div>
                </div>
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">500 URLs / Domain</div>
                </div>
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text"><b>3€ / Additional Domain</b></div>
                </div>
            </div>

            <a href="/go-pro/startup" class="button">Get SEO Bridge Growing</a>
        </div>

        <div class="price-plan">
            <div class="header">
                <h2>Unlimited</h2>
                <h4>60€</h4>
                <span>per month</span>
            </div>

            <div class="features">
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">Unlimited Domains</div>
                </div>
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">Unlimited Scans</div>
                </div>
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text">1000 URLs / Domain</div>
                </div>
                <div class="feature-point">
                    <div class="icon">&#984544;</div>
                    <div class="text"><b>Free Additional Domains</b></div>
                </div>
            </div>

            <a href="/go-pro/agency" class="button">Get SEO Bridge Unlimited</a>
        </div>
    </div>

    <div class="limiter point-wrapper">
        <h2 style="text-align: center">Included in the package</h2>
        <div class="card-point">
            <h4>Personalized SEO Checklists</h4>
            <p>Get relevant information about your website and tips on how to improve it.</p>
        </div>
        <div class="card-point">
            <h4>Mass Analysis</h4>
            <p>Check not just one but up to 50 URLs at a time.</p>
        </div>
        <div class="card-point">
            <h4>Report Sharing</h4>
            <p>Share and assign your page reports with your team.</p>
        </div>
        <div class="card-point">
            <h4>Unlimited Scans</h4>
            <p>Scan your site as often as you like − anytime you like.</p>
        </div>
        <div class="card-point">
            <h4>Open Graph Preview</h4>
            <p>Get a preview of your open graph integration usually hidden in the source code.</p>
        </div>
        <div class="card-point">
            <h4>Twitter Card Preview</h4>
            <p>Preview and validate your Twitter card integration usually hidden in the source code.</p>
        </div>
    </div>

    <div class="limiter point-wrapper">
        <h3 style="text-align: center">Even more coming in the future</h3>
        <div class="card-point">
            <h4>AI Text Analysis</h4>
            <p>Let our AI check your texts for its expressiveness and see if your brand message is coming across.</p>
        </div>
        <div class="card-point">
            <h4>Generate Open Graph Cards</h4>
            <p>Quickly generate your open graph tags uncluding a live preview.</p>
        </div>
        <div class="card-point">
            <h4>Generate Twitter Cards</h4>
            <p>Easily generate your Twitter card tags uncluding a live preview.</p>
        </div>
    </div>
</article>
@endsection
