<template>
    <div class="page-container">
        <div class="header-wrapper">
            <div class="limiter">
                <div class="filter-bar" v-if="details === null && activeTeam && activeTeam.sites.length > 0">
                    <ui-select-input class="sort-input" v-model="reportSearch.sort" :options="[{'DESC':'Newest first'}, {'ASC':'Oldest first'}]"></ui-select-input>

                    <div class="spacer"></div>

                    <div class="search-bar-wrapper">
                        <ui-text-input class="input" placeholder="Search" v-model="reportSearch.url"></ui-text-input>
                        <transition name="scale">
                            <ui-icon-button v-show="reportSearch.url.trim()" class="button" @click="reportSearch.url = ''">&#983382;</ui-icon-button>
                        </transition>
                    </div>
                </div>
            </div>
        </div>

        <div class="limiter" v-if="activeTeam && activeTeam.sites.length > 0">
            <div class="reports-timeline" v-if="details === null">
                <transition-group name="slide" class="block">
                    <div class="job-wrapper" :class="{'has-indicator': !reportGroup.is_own}" v-for="reportGroup in paginatedReportGroups.data" :key="'report_group_'+reportGroup.id">
                        
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

                                <ui-menu-item icon="&#984214;" v-if="reportGroup.is_own" @click="openReportShareDialog('group', reportGroup, false)">Share report</ui-menu-item>
                                <ui-menu-item icon="&#983048;" v-if="reportGroup.is_own" @click="openReportShareDialog('group', reportGroup, true)">Assign report</ui-menu-item>
                                <ui-menu-item icon="&#983881;" @click="reportSearch.url = reportGroup.host">Search for domain</ui-menu-item>
                                <ui-menu-divider></ui-menu-divider>
                                <ui-menu-item icon="&#984089;" @click="requestReport(reportGroup.url)">New report from URL</ui-menu-item>
                                <ui-menu-divider v-if="reportGroup.is_own"></ui-menu-divider>
                                <ui-menu-item icon="&#985721;" v-if="reportGroup.is_own" @click="openReportDeleteDialog(reportGroup)">Delete report</ui-menu-item>
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

                            <page-row v-for="report in reportGroup.reports" :key="'report_'+report.id" :report="report" @details="openDetails($event)"></page-row>
                            <div class="blend" v-if="reportGroup.reports.length > 4">
                                <ui-button text>Show more</ui-button>
                            </div>
                        </div>
                    </div>
                </transition-group>
            </div>

            <div class="details" v-else>
                <div class="nav-row">
                    <ui-button icon="&#983117;" icon-left border text @click="details = null">Back</ui-button>
                </div>

                <div class="detail-row">
                    <div class="card span-4">
                        <h4 class="info"><div class="icon">&#984376;</div>Page Score</h4>
                        <apexchart style="display: inherit;" type="radialBar" height="250" :options="chartOptions" :series="[details.score.totalPageScore] || [0]"></apexchart>
                    </div>

                    <div class="card span-4">
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

                    <div class="card span-4">
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
                                <div class="url" v-if="details.metaData.openGraph['og:url']">{{details.metaData.openGraph['og:url'].host}}</div>
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
                                <div class="label" v-if="link.text && link.text.trim()">{{link.text}}</div>
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

        <button class="fab" v-if="details === null && activeTeam && activeTeam.sites.length > 0" @click="openReportCreateDialog()">&#984085;</button>



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



        <ui-option-dialog ref="reportCreateDialog" @close="resetReportCreate()">
            <template v-slot:heading>
                Create a new website report
            </template>

            <template v-slot:inputs>
                <!-- <ui-select-input label="Mode" v-model="reportCreate.mode" :options="[{'full':'Full Scan'}, {'single':'Single Page'}]"></ui-select-input> -->
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

            requestReport(url = this.reportCreate.url) {
                axios.post('/auth/reports/request-site-analysis', {
                    url,
                    // mode: this.reportCreate.mode || 'single',
                    mode: 'single',
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
            PageCard: require('../components/PageCard.vue').default,
            PageRow: require('../components/PageRow.vue').default,
        },
    }
</script>

<style lang="sass" scoped>
    .page-container
        width: 100%
        height: 100%

        .placeholder
            height: 100%
            width: 100%

        .header-wrapper
            width: calc(100% - var(--menu-width))
            position: fixed
            top: 0
            left: var(--menu-width)
            z-index: 1

            .filter-bar
                filter: var(--elevation-3)
                background: var(--bg)
                width: 100%
                display: flex !important
                padding: 15px
                align-items: center
                border-radius: 0 0 7px 7px

                .spacer
                    flex: 1
                    height: 40px

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
            padding-top: 85px

            .job-wrapper
                width: 100%
                display: inline-flex
                flex-direction: column
                background: white
                border-radius: 7px
                filter: var(--elevation-2)
                margin: 15px 0
                transition: all 300ms
                position: relative

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
                    border-radius: 7px 0 0 7px
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

        .details
            width: 100%
            display: flex
            flex-direction: column
            gap: 15px

            .nav-row
                height: 40px
                margin-top: 15px

            .detail-row
                display: grid
                grid-template-columns: repeat(12, 1fr)
                grid-template-rows: 1fr
                gap: 15px

            .card
                background: var(--bg)
                border-radius: 5px
                font-size: var(--text-size)
                overflow: hidden
                filter: var(--elevation-2)

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
                    padding: 15px 15px 10px

                    &.error
                        color: var(--error)

                    &.warning
                        color: var(--warning)

                    &.info
                        color: var(--primary)

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
                    padding: 7.5px 0
                    padding-top: 0

                .metric-card
                    // background: linear-gradient(90deg, var(--bg-dark) 0%, var(--bg) 70%)
                    border-radius: 5px
                    display: inline-grid
                    width: 100%
                    padding: 8px 10px 8px 0
                    grid-template: auto 1fr / 50px 1fr auto
                    grid-template-areas: "icon label info" "icon value info"

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