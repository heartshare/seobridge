<template>
    <div class="page-row">
        <object class="favicon" v-if="report.data.metaData.favicon" :data="report.data.metaData.favicon" type="image/png">
            <img src="/images/defaults/default_icon.svg" alt="Default Icon Fallback" width="100%" height="100%">
        </object>
        <img class="favicon" v-else src="/images/defaults/default_icon.svg" alt="Default Icon Fallback" width="100%" height="100%">

        <div class="title" :title="report.data.metaData.title">{{report.data.metaData.title}}</div>

        <div class="description" :title="report.data.metaData.description">{{report.data.score.hasDescription ? report.data.metaData.description : 'MISSING'}}</div>
        
        <ui-button class="details-button" icon="&#983362;" @click="$emit('details', report.data)">Details</ui-button>
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
        padding: 5px 5px 5px 0
        gap: 10px

        .favicon
            height: 40px
            width: 40px
            border-radius: 40px
            background: white
            padding: 5px
            object-fit: contain

        .title
            flex: 1
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