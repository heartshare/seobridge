<template>
    <div class="ccc-container block">
        <p class="wrapper">
            <ui-text-input class="url-input" label="URL" v-model="url"></ui-text-input>
            <ui-button class="submit-button" icon="&#983881;" @click="initiateScan(url)">Analyse</ui-button>
        </p>

        <div class="report block" v-if="reports.length > 0" v-show="!loading">
            <div class="page-card" v-for="(report, i) in reports" :key="i">
                <img class="thumbnail" :src="report.preview">
                <div class="score-container">
                    <div class="score">{{report.score.totalPageScore}}</div>
                    <ui-progress-ring size="40" back-color="#ffffff30" :color="scoreColor(report.score.totalPageScore)" :progress="report.score.totalPageScore"></ui-progress-ring>
                </div>
                <div class="text-container">
                    <img class="favicon" v-if="report.metaData.favicon" :src="report.metaData.favicon">
                    <div class="title" :title="report.metaData.title">{{report.metaData.title}}</div>
                    <div class="description" :title="report.metaData.description">{{report.score.hasDescription ? report.metaData.description : 'MISSING'}}</div>
                    <ui-button class="details-button" icon="none" border light small @click="$emit('details', report)">View Details</ui-button>
                </div>
            </div>
        </div>

        <ui-spinner style="display: block; margin: 20px auto" v-show="loading" color="var(--primary)" :size="50"></ui-spinner>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                url: 'https://fireship.io/',
                reports: [],
                loading: false
            }
        },

        methods: {
            scan(url) {
                axios.post('https://puppeteer.seobridge.test/analyse?url='+url)
                .then(response => {
                    // console.log(response.data)
                    this.loading = false
                    this.reports.push(response.data)
                })
                .catch(error => {
                    this.loading = false
                })
            },

            initiateScan(url) {
                this.loading = true
                this.reports = []
                this.scan(url)

                return

                setTimeout(() => {
                    let url = new URL(this.reports[0].url.origin)
                    for (const link of this.reports[0].internalLinks)
                    {
                        url.pathname = link
                        this.scan(url.href)
                    }
                }, 6000);
            },

            scoreColor(score = 0)
            {
                if (score >= 80)
                {
                    return 'var(--success)'
                }
                else if (score >= 50)
                {
                    return 'var(--warning)'
                }
                else if (score >= 20)
                {
                    return '#ff7f50'
                }
                else
                {
                    return 'var(--error)'
                }
            }
        },

        components: {},
    }
</script>

<style lang="sass" scoped>
    .ccc-container
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
                transition: filter 100ms

            &:hover
                .thumbnail
                    filter: saturate(100%)

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
            display: flex
            gap: 15px 15px
            justify-content: flex-start
            flex-wrap: wrap
</style>