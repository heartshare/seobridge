<template>
    <div class="page-card">
        <img class="thumbnail" src="/images/defaults/default_preview.svg">
        <!-- <img class="thumbnail" :src="report.data.preview"> -->
        <div class="score-container" v-show="false">
            <div class="score">{{report.data.score.totalPageScore}}</div>
            <ui-progress-ring size="40" back-color="#ffffff30" :color="scoreColor(report.data.score.totalPageScore)" :progress="report.data.score.totalPageScore"></ui-progress-ring>
        </div>
        <div class="text-container">
            <object class="favicon" :data="report.data.metaData.favicon" type="image/png">
                <img src="/images/defaults/default_icon.svg" alt="Default Icon Fallback" width="100%" height="100%">
            </object>
            <div class="title" :title="report.data.metaData.title">{{report.data.metaData.title}}</div>
            <div class="description" :title="report.data.metaData.description">{{report.data.score.hasDescription ? report.data.metaData.description : 'MISSING'}}</div>
            <ui-button class="details-button" text @click="$emit('details', report.data)">Details</ui-button>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            report: Object
        },

        methods: {
            scoreColor(score = 0) {
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
            },
        }
    }
</script>

<style lang="sass" scoped>
    .page-card
        width: 230px
        height: 305px
        background: var(--bg)
        border-radius: 5px
        // filter: var(--elevation-1)
        border: 1px solid #00000020
        font-size: 0
        position: relative
        vertical-align: top
        
        .thumbnail
            width: 100%
            height: 130px
            object-fit: cover
            border-radius: 5px 5px 0 0
            background: var(--bg-dark)
            display: block

        .score-container
            position: absolute
            z-index: 1
            top: 15px
            left: 15px
            height: 40px
            width: 40px
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
            grid-template: 25px 30px auto 35px / 1fr
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
                justify-self: center
</style>