<template>
    <div class="page-row">
        <div class="favicon-container">
            <object class="favicon" v-if="report.data.metaData.favicon" :data="report.data.metaData.favicon" type="image/png">
                <img src="/images/defaults/default_icon.svg" alt="Default Icon Fallback" width="100%" height="100%">
            </object>
            <img class="favicon" v-else src="/images/defaults/default_icon.svg" alt="Default Icon Fallback" width="100%" height="100%">

            <ui-progress-ring class="progress-ring" stroke="2" back-color="var(--bg-dark)" :progress="report.score" :size="40" :color="scoreColor(report.score)"></ui-progress-ring>
        </div>

        <div class="title" :title="report.data.metaData.title">{{report.data.metaData.title}}</div>

        <div class="description" v-if="report.data.score.hasDescription" :title="report.data.metaData.description">{{report.data.metaData.description}}</div>
        <i class="description" v-else><b>NO DESCRIPTION</b></i>
        
        <ui-button text class="details-button" @click="$emit('details', report.id)">Details</ui-button>
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
    .page-row
        width: 100%
        font-size: 0
        position: relative
        vertical-align: top
        display: flex
        align-items: center
        gap: 15px
        padding: 0 15px

        .favicon-container
            position: relative
            height: 40px
            width: 40px

            .progress-ring
                position: absolute
                top: 0
                left: 0

            .favicon
                position: absolute
                top: 3px
                left: 3px
                height: 34px
                width: 34px
                border-radius: 40px
                background: white
                padding: 5px
                object-fit: contain

        .title
            width: 300px
            font-size: 17px
            color: var(--heading-gray)
            overflow: hidden
            text-overflow: ellipsis
            white-space: nowrap

        .description
            flex: 2
            font-size: 14px
            color: var(--text-gray)
            line-height: 150%
            overflow: hidden
            padding-right: 20px

        .details-button
            justify-self: center
</style>