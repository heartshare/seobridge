<template>
    <div class="job-wrapper">
            <div class="job-header">
                <object class="favicon" v-if="reportGroup.reports[0].data.metaData.favicon" :data="reportGroup.reports[0].data.metaData.favicon" type="image/png">
                <img src="/images/defaults/default_icon.svg" alt="Default Icon Fallback" width="100%" height="100%">
            </object>
            <img class="favicon" v-else src="/images/defaults/default_icon.svg" alt="Default Icon Fallback" width="100%" height="100%">

            <div class="title">{{reportGroup.host}}</div>

            <div class="indicator">
                <div class="indicator-part status" v-if="reportGroup.has_been_shared && !reportGroup.has_been_assigned">Shared</div>
                <div class="indicator-part status" v-else-if="reportGroup.has_been_assigned">Assigned</div>
                <!-- <div class="indicator-part error">
                    <div class="icon">cancel</div>
                    10
                </div>
                <div class="indicator-part warning">
                    <div class="icon">warning</div>
                    15
                </div>
                <div class="indicator-part info">
                    <div class="icon">info</div>
                    67
                </div> -->
            </div>
            
            <div class="spacer"></div>

            <div class="timestamp" v-tooltip="formateDate(reportGroup.created_at)">{{reportGroup.created_at | diffForHumans}}</div>
            
            <ui-popover-menu>
                <template v-slot:trigger>
                    <ui-icon-button class="more-button">&#983513;</ui-icon-button>
                </template>

                <ui-menu-item icon="&#984214;" v-if="reportGroup.is_own" @click="$emit('share', reportGroup)">Share report</ui-menu-item>
                <ui-menu-item icon="&#983048;" v-if="reportGroup.is_own" @click="$emit('assign', reportGroup)">Assign report</ui-menu-item>
                <ui-menu-item icon="&#983881;" @click="$emit('searchForDomain', reportGroup.host)">Search for domain</ui-menu-item>
                <ui-menu-divider></ui-menu-divider>
                <ui-menu-item icon="&#984089;" @click="$emit('newFromDomain', reportGroup.url)">New report from URL</ui-menu-item>
                <ui-menu-divider v-if="reportGroup.is_own"></ui-menu-divider>
                <ui-menu-item icon="&#985721;" v-if="reportGroup.is_own" @click="$emit('delete', reportGroup)">Delete report</ui-menu-item>
            </ui-popover-menu>

            <ui-button class="show-all-button" v-if="isPreview" text border @click="$emit('openGroup', reportGroup.id)">Show all ({{reportGroup.reports_count}})</ui-button>
        </div>

        <div class="job-pages">
            <div class="status" v-if="reportGroup.task && reportGroup.task.status !== 'completed'">
                <div class="label">Status:</div>
                
                <div class="status-text warning" v-if="reportGroup.task.status === 'crawling'">Crawling for links</div>
                <div class="status-text warning" v-else-if="reportGroup.task.status === 'crawling_completed'">Links crawled</div>
                <div class="status-text warning" v-else-if="reportGroup.task.status === 'fetching'">Analysing pages</div>
                <div class="status-text success" v-else-if="reportGroup.task.status === 'completed'">Scan complete</div>

                <ui-spinner class="status-spinner"></ui-spinner>

                <div class="spacer"></div>

                <ui-button small text>Cancel</ui-button>
            </div>

            <report-row v-for="report in reportGroup.reports.slice(0, limit)" :key="'report_'+report.id" :report="report" @details="$emit('openDetails', {group: reportGroup.id, report: $event})"></report-row>
            
            <div class="blend" v-if="reportGroup.reports_count > limit"></div>
        </div>
    </div>
</template>

<script>
    const dayjs = require('dayjs')
    const relativeTime = require('dayjs/plugin/relativeTime')

    export default {
        props: {
            reportGroup: Object,
            limit: Number,
            isPreview: Boolean,
        },

        filters: {
            diffForHumans(date) {
                if (!date)
                {
                    return null
                }
                
                return dayjs(date).fromNow()
            },
        },

        methods: {
            formateDate(date) {
                if (!date)
                {
                    return null
                }
                
                return new Date(date).toLocaleString(undefined, {year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})
            },
        },

        components: {
            ReportRow: require('./ReportRow.vue').default,
        }
    }
</script>

<style lang="sass" scoped>
    .job-wrapper
        width: 100%
        display: inline-flex
        flex-direction: column
        transition: all 300ms
        position: relative
        border-bottom: var(--border)

        &:last-of-type
            border-bottom: none

        &.slide-enter
            transform: translateY(-100px)
            opacity: 0

        &.slide-leave-to
            transform: scale(0)
            opacity: 0

        &.slide-leave-active
            position: absolute

        .job-header
            background: var(--bg-dark)
            display: flex
            align-items: center
            padding: 15px
            gap: 15px

            .favicon
                height: 40px
                width: 40px
                border-radius: 40px
                padding: 5px
                object-fit: contain

            .spacer
                flex: 2

            .title
                width: 300px
                font-size: 16px
                line-height: 20px
                font-weight: 600
                text-transform: uppercase
                color: var(--heading-gray)
                white-space: nowrap
                overflow: hidden
                text-overflow: ellipsis

            .indicator
                display: flex
                height: 30px
                align-items: center

                .indicator-part
                    background: var(--text-gray)
                    display: flex
                    align-items: center
                    padding: 0 10px
                    gap: 7px
                    height: 100%
                    color: var(--bg)
                    text-transform: uppercase
                    font-size: 11px
                    font-weight: 600
                    letter-spacing: 1px
                    user-select: none

                    &:first-of-type
                        border-top-left-radius: 50px
                        border-bottom-left-radius: 50px
                        padding-left: 15px

                    &:last-of-type
                        border-top-right-radius: 50px
                        border-bottom-right-radius: 50px
                        padding-right: 15px

                    &.error
                        color: var(--error)
                        background: var(--error-shine)

                    &.warning
                        color: var(--warning)
                        background: var(--warning-shade)

                    &.info
                        color: var(--primary)
                        background: var(--primary-shine)

                    &.status
                        width: 90px
                        justify-content: center

                    .icon
                        font-family: 'Material Icons Round'
                        font-size: 18px
                        text-transform: none
                        font-weight: normal
                        letter-spacing: normal
                        font-style: normal
                        -webkit-font-feature-settings: 'liga'
                        -webkit-font-smoothing: antialiased

            .timestamp
                line-height: 20px
                font-size: var(--text-size)
                color: var(--text-gray)

            .show-all-button
                width: 160px

        .job-pages
            display: flex
            flex-direction: column
            gap: 15px
            padding: 15px 0
            position: relative

            .status
                display: flex
                height: 50px
                align-items: center
                padding: 10px
                gap: 10px
                border: var(--border)
                border-radius: 5px

                .label
                    font-size: var(--text-size)
                    color: var(--text-gray)
                    width: min-content

                .status-text
                    font-size: var(--text-size)
                    font-weight: 600
                    text-transform: uppercase
                    color: var(--heading-gray)

                    &.warning
                        color: var(--warning)

                    &.error
                        color: var(--error)

                    &.success
                        color: var(--success)

                .spacer
                    flex: 1

                .cancel-button
                    width: min-content

            .blend
                width: 100%
                height: 100px
                padding-top: 40px
                display: grid
                place-content: center
                position: absolute
                bottom: 0
                left: 0
                background: linear-gradient(0deg, #ffffffff 25%, #ffffff00 100%)
                border-radius: 0 0 7px 7px
</style>