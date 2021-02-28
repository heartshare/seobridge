<template>
    <div class="ccc-container block">
        <p>
            <ui-select-input label="Viewport" :options="[{'1080p':'1920 x 1080'}, {'720p':'1280 x 720'}]"></ui-select-input>
        </p>
        <p class="wrapper">
            <ui-text-input class="url-input" label="URL" v-model="url"></ui-text-input>
            <ui-button class="submit-button" :loading="loading" icon="&#983881;" @click="$store.dispatch('createReport', url)">Analyse</ui-button>
        </p>

        <div class="report block" v-if="reports.length > 0">
            <div class="page-card" v-for="(report, i) in reports" :key="i">
                <img class="thumbnail" :src="report.data.preview">
                <div class="score-container">
                    <div class="score">{{report.data.score.totalPageScore}}</div>
                    <ui-progress-ring size="40" back-color="#ffffff30" :color="scoreColor(report.data.score.totalPageScore)" :progress="report.data.score.totalPageScore"></ui-progress-ring>
                </div>
                <div class="text-container">
                    <img class="favicon" v-if="report.data.metaData.favicon" :src="report.data.metaData.favicon">
                    <div class="title" :title="report.data.metaData.title">{{report.data.metaData.title}}</div>
                    <div class="description" :title="report.data.metaData.description">{{report.data.score.hasDescription ? report.data.metaData.description : 'MISSING'}}</div>
                    <ui-button class="details-button" border text small @click="$emit('details', report.data)">View Details</ui-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                url: 'https://fireship.io/',
            }
        },

        computed: {
            reports() {
                return this.$store.getters.reports
            },

            loading() {
                return this.$store.getters.reportScanning
            }
        },

        methods: {
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