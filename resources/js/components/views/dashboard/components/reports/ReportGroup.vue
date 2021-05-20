<template>
    <div class="job-wrapper" :class="{'has-indicator': !reportGroup.is_own}">                   
        <div class="indicator shared" v-show="reportGroup.has_been_shared && !reportGroup.has_been_assigned">
            <div class="text">Shared</div>
        </div>
        <div class="indicator assigned" v-show="reportGroup.has_been_assigned">
            <div class="text">Assigned</div>
        </div>

        <div class="job-header">
            <div class="title">{{reportGroup.host}}</div>
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

            <report-row v-for="report in reportGroup.reports" :key="'report_'+report.id" :report="report" @details="$emit('openDetails', {group: reportGroup.id, report: $event})"></report-row>
            
            <div class="blend" v-if="reportGroup.reports.length > limit">
                <ui-button text @click="$emit('openGroup', reportGroup.id)">Show All Reports</ui-button>
            </div>
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
        padding-top: 10px
        border-bottom: var(--border)

        &:last-of-type
            border-bottom: none

        &.has-indicator
            padding-left: 18px

        &.slide-enter
            transform: translateY(-100px)
            opacity: 0

        &.slide-leave-to
            transform: scale(0)
            opacity: 0

        &.slide-leave-active
            position: absolute

        .indicator
            position: absolute
            top: 0
            left: 0
            width: 18px
            height: 100%
            background: var(--text-gray-shade)
            display: grid
            place-content: center
            color: var(--text-gray)

            &.assigned
                color: white
                background: var(--warning)

            .text
                color: inherit
                text-transform: uppercase
                font-size: 13px
                font-weight: 600
                letter-spacing: 1px
                transform: rotate(-90deg)
                padding-top: 1.5px

        .job-header
            display: flex
            align-items: center
            padding: 5px 0
            border-radius: 7px 7px 0 0

            .title
                flex: 1
                font-size: 16px
                line-height: 20px
                font-weight: 600
                text-transform: uppercase
                color: var(--heading-gray)
                padding: 0 15px

            .timestamp
                line-height: 20px
                font-size: var(--text-size)
                color: var(--text-gray)

            .more-button
                margin: 0 5px

        .job-pages
            display: flex
            flex-direction: column
            gap: 15px
            padding: 0 15px 15px
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