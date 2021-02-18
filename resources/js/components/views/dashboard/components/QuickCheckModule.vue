<template>
    <div class="container block">
        <p class="wrapper">
            <ui-text-input class="url-input" label="URL" v-model="url"></ui-text-input>
            <ui-button class="submit-button" icon="&#983881;" @click="analyse(url)">Analyse</ui-button>
        </p>

        <div class="report block" v-if="reports.length > 0" v-show="!loading">
            <div class="page-card" v-for="(report, i) in reports" :key="i">
                <img class="thumbnail" :src="report.preview">
                <div class="score-container">
                    <div class="score">100</div>
                    <ui-progress-ring size="40" color="var(--success)" :progress="100"></ui-progress-ring>
                </div>
                <div class="text-container">
                    <img class="favicon" v-if="report.favicon" :src="report.url.origin+report.favicon">
                    <div class="title" :title="report.title">{{report.title}}</div>
                    <div class="description" :title="report.metaDescription">{{report.metaDescription ? report.metaDescription : 'MISSING'}}</div>
                    <ui-button class="details-button" icon="none" border light small>View Details</ui-button>
                </div>
            </div>
            <!-- <h1>{{report.title}}</h1>
            <p>
                <a :href="report.url.href" target="_blank">{{report.url.href}}</a><br>
                Description: <b>{{report.metaDescription}}</b><br>
                Favicon: <b>{{report.url.origin+report.favicon}}</b><br>
                <img :src="report.url.origin+report.favicon" width="30">
                <img :src="report.preview" width="200">
            </p>

            <fieldset v-show="report.srcLinks.length > 0">
                <legend>SRC Links</legend>
                <p v-for="(image, i) in report.srcLinks" :key="i">
                    REL: <span>{{image.rel}}</span><br>
                    HREF: <span>{{image.href}}</span><br>
                    Title: <span>{{image.title}}</span><br>
                </p>
            </fieldset>

            <fieldset v-show="report.links.length > 0">
                <legend>Links</legend>
                <p v-for="(link, i) in report.links" :key="i">
                    HREF: <span>{{link.href}}</span><br>
                    <a :href="link.href">{{link.text || 'MISSING'}}</a>
                </p>
            </fieldset>

            <fieldset v-show="report.images.length > 0">
                <legend>Images</legend>
                <p v-for="(image, i) in report.images" :key="i">
                    SRC: <span>{{image.src}}</span><br>
                    Alt-Tag: <b>{{image.alt || 'MISSING'}}</b>
                </p>
            </fieldset> -->

        </div>

        <ui-spinner style="display: block; margin: 20px auto" v-show="loading" color="var(--primary)" :size="50"></ui-spinner>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                url: 'https://fireship.io/courses/react-next-firebase/',
                reports: [],
                loading: false
            }
        },

        methods: {
            analyse(url) {
                this.loading = true

                axios.post('https://puppeteer.seobridge.test/analyse?url='+url)
                .then(response => {
                    console.log(response.data)
                    this.loading = false
                    this.reports = response.data
                })
                .catch(error => {
                    this.loading = false
                })
            },
        },

        components: {},
    }
</script>

<style lang="sass" scoped>
    .container
        .wrapper
            position: relative

            .url-input
                padding-right: 130px

            .submit-button
                position: absolute
                top: 5px
                right: 5px

        .page-card
            width: 230px
            height: 300px
            background: var(--bg)
            border-radius: 5px
            filter: var(--elevation-1)
            font-size: 0
            position: relative
            
            .thumbnail
                width: 100%
                height: 130px
                object-fit: cover
                border-radius: 5px 5px 0 0
                background: var(--bg-dark)
                display: block
                filter: saturate(0%)

            .score-container
                position: absolute
                z-index: 1
                top: 10px
                right: 10px
                height: 46px
                width: 46px
                border-radius: 50px
                background: #00000050
                backdrop-filter: blur(8px)
                display: grid
                place-content: center

                .score
                    color: white
                    font-size: 14px
                    font-weight: 700
                    position: absolute
                    top: 50%
                    left: 50%
                    transform: translate(-50%, -50%)

            .text-container
                width: 100%
                height: calc(100% - 130px)
                padding: 0 15px 15px
                display: grid
                position: relative
                z-index: 1
                grid-template: 25px 30px auto 30px / 1fr
                grid-template-areas: "favicon" "title" "description" "button"
                
                .favicon
                    height: 40px
                    width: 40px
                    border-radius: 40px
                    background: white
                    margin-top: -20px
                    margin-bottom: 5px
                    padding: 5px
                    display: block
                    grid-area: favicon

                .title
                    font-size: 20px
                    color: var(--heading-gray)
                    overflow: hidden
                    text-overflow: ellipsis
                    white-space: nowrap
                    grid-area: title

                .description
                    font-size: 14px
                    color: var(--text-gray)
                    line-height: 150%
                    overflow: hidden
                    margin-bottom: 10px
                    grid-area: description

                .details-button
                    grid-area: button

        .report
            font-size: var(--text-size)

            fieldset
                border: var(--border)
                border-radius: 5px
                display: block
                width: 100%
                overflow: hidden
</style>