<template>
    <div class="page-container limiter">
        <quick-check-module @details="openDetails($event)" v-if="!details"></quick-check-module>

        <div class="details" v-else>

            <div class="nav-row">
                <ui-button icon="&#983117;" icon-left border light @click="details = null">Back</ui-button>
            </div>

            <div class="detail-row">
                <div class="card span-4 page-info-card">
                    <div class="images">
                        <img :src="details.preview" class="preview">
                        <img :src="details.metaData.favicon" class="favicon">
                    </div>
                </div>
                <div class="card span-8 page-info-card">
                    <!-- <div class="images">
                        <img :src="details.preview" class="preview">
                        <img :src="details.metaData.favicon" class="favicon">
                    </div> -->
                    <div class="content">
                        <h4>{{details.metaData.title}}</h4>

                        <div class="metric-card-wrapper">
                            <div class="metric-card" v-if="details.metaData.description">
                                <div class="icon" style="color: #12CBC4">&#985975;</div>
                                <div class="label">Description</div>
                                <div class="value">{{details.metaData.description}}</div>
                            </div>

                            <div class="metric-card half" v-if="details.metaData.generator">
                                <div class="icon" style="color: #1e90ff">&#987021;</div>
                                <div class="label">CMS / Generator</div>
                                <div class="value">{{details.metaData.generator}}</div>
                            </div>

                            <div class="metric-card half" v-if="details.metaData.themeColor">
                                <div class="icon" style="color: #3742fa">&#984024;</div>
                                <div class="label">Theme Color</div>
                                <div class="value">{{details.metaData.themeColor}}</div>
                            </div>

                            <div class="metric-card" v-if="details.metaData.keywords.length > 0">
                                <div class="icon" style="color: #747d8c">&#983819;</div>
                                <div class="label">Keywords</div>
                                <div class="value">{{details.metaData.keywords.join(', ') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-row">

                <div class="card span-4 center">
                    <apexchart style="display: inherit;" type="radialBar" height="350" :options="chartOptions" :series="[details.score.totalPageScore] || [0]"></apexchart>
                </div>

                <div class="card span-4">
                    <h4>Metrics</h4>

                    <div class="metric-card-wrapper">
                        <div class="metric-card">
                            <div class="icon">&#983706;</div>
                            <div class="label">Rendertime</div>
                            <div class="value">{{details.metrics.TaskDuration.toFixed(4)}}s</div>
                        </div>

                        <div class="metric-card">
                            <div class="icon" style="color: #FFC312">&#983838;</div>
                            <div class="label">Script Rendertime</div>
                            <div class="value">{{details.metrics.ScriptDuration.toFixed(4)}}s</div>
                        </div>
                    </div>
                </div>

                <div class="card span-4 primary">
                    <h4>Checklist</h4>

                    <div class="checklist-wrapper">
                        <div class="checklist-item" v-show="details.score.hasFavicon">
                            <div class="icon">&#983340;</div>
                            <div class="text">Favicon</div>
                        </div>
                        <div class="checklist-item" v-show="details.score.hasTitle">
                            <div class="icon">&#983340;</div>
                            <div class="text">Page Title</div>
                        </div>
                        <div class="checklist-item" v-show="details.score.hasDescription">
                            <div class="icon">&#983340;</div>
                            <div class="text">Page Description</div>
                        </div>
                        <div class="checklist-item" v-show="details.score.hasViewport">
                            <div class="icon">&#983340;</div>
                            <div class="text">Mobile Support</div>
                        </div>
                    </div>
                    <div class="checklist-wrapper">
                        <div class="checklist-item" v-show="!details.score.hasFavicon">
                            <div class="icon">&#983382;</div>
                            <div class="text">Favicon</div>
                        </div>
                        <div class="checklist-item" v-show="!details.score.hasTitle">
                            <div class="icon">&#983382;</div>
                            <div class="text">Page Title</div>
                        </div>
                        <div class="checklist-item" v-show="!details.score.hasDescription">
                            <div class="icon">&#983382;</div>
                            <div class="text">Page Description</div>
                        </div>
                        <div class="checklist-item" v-show="!details.score.hasViewport">
                            <div class="icon">&#983382;</div>
                            <div class="text">Mobile Support</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-row" v-if="details.metaData.twitterCard.hasTwitterCard || details.metaData.openGraph.hasOpenGraph">
                <div class="card span-6" v-if="details.metaData.twitterCard.hasTwitterCard">
                    <h4>Twitter Appearence</h4>
                    <div class="twitter-summary-card">
                        <img class="image" :src="details.metaData.twitterCard['twitter:image']">
                        <div class="content">
                            <div class="title">{{details.metaData.twitterCard['twitter:title']}}</div>
                            <div class="description">{{details.metaData.openGraph['og:description']}}</div>
                            <div class="url">
                                <div class="icon">&#983865;</div>
                                <div class="text">{{details.url.host}}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card span-6" v-if="details.metaData.openGraph.hasOpenGraph">
                    <h4>Open Graph Appearence</h4>
                    <p>
                        Titel: <b>{{details.metaData.openGraph['og:title']}}</b><br>
                        URL: <a :href="details.metaData.openGraph['og:url']" target="_blank">{{details.metaData.openGraph['og:url']}}</a><br>
                        Description: <b>{{details.metaData.openGraph['og:description']}}</b><br>
                        <img :src="details.metaData.openGraph['og:image']" width="300"><br>
                    </p>
                </div>
            </div>

            <div class="detail-row">
                <div class="card span-6">
                    <h4>Meta Tags</h4>
                    <p v-for="(meta, i) in details.meta" :key="i">
                        Content: <span>{{meta.content}}</span><br>
                        Name: <span>{{meta.name}}</span><br>
                        Property: <span>{{meta.property}}</span><br>
                        Charset: <span>{{meta.charset}}</span><br>
                    </p>
                </div>
                <div class="card span-6">
                    <h4>Images</h4>
                    <p v-for="(image, i) in details.images" :key="i">
                        <img :src="image.href" width="200"><br>
                        src: <span>{{image.src}}</span><br>
                        size: <span>{{image.width}} x {{image.height}}</span><br>
                        visible size: <span>{{image.visibleWidth}} x {{image.visibleHeight}}</span><br>
                        alt: <span>{{image.alt}}</span><br>
                    </p>
                </div>
            </div>

            <!-- <fieldset v-show="details.links.length > 0">
                <legend>Links</legend>
                <p v-for="(link, i) in details.links" :key="i">
                    HREF: <span>{{link.href}}</span><br>
                    <a :href="link.href">{{link.text || 'MISSING'}}</a>
                </p>
            </fieldset>

            <fieldset v-show="details.images.length > 0">
                <legend>Images</legend>
                <p v-for="(image, i) in details.images" :key="i">
                    SRC: <span>{{image.src}}</span><br>
                    Alt-Tag: <b>{{image.alt || 'MISSING'}}</b>
                </p>
            </fieldset> -->
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                details: {"url":{"origin":"https://fireship.io","host":"fireship.io", "href":"https://fireship.io/"},"preview": null, "appleTouchIcon":"/img/favicon.png","links":[{"href":"/pro","text":"PRO"},{"href":"/dashboard","text":"\n          \n\n      "},{"href":"/lessons","text":""},{"href":"/courses","text":""},{"href":"/snippets","text":""},{"href":"/tags","text":""},{"href":"/mission","text":""},{"href":"/contributors","text":""},{"href":"/about","text":""},{"href":"/login","text":""},{"href":"https://fireship.page.link/slack","text":""},{"href":"/tags/angular","text":""},{"href":"/tags/flutter","text":""},{"href":"/tags/firebase","text":""},{"href":"/tags/stripe","text":""},{"href":"/terms","text":""},{"href":"/privacy-policy","text":""},{"href":"/style-guide","text":""},{"href":"/","text":"v0.23.7"},{"href":"/lessons","text":"video lessons"},{"href":"/courses","text":"courses"},{"href":"/snippets","text":"quick snippets"},{"href":"/tags","text":"tags"},{"href":"/dashboard","text":"\n            \n            \n            dashboard\n        "},{"href":"/lessons","text":"EXPLORE"},{"href":"https://itunes.apple.com/us/app/fireship/id1462592372?mt=8","text":""},{"href":"https://play.google.com/store/apps/details?id=io.fireship.quizapp&pcampaignid=MKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1","text":""},{"href":"/courses/react-next-firebase","text":"React Firebase Full Course\nBuild a fullstack server-rendered webapp with React, Next.js, and Firebase\nSTART"},{"href":"/courses/stripe-js","text":"Stripe Payments Master Course\nBuild a fullstack payment solution with Node.js and React.\nSTART"},{"href":"/courses/flutter-firebase","text":"Flutter Firebase App from Scratch\nBuild a complex production-ready app from scratch with Flutter and Firebase"},{"href":"/courses/angular","text":"Angular 9 Firebase Project Course\nBuild a high-performance progressive web application (PWA) with Angular & Firebase"},{"href":"/courses/firestore-data-modeling","text":"Firestore Data Modeling Course\nLearn how to model data and optimize queries with Cloud Firestore."},{"href":"/courses/cloud-functions","text":"Cloud Functions Master Course\nMaster the fundamentals of serverless with Firebase Cloud Functions by building seven different microservices from scratch."},{"href":"/lessons/firebase-quickstart","text":"Firebase - The Basics\nLearn the fundamental concepts needed to start building serious apps with Firebase."},{"href":"/courses","text":"ALL PRO COURSES"},{"href":"https://www.youtube.com/channel/UCsBjURrPoezykLs9EqgamOA","text":"WATCH"},{"href":"https://www.youtube.com/channel/UCsBjURrPoezykLs9EqgamOA","text":""},{"href":"https://fireship.page.link/slack","text":"💬 Slack Chat\n\nJoin our Slack channel with over 3K users for project support, news, and just having fun."},{"href":"https://github.com/fireship-io","text":"👨‍💻 Open Source Code\n\nAlmost every lesson is supported by a GitHub repo. Even this very website is open source."},{"href":"https://github.com/fireship-io","text":"🍰 Cake\n\nThere will be cake! Access additional free stuff like books, stickers, cheat sheets, swag, and more."},{"href":"/lessons/custom-usernames-firebase/","text":""},{"href":"/lessons/custom-usernames-firebase/","text":"Custom Usernames in Firebase"},{"href":"/tags/pro","text":"#pro"},{"href":"/tags/firebase","text":"#firebase"},{"href":"/tags/firestore","text":"#firestore"},{"href":"/tags/authentication","text":"#authentication"},{"href":"/lessons/custom-usernames-firebase/","text":"Custom Usernames in Firebase"},{"href":"/tags/pro","text":"#pro"},{"href":"/tags/firebase","text":"#firebase"},{"href":"/tags/firestore","text":"#firestore"},{"href":"/tags/authentication","text":"#authentication"},{"href":"/lessons/host-website-raspberry-pi/","text":"Host a Website on Raspberry Pi"},{"href":"/tags/rpi","text":"#rpi"},{"href":"/tags/nginx","text":"#nginx"},{"href":"/lessons/pwa-top-features/","text":"Seven Awesome PWA Features"},{"href":"/tags/javascript","text":"#javascript"},{"href":"/tags/pwa","text":"#pwa"},{"href":"/snippets/nextjs-ssr-data-fetching-with-firebase/","text":"Hydrate Next.js Props to Realtime Firestore Data"},{"href":"/tags/react","text":"#react"},{"href":"/tags/ssr","text":"#ssr"},{"href":"/tags/nextjs","text":"#nextjs"},{"href":"/tags/firestore","text":"#firestore"},{"href":"/lessons/wasm-video-to-gif/","text":"Video to GIF with WASM"},{"href":"/tags/wasm","text":"#wasm"},{"href":"/tags/react","text":"#react"},{"href":"/tags/javascript","text":"#javascript"},{"href":"/lessons/fauna-db-quickstart/","text":"FaunaDB Basics"},{"href":"/tags/fauna","text":"#fauna"},{"href":"/tags/javascript","text":"#javascript"},{"href":"/about","text":""},{"href":"https://github.com/fireship-io/fireship.io/tree/master/hugo/content/_index.md","text":"Fix it on GitHub"},{"href":"https://developers.google.com/community/experts/directory/profile/profile-jeff_delaney","text":"Jeff Delaney "},{"href":"https://www.youtube.com/channel/UCsBjURrPoezykLs9EqgamOA","text":""},{"href":"https://twitter.com/fireship_dev","text":""},{"href":"https://github.com/fireship-io","text":""},{"href":"https://fireship.page.link/slack","text":""},{"href":"https://itunes.apple.com/us/app/fireship/id1462592372?mt=8","text":""},{"href":"https://play.google.com/store/apps/details?id=io.fireship.quizapp&pcampaignid=MKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1","text":""},{"href":"/terms","text":"Terms of Service"},{"href":"/privacy-policy","text":"Privacy Policy"},{"href":"https://srv.carbonads.net/ads/click/x/GTND42JJCKAI5KJUCVY4YKQNC6BD4KQ7CTSICZ3JCYYIVK37CTYIC53KC6BI5K3UF67I6K7JCASDLK7MC67DV2Q7HEYIKKQWCTYICKJECTNCYBZ52K?segment=placement:fireshipio;","text":""},{"href":"https://srv.carbonads.net/ads/click/x/GTND42JJCKAI5KJUCVY4YKQNC6BD4KQ7CTSICZ3JCYYIVK37CTYIC53KC6BI5K3UF67I6K7JCASDLK7MC67DV2Q7HEYIKKQWCTYICKJECTNCYBZ52K?segment=placement:fireshipio;","text":"Entwickeln Sie KI-Apps mit wenigen Zeilen Code."},{"href":"http://carbonads.net/?utm_source=fireshipio&utm_medium=ad_via_link&utm_campaign=in_unit&utm_term=carbon","text":"ADS VIA CARBON"}],"internalLinks":["/pro","/dashboard","/lessons","/courses","/snippets","/tags","/mission","/contributors","/about","/login","/tags/angular","/tags/flutter","/tags/firebase","/tags/stripe","/terms","/privacy-policy","/style-guide","/","/courses/react-next-firebase","/courses/stripe-js","/courses/flutter-firebase","/courses/angular","/courses/firestore-data-modeling","/courses/cloud-functions","/lessons/firebase-quickstart","/lessons/custom-usernames-firebase/","/tags/pro","/tags/firestore","/tags/authentication","/lessons/host-website-raspberry-pi/","/tags/rpi","/tags/nginx","/lessons/pwa-top-features/","/tags/javascript","/tags/pwa","/snippets/nextjs-ssr-data-fetching-with-firebase/","/tags/react","/tags/ssr","/tags/nextjs","/lessons/wasm-video-to-gif/","/tags/wasm","/lessons/fauna-db-quickstart/","/tags/fauna"],"metrics":{"Timestamp":21789.093577,"Documents":17,"Frames":8,"JSEventListeners":406,"Nodes":2073,"LayoutCount":28,"RecalcStyleCount":44,"LayoutDuration":0.139358,"RecalcStyleDuration":0.036729,"ScriptDuration":0.421778,"TaskDuration":0.919511,"JSHeapUsedSize":12601828,"JSHeapTotalSize":20897792},"images":[{"src":"/img/google-login.svg","href":"https://fireship.io/img/google-login.svg","alt":"google login","width":150,"height":150,"visibleWidth":0,"visibleHeight":0},{"src":"/img/google-login.svg","href":"https://fireship.io/img/google-login.svg","alt":"google login","width":150,"height":150,"visibleWidth":30,"visibleHeight":30},{"src":"/img/app-store-badge.svg","href":"https://fireship.io/img/app-store-badge.svg","alt":"Get it on the Apple App Store","width":120,"height":40,"visibleWidth":170,"visibleHeight":57},{"src":"https://play.google.com/intl/en/badges/images/generic/en_badge_web_generic.png","href":"https://play.google.com/intl/en/badges/images/generic/en_badge_web_generic.png","alt":"Get it on Google Play","width":646,"height":250,"visibleWidth":210,"visibleHeight":81},{"src":"/courses/react-next-firebase/img/featured.jpg","href":"https://fireship.io/courses/react-next-firebase/img/featured.jpg","alt":null,"width":1920,"height":1080,"visibleWidth":680,"visibleHeight":383},{"src":"/courses/stripe-js/img/featured.jpg","href":"https://fireship.io/courses/stripe-js/img/featured.jpg","alt":null,"width":1280,"height":720,"visibleWidth":680,"visibleHeight":383},{"src":"/courses/flutter-firebase/img/featured.jpg","href":"https://fireship.io/courses/flutter-firebase/img/featured.jpg","alt":null,"width":1080,"height":608,"visibleWidth":680,"visibleHeight":383},{"src":"/courses/angular/img/featured.jpg","href":"https://fireship.io/courses/angular/img/featured.jpg","alt":null,"width":1920,"height":1080,"visibleWidth":680,"visibleHeight":383},{"src":"/courses/firestore-data-modeling/img/featured.jpg","href":"https://fireship.io/courses/firestore-data-modeling/img/featured.jpg","alt":null,"width":720,"height":405,"visibleWidth":680,"visibleHeight":383},{"src":"/courses/cloud-functions/img/featured.jpg","href":"https://fireship.io/courses/cloud-functions/img/featured.jpg","alt":null,"width":1920,"height":1080,"visibleWidth":680,"visibleHeight":383},{"src":"/lessons/firebase-quickstart/img/featured.jpg","href":"https://fireship.io/lessons/firebase-quickstart/img/featured.jpg","alt":null,"width":720,"height":405,"visibleWidth":680,"visibleHeight":383},{"src":"/img/testimonial.png","href":"https://fireship.io/img/testimonial.png","alt":"Testimonials for Fireship.io code tutorials","width":1000,"height":396,"visibleWidth":1000,"visibleHeight":396},{"src":"img/pages/yt.jpg","href":"https://fireship.io/img/pages/yt.jpg","alt":"old film gif for video episodes on Fireship.io","width":800,"height":445,"visibleWidth":800,"visibleHeight":445},{"src":"/lessons/custom-usernames-firebase//img/featured.webp","href":"https://fireship.io/lessons/custom-usernames-firebase//img/featured.webp","alt":null,"width":1000,"height":563,"visibleWidth":650,"visibleHeight":366},{"src":"/img/pages/jeffdelaney-gde-2020.webp","href":"https://fireship.io/img/pages/jeffdelaney-gde-2020.webp","alt":"Jeff Delaney Google Developer Expert","width":500,"height":500,"visibleWidth":300,"visibleHeight":300},{"src":"/img/app-store-badge.svg","href":"https://fireship.io/img/app-store-badge.svg","alt":"Get it on the Apple App Store","width":120,"height":40,"visibleWidth":170,"visibleHeight":57},{"src":"https://play.google.com/intl/en/badges/images/generic/en_badge_web_generic.png","href":"https://play.google.com/intl/en/badges/images/generic/en_badge_web_generic.png","alt":"Get it on Google Play","width":646,"height":250,"visibleWidth":210,"visibleHeight":81},{"src":"/img/google-login.svg","href":"https://fireship.io/img/google-login.svg","alt":"google login","width":150,"height":150,"visibleWidth":24,"visibleHeight":24},{"src":"/img/apple-login.svg","href":"https://fireship.io/img/apple-login.svg","alt":"apple login","width":1000,"height":1187,"visibleWidth":24,"visibleHeight":28},{"src":"https://cdn4.buysellads.net/uu/1/82155/1608737998-AZR_NAT-FreeAcct-Prosp-AI-NA-3_USA_1200x627_NAT_AZR-Direct_EN_Trial_Standard_SBAN_SINU_Device_Offer.png","href":"https://cdn4.buysellads.net/uu/1/82155/1608737998-AZR_NAT-FreeAcct-Prosp-AI-NA-3_USA_1200x627_NAT_AZR-Direct_EN_Trial_Standard_SBAN_SINU_Device_Offer.png","alt":"ads via Carbon","width":260,"height":200,"visibleWidth":130,"visibleHeight":100},{"src":"https://ad.doubleclick.net/ddm/trackimp/N572608.452584BUYSELLADS.COM/B25163184.290532778;dc_trk_aid=483875582;dc_trk_cid=142952668;ord=161401449;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=;gdpr=$;gdpr_consent=$?","href":"https://ad.doubleclick.net/ddm/trackimp/N572608.452584BUYSELLADS.COM/B25163184.290532778;dc_trk_aid=483875582;dc_trk_cid=142952668;ord=161401449;dc_lat=;dc_rdid=;tag_for_child_directed_treatment=;tfua=;gdpr=$;gdpr_consent=$?","alt":"ads via Carbon","width":1,"height":1,"visibleWidth":0,"visibleHeight":0},{"src":"https://pixel.adsafeprotected.com/rfw/st/552031/51166066/skeleton.gif?gdpr=$&gdpr_consent=$&gdpr_pd=$&network=BUYSELLADS","href":"https://pixel.adsafeprotected.com/rfw/st/552031/51166066/skeleton.gif?gdpr=$&gdpr_consent=$&gdpr_pd=$&network=BUYSELLADS","alt":"ads via Carbon","width":1,"height":1,"visibleWidth":0,"visibleHeight":0}],"meta":[{"name":"generator","property":null,"content":"Hugo 0.74.2","charset":null,"httpEquiv":null},{"name":null,"property":null,"content":null,"charset":"utf-8","httpEquiv":null},{"name":null,"property":null,"content":"IE=edge,chrome=1","charset":null,"httpEquiv":"X-UA-Compatible"},{"name":"HandheldFriendly","property":null,"content":"True","charset":null,"httpEquiv":null},{"name":"MobileOptimized","property":null,"content":"320","charset":null,"httpEquiv":null},{"name":"viewport","property":null,"content":"width=device-width,minimum-scale=1","charset":null,"httpEquiv":null},{"name":"description","property":null,"content":"Training and Consulting for App Developers | Full Courses, Video Lessons, Chat, and More","charset":null,"httpEquiv":null},{"name":"image","property":null,"content":"https://fireship.io/img/covers/default.png","charset":null,"httpEquiv":null},{"name":null,"property":"og:title","content":"Fireship.io","charset":null,"httpEquiv":null},{"name":null,"property":"og:type","content":"article","charset":null,"httpEquiv":null},{"name":null,"property":"og:url","content":"https://fireship.io/","charset":null,"httpEquiv":null},{"name":null,"property":"og:image","content":"https://fireship.io/img/covers/default.png","charset":null,"httpEquiv":null},{"name":null,"property":"og:description","content":"Training and Consulting for App Developers | Full Courses, Video Lessons, Chat, and More","charset":null,"httpEquiv":null},{"name":"twitter:card","property":null,"content":"summary","charset":null,"httpEquiv":null},{"name":"twitter:site","property":null,"content":"@fireship_dev","charset":null,"httpEquiv":null},{"name":"twitter:title","property":null,"content":"Fireship.io","charset":null,"httpEquiv":null},{"name":"twitter:description","property":null,"content":"Training and Consulting for App Developers | Full Courses, Video Lessons, Chat, and More","charset":null,"httpEquiv":null},{"name":"twitter:image","property":null,"content":"https://fireship.io/img/covers/default.png","charset":null,"httpEquiv":null},{"name":"theme-color","property":null,"content":"#454e56","charset":null,"httpEquiv":null}],"srcLinks":[{"rel":"prefetch","href":"/components/wc.1612814468946.js","title":null},{"rel":"stylesheet","href":"https://use.typekit.net/rcr1opg.css","title":null},{"rel":"stylesheet","href":"/design/css/app.a09486ce40f5c7e7bb47.css","title":null},{"rel":"canonical","href":"https://fireship.io/","title":null},{"rel":"shortcut icon","href":"/img/favicon.png","title":null},{"rel":"apple-touch-icon","href":"/img/favicon.png","title":null},{"rel":"manifest","href":"/manifest.json","title":null},{"rel":"prefetch","href":"https://fireship.io/pro","title":null},{"rel":"prefetch","href":"https://fireship.io/lessons","title":null},{"rel":"prefetch","href":"https://fireship.io/courses","title":null},{"rel":"prefetch","href":"https://fireship.io/snippets","title":null},{"rel":"prefetch","href":"https://fireship.io/tags","title":null},{"rel":"prefetch","href":"https://fireship.io/mission","title":null},{"rel":"prefetch","href":"https://fireship.io/contributors","title":null},{"rel":"prefetch","href":"https://fireship.io/about","title":null},{"rel":"prefetch","href":"https://fireship.io/login","title":null},{"rel":"prefetch","href":"https://fireship.io/tags/angular","title":null},{"rel":"prefetch","href":"https://fireship.io/tags/flutter","title":null},{"rel":"prefetch","href":"https://fireship.io/tags/firebase","title":null},{"rel":"prefetch","href":"https://fireship.io/tags/stripe","title":null},{"rel":"prefetch","href":"https://fireship.io/terms","title":null},{"rel":"prefetch","href":"https://fireship.io/privacy-policy","title":null},{"rel":"prefetch","href":"https://fireship.io/style-guide","title":null},{"rel":"prefetch","href":"https://fireship.io/","title":null},{"rel":"prefetch","href":"https://fireship.io/courses/react-next-firebase","title":null},{"rel":"prefetch","href":"https://fireship.io/courses/stripe-js","title":null}],"headings":[{"tag":"H3","text":""},{"tag":"H3","text":""},{"tag":"H3","text":""},{"tag":"H1","text":"Build and ship 🔥 your app ⚡ faster"},{"tag":"H2","text":"New Courses"},{"tag":"H4","text":"BRAND NEW COURSES AND UPDATES EVERY MONTH"},{"tag":"H3","text":"React Firebase Full Course"},{"tag":"H3","text":"Stripe Payments Master Course"},{"tag":"H2","text":"Learn by Doing"},{"tag":"H4","text":"FAST, EFFICIENT, PROJECT-BASED VIDEO COURSES"},{"tag":"H3","text":"Flutter Firebase App from Scratch"},{"tag":"H3","text":"Angular 9 Firebase Project Course"},{"tag":"H2","text":"Modern Fullstack"},{"tag":"H4","text":"DEVELOP FASTER WITH SCALABLE CLOUD INFRASTRUCTURE"},{"tag":"H3","text":"Firestore Data Modeling Course"},{"tag":"H3","text":"Cloud Functions Master Course"},{"tag":"H2","text":"New to Firebase?"},{"tag":"H4","text":"LEARN THE BASICS IN 25 MINUTES 👇"},{"tag":"H3","text":"Firebase - The Basics"},{"tag":"H2","text":"Trusted by Developers"},{"tag":"H4","text":"\"I HAVE A JOB THANKS TO YOU\""},{"tag":"H2","text":" Weekly Video Episodes"},{"tag":"H2","text":"Not your Average Code Tutorials"},{"tag":"H4","text":"ALL LESSONS ARE BACKED BY EXPERT SUPPORT"},{"tag":"H2","text":"💬 Slack Chat"},{"tag":"H2","text":"👨‍💻 Open Source Code"},{"tag":"H2","text":"🍰 Cake"},{"tag":"H2","text":"Watch the Latest Lessons"},{"tag":"H4","text":"NEW VIDEO CONTENT EVERY FEW DAYS"},{"tag":"H3","text":"Custom Usernames in Firebase"},{"tag":"H3","text":"Custom Usernames in Firebase"},{"tag":"H3","text":"Host a Website on Raspberry Pi"},{"tag":"H3","text":"Seven Awesome PWA Features"},{"tag":"H3","text":"Hydrate Next.js Props to Realtime Firestore Data"},{"tag":"H3","text":"Video to GIF with WASM"},{"tag":"H3","text":"FaunaDB Basics"},{"tag":"H2","text":" Your Host"},{"tag":"H3","text":"Create Account with Email"}],"score":{"totalPageScore":100,"hasTitle":true,"hasDescription":true,"hasFavicon":true,"hasViewport":true},"metaData":{"openGraph":{"og:title":"Fireship.io","og:type":"article","og:url":"https://fireship.io/","og:image":"https://fireship.io/img/covers/default.png","og:description":"Training and Consulting for App Developers | Full Courses, Video Lessons, Chat, and More","hasOpenGraph":true},"twitterCard":{"twitter:card":"summary","twitter:site":"@fireship_dev","twitter:title":"Fireship.io","twitter:description":"Training and Consulting for App Developers | Full Courses, Video Lessons, Chat, and More","twitter:image":"https://fireship.io/img/covers/default.png","hasTwitterCard":true},"viewport":"width=device-width,minimum-scale=1","description":"Training and Consulting for App Developers | Full Courses, Video Lessons, Chat, and More","generator":"Hugo 0.74.2","themeColor":"#454e56","keywords":[],"title":"Fireship.io","favicon":"https://fireship.io/img/favicon.png"}},

                series: [67],
                chartOptions: {
                    chart: {
                        height: 350,
                        type: 'radialBar',
                    },
                    plotOptions: {
                        radialBar: {
                            hollow: {
                                size: '80%',
                            },
                            dataLabels: {
                                name: {
                                    show: true,
                                    label: 'Page Score',
                                    fontSize: '30px',
                                    fontFamily: 'Roboto',
                                    fontWeight: 'bold',
                                    color: 'var(--heading-gray)',
                                },

                                value: {
                                    show: true,
                                    fontSize: '20px',
                                    fontFamily: 'Roboto',
                                    color: 'var(--text-gray)',
                                    formatter: function (value) {
                                        return value + ' / 100'
                                    },
                                },
                            },
                        },
                    },
                    colors: [function({value}) {
                        if      (value > 80) return 'var(--success)'
                        else if (value > 50) return 'var(--warning)'
                        else if (value > 20) return '#ff7f50'
                        else                 return 'var(--error)'
                    }],
                    labels: ['Page Score'],
                    states: {
                        hover: {
                            filter: {
                                type: 'none',
                            }
                        },
                    },
                },
            }
        },

        methods: {
            openDetails(details) {
                console.log(details)
                this.details = details
            },
        },

        components: {
            QuickCheckModule: require('../components/QuickCheckModule.vue').default,
        },
    }
</script>

<style lang="sass" scoped>
    .page-container
        width: 100%

        .details
            width: 100%
            display: flex
            flex-direction: column
            gap: 30px

            .nav-row
                height: 40px
                margin-top: 30px

            .detail-row
                display: grid
                grid-template-columns: repeat(12, 1fr)
                grid-template-rows: 1fr
                gap: 30px

            .card
                background: var(--bg)
                border-radius: 5px
                font-size: var(--text-size)
                filter: var(--elevation-2)
                overflow: auto

                h1, h2, h3, h4, h5, h6
                    margin: 0
                    color: var(--primary)
                    width: 100%
                    padding: 7px 15px 0

                &.center
                    display: grid
                    place-content: center

                &.primary
                    background: var(--primary)
                    color: #ffffffdd

                    h1, h2, h3, h4, h5, h6
                        color: white
                        border-bottom: var(--border)
                        border-color: #ffffff40

                &.span-2
                    grid-column: span 2

                &.span-3
                    grid-column: span 3

                &.span-4
                    grid-column: span 4

                &.span-5
                    grid-column: span 5

                &.span-6
                    grid-column: span 6

                &.span-7
                    grid-column: span 7

                &.span-8
                    grid-column: span 8

                &.span-9
                    grid-column: span 9

                &.span-12
                    grid-column: span 12

                &.page-info-card
                    overflow: hidden

                    .images
                        grid-area: images
                        width: 100%
                        height: 100%
                        position: relative

                        .preview
                            width: 100%
                            height: 100%
                            object-fit: contain
                            filter: saturate(0%)

                        .favicon
                            height: 50px
                            width: 50px
                            border-radius: 100%
                            position: absolute
                            top: 10px
                            left: 10px
                            background: white
                            padding: 10px
                            border: var(--border)

                    .content
                        font-size: var(--text-size)
                        color: var(--text-gray)

                p
                    padding: 0 15px

                .metric-card-wrapper
                    width: 100%
                    padding: 7.5px

                .metric-card
                    // border: var(--border)
                    background: linear-gradient(90deg, var(--bg-dark) 0%, var(--bg) 70%)
                    border-radius: 5px
                    display: inline-grid
                    width: calc(100% - 15px)
                    margin: 7.5px
                    padding: 5px 0
                    grid-template: auto 1fr / 50px 1fr
                    grid-template-areas: "icon label" "icon value"

                    &.half
                        width: calc(50% - 15px)

                    .icon
                        grid-area: icon
                        font-size: 25px
                        color: var(--primary)
                        font-family: 'Material Icons'
                        align-self: center
                        justify-self: center
                        user-select: none

                    .label
                        grid-area: label
                        font-size: var(--text-size)
                        color: var(--text-gray)
                        align-self: end
                        height: 18px
                        line-height: 18px
                        margin-top: 3px

                    .value
                        grid-area: value
                        font-size: var(--text-size)
                        color: var(--heading-gray)
                        align-self: top
                        font-weight: 600

                .checklist-wrapper
                    width: 100%
                    padding: 10px 0

                .checklist-item
                    width: 100%
                    display: flex
                    height: 36px
                    padding: 0 15px
                    gap: 15px
                    align-items: center
                    user-select: none

                    .icon
                        font-size: 20px
                        color: #ffffffaa
                        font-family: 'Material Icons'
                        text-align: center

                    .text
                        font-size: var(--button-size)
                        font-weight: 600
                        color: white
                        flex: 1
                        text-transform: uppercase
                        letter-spacing: 1px

                .twitter-summary-card
                    display: grid
                    margin: 15px
                    border-radius: 12px
                    grid-template: 125px / 125px auto
                    grid-template-areas: "image content"
                    align-items: center
                    border: var(--border)
                    overflow: hidden

                    .image
                        grid-area: image
                        width: 100%
                        height: 100%
                        object-fit: cover
                        border-right: var(--border)

                    .content
                        grid-area: content
                        padding: 8px 10px
                        display: block

                        .title
                            display: block
                            font-size: var(--text-size)
                            color: var(--heading-gray)
                            margin-bottom: 3px

                        .description
                            display: block
                            font-size: var(--text-size)
                            color: var(--text-gray)
                            line-height: 130%
                            margin-bottom: 3px

                        .url
                            display: flex
                            align-items: center

                            .icon
                                font-size: 16px
                                width: 22px
                                color: var(--text-gray)
                                font-family: 'Material Icons'
                                text-align: left
                                user-select: none

                            .text
                                font-size: var(--text-size)
                                color: var(--text-gray)

            fieldset
                font-size: var(--text-size)
                border: var(--border)
                border-radius: 5px
                display: inline-block
                width: 100%
                margin: 0
                vertical-align: top
                overflow: hidden

                &.half
                    width: 50%

                p
                    margin: 0
                    margin-bottom: 15px
</style>