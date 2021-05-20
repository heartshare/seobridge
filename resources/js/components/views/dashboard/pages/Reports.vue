<template>
    <div class="page-container">
        <div class="page-header">
            <div class="limiter">
                <div class="page-header-wrapper">
                    <div class="row">
                        <h1>Reports</h1>
                        <div class="spacer"></div>
                        <ui-icon-button class="icon-button" v-if="activeTeam && activeTeam.sites.length > 0" @click="openReportCreateDialog()">&#984085;</ui-icon-button>
                    </div>
                </div>
            </div>
        </div>

        <div class="limiter overlap">
            <div class="sheet">
                <div class="block" v-if="activeTeam && activeTeam.sites.length > 0">
                    <div class="filter-bar">
                        <ui-button text @click="openOverview()">Overview</ui-button>
                        <ui-button v-if="reportGroupId" text @click="openGroup(reportGroupId)">Report Group</ui-button>
                        <ui-button v-if="reportId" text @click="openDetails(reportGroupId, reportId)">Report</ui-button>
                        <ui-button v-if="viewCode" text icon="&#984620;">Code</ui-button>
                        <ui-select-input class="sort-input" v-model="reportSearch.sort" :options="[{'DESC':'Newest first'}, {'ASC':'Oldest first'}]"></ui-select-input>

                        <div class="spacer"></div>

                        <div class="search-bar-wrapper">
                            <ui-text-input class="input" placeholder="Search" v-model="reportSearch.url"></ui-text-input>
                            <transition name="scale">
                                <ui-icon-button v-show="reportSearch.url.trim()" class="button" @click="reportSearch.url = ''">&#983382;</ui-icon-button>
                            </transition>
                        </div>
                    </div>

                    <div class="reports-timeline" v-if="!activeReportGroup && !activeReport">
                        <transition-group name="slide" class="block">
                            <report-group
                                v-for="reportGroup in paginatedReportGroups.data"
                                :key="'report_group_'+reportGroup.id"
                                :report-group="reportGroup"
                                :limit="4"
                                @share="openReportShareDialog('group', $event, false)"
                                @assign="openReportShareDialog('group', $event, true)"
                                @searchForDomain="reportSearch.url = $event"
                                @newFromDomain="requestReport($event)"
                                @delete="openReportDeleteDialog($event)"
                                @openGroup="openGroup($event)"
                                @openDetails="openDetails($event.group, $event.report)"
                            ></report-group>
                        </transition-group>

                        <div class="placeholder grid-centered" v-if="paginatedReportGroups.data && paginatedReportGroups.data.length === 0">
                            <p>You don't have any reports yet!</p>
                            <ui-button @click="openReportCreateDialog()">Create a Report</ui-button>
                        </div>
                    </div>

                    <report-group
                        v-if="activeReportGroup  && !activeReport"
                        :report-group="activeReportGroup"
                        :limit="10000"
                        @share="openReportShareDialog('group', $event, false)"
                        @assign="openReportShareDialog('group', $event, true)"
                        @searchForDomain="reportSearch.url = $event"
                        @newFromDomain="requestReport($event)"
                        @delete="openReportDeleteDialog($event)"
                        @openGroup="openGroup($event)"
                        @openDetails="openDetails($event.group, $event.report)"
                    ></report-group>

                    <report-details v-if="activeReport" :details="activeReport.data"></report-details>
                </div>



                <div class="placeholder grid-centered" v-if="teams.length === 0">
                    <p>To analyse a website you<br>need to create a team first</p>
                    <ui-button @click="$store.dispatch('setPage', 'teams')">Create a team</ui-button>
                </div>

                <div class="placeholder grid-centered" v-if="!activeTeam && teams.length > 0">
                    <p>To analyse a website you<br>need to join a team first</p>
                    <ui-button @click="$store.dispatch('setPage', 'teams')">Join now</ui-button>
                </div>

                <div class="placeholder grid-centered" v-if="activeTeam && activeTeam.sites.length === 0">
                    <p>To analyse a website your team has to <br> have at least one Namespace added</p>
                    <ui-button v-if="activeTeam.is_owner" @click="$store.dispatch('setPage', 'teams')">Add Site Namespace now</ui-button>
                </div>
            </div>
        </div>


        <div class="footer-wrapper" v-show="paginatedReportGroups.prev_page_url || paginatedReportGroups.next_page_url">
            <div class="limiter">
                <!-- <ui-select-input class="entity-input" v-model="reportSearch.pageCount" :options="[{'10':10}, {'20':20}, {'50':50}, {'100':100}]"></ui-select-input> -->
                
                <div class="spacer"></div>

                <ui-icon-button class="pagination-button" @click="prevPage()" :disabled="paginatedReportGroups.prev_page_url === null">&#983361;</ui-icon-button>
                <div class="page-number">{{paginatedReportGroups.current_page}}</div>
                <ui-icon-button class="pagination-button" @click="nextPage()" :disabled="paginatedReportGroups.next_page_url === null">&#983362;</ui-icon-button>
            </div>
        </div>



        <ui-option-dialog ref="reportCreateDialog" @close="resetReportCreate()">
            <template v-slot:heading>
                Create a new website report
            </template>

            <template v-slot:inputs>
                <ui-select-input label="Mode" v-model="reportCreate.mode" :options="[{'single':'Single Page Scan (fast)'}, {'full':'Full Website Scan (slow)'}]"></ui-select-input>
                <!-- <ui-select-input label="Viewport" v-model="reportCreate.viewport" :options="[{'1080p':'1920 x 1080'}, {'720p':'1280 x 720'}]"></ui-select-input> -->
                <ui-text-input label="URL" v-model="reportCreate.url"></ui-text-input>
            </template>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetReportCreate()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button icon="&#983881;" :loading="reportCreate.loading" @click="requestReport()">Analyse</ui-button>
            </template>
        </ui-option-dialog>



        <ui-option-dialog ref="reportDeleteDialog" @close="resetReportDelete()">
            <template v-slot:heading>
                Delete report?
            </template>

            <span>
                Are you sure you want to delete the report about:<br>
                <b>{{reportDelete.name}}</b> created <b>{{reportDelete.date | diffForHumans}}</b>?
            </span>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetReportDelete()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button error :loading="reportDelete.loading" @click="deleteReport()">Yes, Delete now</ui-button>
            </template>
        </ui-option-dialog>



        <ui-option-dialog ref="reportShareDialog" @close="resetReportShare()">
            <template v-slot:heading>
                <span v-if="reportShare.shouldAssign">Assign Report: <b>{{reportShare.name}}</b></span>
                <span v-else>Share Report: <b>{{reportShare.name}}</b></span>
            </template>

            <template v-slot:inputs>
                <ui-select-input label="Team" v-model="reportShare.teamId" :options="reportShareTeamOptions"></ui-select-input>

                <ui-select-input label="Share with" v-show="reportShare.teamId && !reportShare.shouldAssign" v-model="reportShare.userId" :options="reportShareMemberOptions"></ui-select-input>
                <ui-select-input label="Assign to" v-show="reportShare.teamId && reportShare.shouldAssign" v-model="reportShare.assignedToUserId" :options="reportShareMemberOptions.slice(1)"></ui-select-input>
            </template>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetReportShare()">Cancel</ui-button>
            </template>

            <template v-slot:button-2>
                <ui-button v-if="reportShare.shouldAssign" icon="&#983048;" :loading="reportShare.loading" @click="shareReport()">Assign</ui-button>
                <ui-button v-else icon="&#984214;" :loading="reportShare.loading" @click="shareReport()">Share</ui-button>
            </template>
        </ui-option-dialog>
    </div>
</template>

<script>
    const dayjs = require('dayjs')
    const relativeTime = require('dayjs/plugin/relativeTime')
    // require('dayjs/locale/de')

    export default {
        data() {
            return {
                reportId: null,
                reportGroupId: null,
                viewCode: false,
                details: null,



                reportSearch: {
                    url: '',
                    pageCount: '20',
                    sort: 'DESC',
                },

                reportCreate: {
                    url: '',
                    // url: 'https://freuwort.com',
                    mode: null,
                    viewport: null,
                    loading: false,
                },

                reportShare: {
                    id: null,
                    type: 'group',
                    teamId: null,
                    userId: null,
                    shouldAssign: false,
                    assignedToUserId: null,
                    name: '',
                    loading: false,
                },

                reportDelete: {
                    id: null,
                    name: '',
                    loading: false,
                },
            }
        },

        created() {
            dayjs.extend(relativeTime)
        },

        watch: {
            'reportSearch.url': debounce(function() {
                this.refetchPaginatedReportGroups()
            }, 400),
            'reportSearch.pageCount': debounce(function() {
                this.refetchPaginatedReportGroups()
            }, 200),
            'reportSearch.sort'() {
                this.refetchPaginatedReportGroups()
            },
        },

        computed: {
            paginatedReportGroups() {
                return this.$store.getters.paginatedReportGroups
            },

            teams() {
                return this.$store.getters.teams
            },

            activeTeam() {
                let activeTeam = this.teams.find(e => e.id === this.user.active_team_id)
                return activeTeam || false
            },

            user() {
                return this.$store.getters.user
            },

            reportShareTeamOptions() {
                return this.teams.map(e => ({'value': e.id, 'label': e.name}))
            },

            reportShareMemberOptions() {
                let selectedTeam = this.teams.find(e => e.id === this.reportShare.teamId)
                let users = []

                if (selectedTeam)
                {
                    users = selectedTeam.members.filter(e => e.user_id !== this.user.id).map(e => ({'value': e.user.id, 'label': e.user.username}))
                }

                return [{'value': null, label: 'All'}, ...users]
            },

            activeReportGroup() {
                return this.paginatedReportGroups.data ? this.paginatedReportGroups.data.find(e => e.id === this.reportGroupId) : null
            },

            activeReport() {
                return this.activeReportGroup ? this.activeReportGroup.reports.find(e => e.id === this.reportId) : null
            },
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



            toggleSort() {
                this.reportSearch.sort = (this.reportSearch.sort === 'ASC') ? 'DESC' : 'ASC'
            },

            prevPage() {
                this.refetchPaginatedReportGroups(-1)
            },

            nextPage() {
                this.refetchPaginatedReportGroups(1)
            },

            refetchPaginatedReportGroups(page = 0)
            {
                this.$store.dispatch('fetchPaginatedReportGroups', {
                    page: this.paginatedReportGroups.current_page + page,
                    order: this.reportSearch.sort,
                    size: this.reportSearch.pageCount,
                    searchKey: this.reportSearch.url,
                })
            },



            openOverview() {
                this.reportGroupId = null
                this.reportId = null
            },
            
            openGroup(reportGroupId) {
                this.reportGroupId = reportGroupId
                this.reportId = null
            },

            openDetails(reportGroupId, reportId) {
                this.reportGroupId = reportGroupId
                this.reportId = reportId
            },



            openReportCreateDialog() {
                this.$refs.reportCreateDialog.open()
            },

            resetReportCreate() {
                this.reportCreate.url = ''
                this.reportCreate.mode = null
                this.reportCreate.viewport = null
                this.$refs.reportCreateDialog.close()
            },

            requestReport(url = this.reportCreate.url) {
                axios.post('/auth/reports/request-site-analysis', {
                    url,
                    mode: this.reportCreate.mode || 'single',
                    device: {
                        viewport: this.reportCreate.viewport || '1080p'
                    },
                    teamId: this.user.active_team_id,
                })
                .then(response => {
                    this.$store.commit('addReportGroup', response.data)
                    this.resetReportCreate()
                    console.log(response.data)
                })
                .catch(error => {
                    console.log(error.response)
                })
            },



            openReportDeleteDialog(report) {
                this.reportDelete.id = report.id
                this.reportDelete.name = report.host
                this.reportDelete.date = report.created_at
                this.$refs.reportDeleteDialog.open()
            },

            resetReportDelete() {
                this.reportDelete.id = null
                this.reportDelete.name = ''
                this.reportDelete.date = ''
                this.$refs.reportDeleteDialog.close()
            },

            deleteReport() {
                this.reportDelete.loading = true

                axios.post('/auth/reports/delete-report', {
                    id: this.reportDelete.id
                })
                .then(response => {
                    this.$store.commit('deleteReportGroup', response.data)
                    this.reportDelete.loading = false
                    this.resetReportDelete()
                })
                .catch(error => {
                    this.reportDelete.loading = false
                    console.log(error.response)
                })
            },



            openReportShareDialog(type, report, shouldAssign = false) {
                this.reportShare.id = report.id
                this.reportShare.type = type
                this.reportShare.name = report.host
                this.reportShare.shouldAssign = shouldAssign
                this.$refs.reportShareDialog.open()
            },

            resetReportShare() {
                this.reportShare.id = null
                this.reportShare.typo = 'group'
                this.reportShare.teamId = null
                this.reportShare.userId = null
                this.reportShare.shouldAssign = false
                this.reportShare.assignedToUserId = null
                this.reportShare.name = ''
                this.$refs.reportShareDialog.close()
            },

            shareReport() {
                this.reportShare.loading = true

                axios.post('/auth/reports/share-report', {
                    id: this.reportShare.id,
                    type: this.reportShare.type,
                    teamId: this.reportShare.teamId,
                    userId: this.reportShare.userId,
                    assignedToUserId: this.reportShare.assignedToUserId,
                })
                .then(response => {
                    this.reportShare.loading = false
                    this.resetReportShare()
                })
                .catch(error => {
                    this.reportShare.loading = false
                    console.log(error.response)
                })
            },
        },

        components: {
            ReportGroup: require('../components/reports/ReportGroup.vue').default,
            ReportDetails: require('../components/reports/ReportDetails.vue').default,
        },
    }
</script>

<style lang="sass" scoped>
    .page-container
        width: 100%
        height: 100%

        .placeholder
            height: 200px
            width: 100%

        .filter-bar
            width: 100%
            display: flex !important
            gap: 15px
            padding: 15px
            align-items: center
            border-bottom: var(--border)

            .spacer
                flex: 1

            .sort-input
                width: 150px
                --height: 40px

            .search-bar-wrapper
                display: block
                position: relative
                flex: 1
                max-width: 300px

                .input
                    padding-right: 40px
                    --height: 40px

                .button
                    position: absolute
                    top: 0
                    right: 0
                    z-index: 1
                    transition: all 100ms

                    &.scale-enter,
                    &.scale-leave-to
                        transform: scale(0)


        .footer-wrapper
            width: calc(100%)

            .limiter
                width: 100%
                display: flex !important
                padding: 15px 0
                align-items: center

                .entity-input
                    width: 80px
                    --height: 40px

                .spacer
                    flex: 1
                    height: 40px

                .pagination-button
                    width: 40px

                .page-number
                    width: 40px
                    line-height: 20px
                    height: 20px
                    font-size: var(--text-size)
                    color: var(--text-gray)
                    user-select: none
                    text-align: center

        .reports-timeline
            display: block
</style>