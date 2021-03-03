<template>
    <div class="page-container limiter">
        <div class="filter-bar" v-if="details === null">
            <div class="search-bar-wrapper">
                <ui-text-input class="input" label="Search for URL" v-model="reportSearch.url"></ui-text-input>
                <transition name="scale">
                    <ui-icon-button v-show="reportSearch.url.trim()" class="button" @click="reportSearch.url = ''">&#983382;</ui-icon-button>
                </transition>
            </div>
        </div>

        <button class="fab" v-if="details === null" @click="openReportCreateDialog()">&#984085;</button>

        <div class="reports-timeline" v-if="details === null">
            <div class="job-wrapper" v-for="report in searchedReports" :key="report.id">
                <div class="job-header">
                    <div class="title">{{report.host}}</div>
                    <div class="timestamp" v-tooltip="formateDate(report.created_at)">{{report.created_at | diffForHumans}}</div>
                    
                    <ui-popover-menu>
                        <template v-slot:trigger>
                            <ui-icon-button class="more-button">&#983513;</ui-icon-button>
                        </template>

                        <!-- <ui-menu-item icon="&#984214;">Share report</ui-menu-item> -->
                        <!-- <ui-menu-item icon="&#983048;">Assign report</ui-menu-item> -->
                        <ui-menu-item icon="&#983881;" @click="reportSearch.url = report.host">Search for domain</ui-menu-item>
                        <ui-menu-divider></ui-menu-divider>
                        <ui-menu-item icon="&#984089;" @click="$store.dispatch('createReport', report.url)">New report from URL</ui-menu-item>
                        <ui-menu-item icon="&#985721;" @click="openReportDeleteDialog(report)">Delete report</ui-menu-item>
                    </ui-popover-menu>
                </div>

                <div class="job-pages">
                    <!-- <page-card :report="report" @details="openDetails($event)"></page-card> -->
                    <page-row :report="report" @details="openDetails($event)"></page-row>
                </div>
            </div>
        </div>

        <div class="details" v-else>
            <div class="nav-row">
                <ui-button icon="&#983117;" icon-left border text @click="details = null">Back</ui-button>
            </div>

            <div class="detail-row">
                <div class="card span-3">
                    <h4 class="info"><div class="icon">&#984376;</div>Page Score</h4>
                    <apexchart style="display: inherit;" type="radialBar" height="250" :options="chartOptions" :series="[details.score.totalPageScore] || [0]"></apexchart>
                </div>

                <div class="card span-3">
                    <h4 class="info"><div class="icon">&#983341;</div>Checklist</h4>

                    <div class="metric-card-wrapper">
                        <div class="metric-card" v-show="details.score.hasFavicon">
                            <div class="icon" style="color: var(--success)">&#983340;</div>
                            <div class="value">Favicon</div>
                            <ui-tooltip-button class="info">
                                <b>The Favicon</b><br>
                                The favicon is the small icon next to the page title in the tab above the page. It isn't displayed on the page itself. 
                            </ui-tooltip-button>
                        </div>
                        <div class="metric-card" v-show="details.score.hasTitle">
                            <div class="icon" style="color: var(--success)">&#983340;</div>
                            <div class="value">Page Title</div>
                            <ui-tooltip-button class="info">
                                <b>The Page Title</b><br>
                                The title-tag is a meta-tag in the page's head section. It is displayed in search results and in the tab above the page. Former of which
                                makes it very relevant for your page's SEO ranking. 
                            </ui-tooltip-button>
                        </div>
                        <div class="metric-card" v-show="details.score.hasDescription">
                            <div class="icon" style="color: var(--success)">&#983340;</div>
                            <div class="value">Page Description</div>
                            <ui-tooltip-button class="info">
                                <b>The Page Description</b><br>
                                The page description is a meta-tag in the page's head section. Search engines display it below your page title and may use it to 
                                rank your page and extract relevant information of what your page is about.
                            </ui-tooltip-button>
                        </div>
                        <div class="metric-card" v-show="details.score.hasViewport">
                            <div class="icon" style="color: var(--success)">&#983340;</div>
                            <div class="value">Mobile Support</div>
                            <ui-tooltip-button class="info">
                                <b>Mobile Support (WIP)</b><br>
                                A modern web-page is being visited by desktop and mobile users alike. It is therefore strongly recommended to offer a mobile friendly
                                version of your page.<br><br>
                                This detection is still work in progress.
                            </ui-tooltip-button>
                        </div>
                        <div class="metric-card" v-show="details.score.errorPage.hasCustom404Page">
                            <div class="icon" style="color: var(--success)">&#983340;</div>
                            <div class="value">Custom 404 Page</div>
                            <ui-tooltip-button class="info">
                                <b>Custom 404 Pages (WIP)</b><br>
                                When the user visits a non-existing page by default he is shown a standard error page. This my lead the user to believe the whole website
                                is offline. To mitigate this issue it is recommened to build a custom error page to bring the user back to a functional page.<br><br>
                                This detection is still work in progress.
                            </ui-tooltip-button>
                        </div>
                    </div>
                    <div class="metric-card-wrapper">
                        <div class="metric-card" v-show="!details.score.hasFavicon">
                            <div class="icon" style="color: var(--error)">&#983382;</div>
                            <div class="value">No Favicon</div>
                            <ui-tooltip-button class="info">
                                <b>The Favicon</b><br>
                                The favicon is the small icon next to the page title in the tab above the page. It isn't displayed on the page itself. 
                            </ui-tooltip-button>
                        </div>
                        <div class="metric-card" v-show="!details.score.hasTitle">
                            <div class="icon" style="color: var(--error)">&#983382;</div>
                            <div class="value">No Page Title</div>
                            <ui-tooltip-button class="info">
                                <b>The Page Title</b><br>
                                The title-tag is a meta-tag in the page's head section. It is displayed in search results and in the tab above the page. Former of which
                                makes it very relevant for your page's SEO ranking. 
                            </ui-tooltip-button>
                        </div>
                        <div class="metric-card" v-show="!details.score.hasDescription">
                            <div class="icon" style="color: var(--error)">&#983382;</div>
                            <div class="value">No Page Description</div>
                            <ui-tooltip-button class="info">
                                <b>The Page Description</b><br>
                                The page description is a meta-tag in the page's head section. Search engines display it below your page title and may use it to 
                                rank your page and extract relevant information of what your page is about.
                            </ui-tooltip-button>
                        </div>
                        <div class="metric-card" v-show="!details.score.hasViewport">
                            <div class="icon" style="color: var(--error)">&#983382;</div>
                            <div class="value">No Mobile Support</div>
                            <ui-tooltip-button class="info">
                                <b>Mobile Support (WIP)</b><br>
                                A modern web-page is being visited by desktop and mobile users alike. It is therefore strongly recommended to offer a mobile friendly
                                version of your page.<br><br>
                                This detection is still work in progress.
                            </ui-tooltip-button>
                        </div>
                        <div class="metric-card" v-show="!details.score.errorPage.hasCustom404Page">
                            <div class="icon" style="color: var(--error)">&#983382;</div>
                            <div class="value">No Custom 404 Page</div>
                            <ui-tooltip-button class="info">
                                <b>Custom 404 Pages (WIP)</b><br>
                                When the user visits a non-existing page by default he is shown a standard error page. This my lead the user to believe the whole website
                                is offline. To mitigate this issue it is recommened to build a custom error page to bring the user back to a functional page.<br><br>
                                This detection is still work in progress.
                            </ui-tooltip-button>
                        </div>
                    </div>
                </div>

                <div class="card span-6">
                    <h4 class="info"><div class="icon">&#984261;</div>Metrics</h4>

                    <div class="metric-card-wrapper">
                        <div class="metric-card half">
                            <div class="icon">&#983837;</div>
                            <div class="label">HTML Nodes</div>
                            <div class="value">{{details.metrics.Nodes}}</div>
                        </div>

                        <div class="metric-card half">
                            <div class="icon">&#983706;</div>
                            <div class="label">Rendertime</div>
                            <div class="value">{{details.metrics.TaskDuration.toFixed(4)}}s</div>
                        </div>

                        <div class="metric-card half">
                            <div class="icon">&#984430;</div>
                            <div class="label">Layout Rendertime</div>
                            <div class="value">{{details.metrics.LayoutDuration.toFixed(4)}}s</div>
                        </div>

                        <div class="metric-card half">
                            <div class="icon">&#983838;</div>
                            <div class="label">Script Rendertime</div>
                            <div class="value">{{details.metrics.ScriptDuration.toFixed(4)}}s</div>
                        </div>

                        <div class="metric-card half">
                            <div class="icon">&#983559;</div>
                            <div class="label">Outbound-Links</div>
                            <div class="value">{{details.outboundLinks.length}}</div>
                            <ui-tooltip-button class="info">
                                <b>Outbound Links</b><br>
                                Outbound links are hyperlinks going to external websites. Search engines use them to better understand what your page is about.
                                Your page should only link to websites related to your content.
                            </ui-tooltip-button>
                        </div>

                        <div class="metric-card half">
                            <div class="icon">&#983865;</div>
                            <div class="label">Internal-Links</div>
                            <div class="value">{{details.internalLinks.length}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-row">
                <div class="card span-12 page-info-card">
                    <h4>
                        <object class="favicon" v-if="details.metaData.favicon" :data="details.metaData.favicon" type="image/png">
                            <img src="/images/defaults/default_icon.svg" alt="Default Icon Fallback" width="100%" height="100%">
                        </object>
                        <img class="favicon" v-else src="/images/defaults/default_icon.svg" alt="Default Icon Fallback" width="100%" height="100%">
                        {{details.metaData.title}}
                    </h4>

                    <div class="metric-card-wrapper">
                        <div class="metric-card" v-if="details.metaData.description">
                            <div class="icon">&#985975;</div>
                            <div class="label">Description</div>
                            <div class="value">{{details.metaData.description}}</div>
                        </div>

                        <div class="metric-card" v-if="details.metaData.generator">
                            <div class="icon">&#988535;</div>
                            <div class="label">CMS / Generator</div>
                            <div class="value">
                                <span v-show="details.metaData.cms">{{details.metaData.cms}} —</span>
                                {{details.metaData.generator}}
                            </div>
                        </div>

                        <div class="metric-card" v-if="details.metaData.themeColor">
                            <div class="square" :style="'background:' + details.metaData.themeColor"></div>
                            <div class="label">Theme Color</div>
                            <div class="value">{{details.metaData.themeColor}}</div>
                        </div>

                        <div class="metric-card" v-if="details.metaData.keywords.length > 0">
                            <div class="icon">&#983819;</div>
                            <div class="label">Keywords</div>
                            <div class="value">{{details.metaData.keywords.join(', ') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-row" v-if="details.score.errorData.errors.length > 0">
                <div class="card span-12">
                    <h4 class="error"><div class="icon">&#983385;</div>Errors</h4>

                    <div class="metric-card-wrapper">
                        <div class="metric-card" v-for="(error, i) in details.score.errorData.errors" :key="i">
                            <div class="icon" style="color: var(--error)">&#983789;</div>
                            <div class="label">Error</div>
                            <div class="value">{{error.desc}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-row" v-if="details.score.warningData.warnings.length > 0">
                <div class="card span-12">
                    <h4 class="warning"><div class="icon">&#983078;</div>Warnings</h4>

                    <div class="metric-card-wrapper">
                        <div class="metric-card" v-for="(warning, i) in details.score.warningData.warnings" :key="i">
                            <div class="icon" style="color: var(--warning)">&#983659;</div>
                            <div class="label">Warning</div>
                            <div class="value">{{warning.desc}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-row" v-if="details.metaData.twitterCard.hasTwitterCard || details.metaData.openGraph.hasOpenGraph">
                <div class="card span-6" v-if="details.metaData.twitterCard.hasTwitterCard">
                    <h4><div class="icon">&#984388;</div>Twitter Appearence</h4>
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
                    <h4><div class="icon">&#987137;</div>Open Graph Appearence</h4>

                    <div class="open-graph-article-card">
                        <img :src="details.metaData.openGraph['og:image']" class="image">
                        <div class="content">
                            <div class="url">{{details.metaData.openGraph['og:url'].host}}</div>
                            <div class="title">{{details.metaData.openGraph['og:title']}}</div>
                            <div class="description">{{details.metaData.openGraph['og:description']}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-row">
                <div class="card span-12">
                    <h4><div class="icon">&#984313;</div>Meta Tags</h4>

                    <div class="metric-card-wrapper">
                        <div class="metric-card" v-for="(meta, i) in details.meta" :key="i">
                            <div class="icon" style="color: var(--text-gray)">&#984313;</div>
                            <div class="label">{{meta.name || meta.property || 'Charset'}}</div>
                            <div class="value">{{meta.content || meta.charset || meta.httpEquiv}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-row">
                <div class="card span-12">
                    <h4><div class="icon">&#983865;</div>Links</h4>

                    <div class="metric-card-wrapper">
                        <div class="metric-card" v-for="(link, i) in details.links" :key="i">
                            <div class="icon" v-if="link.href && link.href.startsWith('mailto:')" style="color: var(--text-gray)">&#983536;</div>
                            <div class="icon" v-else-if="link.href && link.href.startsWith('tel:')" style="color: var(--text-gray)">&#984050;</div>
                            <div class="icon" v-else style="color: var(--text-gray)">&#983865;</div>
                            <div class="label" v-if="link.text.trim()">{{link.text}}</div>
                            <div class="label" v-else><i>No link text</i></div>
                            <div class="value">{{link.href}}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="detail-row">
                <div class="card span-12">
                    <h4><div class="icon">&#983785;</div>Images</h4>

                    <div class="image-card" v-for="(image, i) in details.images" :key="i">
                        <img class="image" :src="image.href" v-tooltip.bottom-start="image.src">
                        <div class="content">
                            <b class="title" v-if="image.alt">{{image.alt}}</b>
                            <i class="title" v-else>No "alt"-attribute</i>
                            <div class="description">
                                Natural-Size: <b>{{image.width}} x {{image.height}}</b><br>
                                Visible-Size: <b>{{image.visibleWidth}} x {{image.visibleHeight}}</b><br>
                                <span class="no-text-overflow">Src: <b>{{image.src}}</b></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <ui-option-dialog ref="reportCreateDialog" @close="resetReportCreate()">
            <template v-slot:heading>
                Create a new website report
            </template>

            <template v-slot:inputs>
                <ui-select-input label="Mode" v-model="reportCreate.mode" :options="[{'full':'Full Scan'}, {'single':'Single Page'}]"></ui-select-input>
                <ui-select-input label="Viewport" v-model="reportCreate.viewport" :options="[{'1080p':'1920 x 1080'}, {'720p':'1280 x 720'}]"></ui-select-input>
                <ui-text-input label="URL" v-model="reportCreate.url"></ui-text-input>
            </template>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetReportCreate()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button icon="&#983881;" :loading="reportLoading" @click="requestReport()">Analyse</ui-button>
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
    </div>
</template>

<script>
    const dayjs = require('dayjs')
    const relativeTime = require('dayjs/plugin/relativeTime')
    // require('dayjs/locale/de')

    export default {
        data() {
            return {
                details: null,
                series: [67],
                chartOptions: {
                    chart: {
                        height: 250,
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
                                    fontSize: '25px',
                                    fontFamily: 'Roboto',
                                    fontWeight: '900',
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
                        if      (value >= 80) return 'var(--success)'
                        else if (value >= 50) return 'var(--warning)'
                        else if (value >= 20) return '#ff7f50'
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



                reportSearch: {
                    url: '',
                },

                reportCreate: {
                    url: '',
                    mode: null,
                    viewport: null,
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

        computed: {
            reports() {
                return this.$store.getters.reports
            },

            reportLoading() {
                return this.$store.getters.reportScanning
            },

            searchedReports() {
                return this.reports.filter(e => e.host.trim().toUpperCase().includes(this.reportSearch.url.trim().toUpperCase()))
            }
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



            openDetails(details) {
                console.log(details)
                this.details = details
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

            requestReport() {
                axios.post('/auth/reports/request-site-analysis', {
                    url: this.reportCreate.url,
                    mode: this.reportCreate.mode,
                    device: {
                        viewport: this.reportCreate.viewport
                    },
                })
                .then(response => {
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
                    this.$store.commit('deleteReport', response.data)
                    this.reportDelete.loading = false
                    this.resetReportDelete()
                })
                .catch(error => {
                    this.reportDelete.loading = false
                    console.log(error.response)
                })
            },
        },

        components: {
            PageCard: require('../components/PageCard.vue').default,
            PageRow: require('../components/PageRow.vue').default,
        },
    }
</script>

<style lang="sass" scoped>
    .page-container
        width: 100%

        .filter-bar
            display: flex
            padding: 15px
            background: var(--bg)
            border-radius: 0 0 15px 15px
            filter: var(--elevation-2)
            gap: 15px
            margin-bottom: 15px
            align-items: center

            .search-bar-wrapper
                display: block
                position: relative
                flex: 1

                .input
                    padding-right: 50px

                .button
                    position: absolute
                    top: 5px
                    right: 5px
                    z-index: 1
                    transition: all 100ms

                    &.scale-enter,
                    &.scale-leave-to
                        transform: scale(0)

            .submit-button
                white-space: nowrap

        .fab
            height: 56px
            width: 56px
            font-family: 'Material Icons'
            color: white
            background: var(--primary)
            display: grid
            place-content: center
            font-size: 24px
            position: fixed
            bottom: 30px
            right: 30px
            border-radius: 100%
            border: none
            filter: var(--elevation-2)
            cursor: pointer
            user-select: none
            transition: all 200ms
            z-index: 100

            &:hover
                filter: var(--elevation-4)

        .reports-timeline
            display: block

            .job-wrapper
                width: 100%
                display: inline-flex
                flex-direction: column
                background: white
                border-radius: 7px
                filter: var(--elevation-2)
                margin: 15px 0

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
                    gap: 15px
                    padding: 0 15px 15px

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
                overflow: hidden

                h1, h2, h3, h4, h5, h6
                    margin: 0
                    color: var(--primary)
                    width: 100%
                    padding: 7px 15px 0

                h4
                    color: var(--heading-gray)
                    font-size: var(--button-size)
                    letter-spacing: 1px
                    text-transform: uppercase
                    position: relative
                    font-family: var(--text-font)
                    padding: 7px 15px 7px 10px
                    border-bottom: 2px solid var(--border-color)

                    &.error
                        color: var(--error)
                        border-color: var(--error)

                    &.warning
                        color: var(--warning)
                        border-color: var(--warning)

                    &.info
                        color: var(--primary)
                        border-color: var(--primary)

                    .icon
                        font-size: 20px
                        font-weight: 300
                        margin-right: 10px
                        color: inherit
                        font-family: 'Material Icons'
                        vertical-align: middle
                        user-select: none

                    .favicon
                        height: 20px
                        width: 20px
                        margin-right: 10px
                        vertical-align: middle

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

                    .preview
                        width: 100%
                        height: 100%
                        object-fit: contain
                        filter: saturate(0%)
                        float: left
                        border-radius: 5px
                        width: 200px

                    .content
                        font-size: var(--text-size)
                        color: var(--text-gray)

                p
                    padding: 0 15px

                .metric-card-wrapper
                    width: 100%
                    padding: 7.5px

                .metric-card
                    background: linear-gradient(90deg, var(--bg-dark) 0%, var(--bg) 70%)
                    border-radius: 5px
                    display: inline-grid
                    width: calc(100% - 15px)
                    margin: 7.5px
                    padding: 5px 0
                    grid-template: auto 1fr / 50px 1fr auto
                    grid-template-areas: "icon label info" "icon value info"

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

                    .square
                        grid-area: icon
                        height: 20px
                        width: 20px
                        border-radius: 3px
                        border: var(--border)
                        background: var(--bg)
                        align-self: center
                        justify-self: center

                    .label
                        grid-area: label
                        font-size: var(--text-size)
                        color: var(--text-gray)
                        align-self: center
                        height: 18px
                        line-height: 18px
                        margin-top: 3px

                    .value
                        grid-area: value
                        font-size: var(--text-size)
                        color: var(--heading-gray)
                        line-height: 130%
                        align-self: center
                        font-weight: 600

                        &.no-overflow
                            width: 100%
                            white-space: nowrap
                            overflow: hidden
                            text-overflow: ellipsis

                    .info
                        grid-area: info
                        align-self: center
                        justify-self: center
                        user-select: none

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
                        color: var(--text-gray)
                        font-family: 'Material Icons'
                        text-align: center

                    .text
                        font-size: var(--button-size)
                        font-weight: 600
                        color: var(--heading-gray)
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

                .open-graph-article-card
                    display: grid
                    margin: 15px
                    border-radius: 7px
                    grid-template: auto 1fr / 1fr
                    grid-template-areas: "image" "content"
                    align-items: center
                    border: var(--border)
                    overflow: hidden

                    .image
                        grid-area: image
                        width: 100%
                        height: 100%
                        object-fit: cover
                        border-bottom: var(--border)

                    .content
                        grid-area: content
                        padding: 8px 10px
                        display: block

                        .url
                            display: block
                            align-items: center
                            font-size: var(--text-size)
                            color: var(--text-gray)
                            text-transform: uppercase
                            margin-bottom: 3px

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
                    
                .image-card
                    display: grid
                    margin: 15px
                    border-radius: 5px
                    grid-template: 125px / 125px auto
                    grid-template-areas: "image content"
                    align-items: center
                    border: var(--border)
                    overflow: hidden

                    .image
                        grid-area: image
                        width: 100%
                        height: 100%
                        object-fit: contain
                        border-right: var(--border)
                        background: var(--bg-dark)
                        padding: 5px

                    .content
                        grid-area: content
                        padding: 8px 10px
                        display: block
                        max-width: 100%
                        overflow: hidden

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
                            max-width: 100%
                            overflow: hidden

                            .no-text-overflow
                                width: 100%
                                white-space: nowrap
                                overflow: hidden
                                text-overflow: ellipsis
</style>