<template>
    <div id="wrapper" :class="{'navbar-open': $store.getters.navbar === 'open'}">
        <header>
            
        </header>

        <nav>
            <a id="logo" href="/">
                <img src="/images/app/logo.svg" alt="SEO Bridge logo">
            </a>

            <div class="team-selector-wrapper">
                <ui-select-input label="Current Team" :options="teamOptions" :value="currentTeam" @input="setCurrentTeam($event)"></ui-select-input>
            </div>

            <div class="group">
                <a href="/dashboard/overview" class="button" :class="{'active': $store.getters.page === 'overview'}" @click.prevent="$store.dispatch('setPage', 'overview')">
                    <div class="icon">dashboard</div>
                    <div class="text">Overview</div>
                </a>

                <a href="/dashboard/teams" class="button" :class="{'active': $store.getters.page === 'teams'}" @click.prevent="$store.dispatch('setPage', 'teams')">
                    <div class="icon">supervisor_account</div>
                    <div class="text">My Teams</div>
                </a>

                <a href="/dashboard/reports" class="button" :class="{'active': $store.getters.page === 'reports'}" @click.prevent="$store.dispatch('setPage', 'reports')">
                    <div class="icon">insights</div>
                    <div class="text">Reports</div>
                </a>

                <!-- <a href="/dashboard/keywords" class="button" :class="{'active': $store.getters.page === 'keywords'}" @click.prevent="$store.dispatch('setPage', 'keywords')">
                    <div class="icon">sell</div>
                    <div class="text">Keywords</div>
                </a> -->

                <!-- <a href="/dashboard/social-media" class="button" :class="{'active': $store.getters.page === 'social-media'}" @click.prevent="$store.dispatch('setPage', 'social-media')">
                    <div class="icon">whatshot</div>
                    <div class="text">Social Media</div>
                </a> -->

                <a href="/dashboard/author" class="button" :class="{'active': $store.getters.page === 'author'}" v-if="authorProfile" @click.prevent="$store.dispatch('setPage', 'author')">
                    <div class="icon">video_settings</div>
                    <div class="text">CMS</div>
                </a>
            </div>

            <div class="spacer"></div>

            <div class="group">
                <!-- <a href="/dashboard/notifications" class="button" :class="{'active': $store.getters.page === 'notifications'}" @click.prevent="$store.dispatch('setPage', 'notifications')">
                    <div class="icon">notifications</div>
                    <div class="text">Notifications</div>
                    <div class="notifications">200</div>
                </a> -->
                
                <a href="/dashboard/settings" class="button" :class="{'active': $store.getters.page === 'settings'}" @click.prevent="$store.dispatch('setPage', 'settings')">
                    <div class="icon">settings</div>
                    <div class="text">Settings</div>
                </a>

                <button @click="logout()" class="button">
                    <div class="icon">exit_to_app</div>
                    <div class="text">Logout</div>
                </button>
            </div>
        </nav>

        <main>
            <transition class="transition-group" :name="'opacity-slide-'+$store.getters.pageTransitionDirection" mode="out-in">
                <overview-page class="page" key="overview-page" v-if="$store.getters.page === 'overview'"></overview-page>
                <teams-page class="page" key="teams-page" v-if="$store.getters.page === 'teams'"></teams-page>
                <reports-page class="page" key="reports-page" v-if="$store.getters.page === 'reports'"></reports-page>
                <social-media-page class="page" key="social-media-page" v-if="$store.getters.page === 'social-media'"></social-media-page>
                <author-page class="page" key="author-page" v-if="$store.getters.page === 'author' && authorProfile"></author-page>
                <notifications-page class="page" key="notifications-page" v-if="$store.getters.page === 'notifications'"></notifications-page>
                <settings-page class="page" key="settings-page" v-if="$store.getters.page === 'settings'"></settings-page>
            </transition>
        </main>
    </div>
</template>

<script>
    export default {
        computed: {
            user() {
                return this.$store.getters.user
            },

            teams() {
                return this.$store.getters.teams
            },

            teamOptions() {
                return this.teams.map(e => {
                    let obj = {}
                    obj[e.id] = e.name + (e.description ? ' • '+e.description : '')
                    return obj
                })
            },

            currentTeam() {
                return this.$store.getters.currentTeam
            },

            authorProfile() {
                return this.$store.getters.authorProfile
            },
        },

        methods: {
            setCurrentTeam(e) {
                this.$store.commit('currentTeam', e)
            },

            logout() {
                this.$store.dispatch('logout', () => {
                    window.location = '/'
                })
            },
        },

        components: {
            OverviewPage: require('./pages/Overview.vue').default,
            TeamsPage: require('./pages/Teams.vue').default,
            ReportsPage: require('./pages/Reports.vue').default,
            SocialMediaPage: require('./pages/SocialMedia.vue').default,
            AuthorPage: require('./pages/Author.vue').default,
            NotificationsPage: require('./pages/Notifications.vue').default,
            SettingsPage: require('./pages/Settings.vue').default,
        },
    }
</script>

<style lang="sass">
    #wrapper
        --menu-width: 280px

        main
            .limiter
                max-width: 1240px !important
                padding-left: 30px !important
                padding-right: 30px !important

            .page-container

                .page-header
                    display: block
                    width: 100%
                    color: var(--bg)
                    background: var(--primary)
                    padding-bottom: 70px

                    .page-header-wrapper
                        width: 100%
                        display: block
                        color: inherit
                        padding: 70px 0

                    h1, h2, h3, h4, h5, h6
                        margin: 0
                        color: inherit
                        font-weight: 600

                    .icon-button
                        height: 50px
                        width: 50px
                        color: var(--heading-gray)
                        background: #34e7e4
                        filter: var(--elevation-4)
                        font-size: 24px

                    .row
                        display: flex
                        gap: 10px
                        align-items: center

                        .spacer
                            flex: 1

                .overlap
                    margin-top: -70px
</style>

<style lang="sass" scoped>
    #wrapper
        height: 100%
        width: 100%
        --menu-width: 280px

        header
            display: none
            background: var(--bg)
            filter: drop-shadow(0 1px 2px #00000020)

        nav
            position: fixed
            left: 0
            top: 0
            width: var(--menu-width)
            height: 100%
            z-index: 100
            color: var(--text-gray)
            font-size: var(--text-size)
            background: var(--bg)
            // border-right: 1px solid #e1e1e1
            filter: var(--elevation-4)
            display: flex
            flex-direction: column

            #logo
                height: 85px
                width: 100%
                padding: 20px 0
                text-align: center
                background: var(--bg-dark)

                img
                    height: 100%

            .team-selector-wrapper
                background: var(--bg-dark)
                margin-bottom: 10px
                padding: 15px

            .spacer
                flex: 1
                display: block

            .group
                display: block

            .button
                width: 100%
                text-decoration: none
                height: 50px
                cursor: pointer
                color: var(--text-gray)
                display: flex
                align-content: center
                position: relative
                user-select: none
                border: none
                background: none
                padding: 0
                margin: 0

                .icon
                    font-family: 'Material Icons'
                    font-family: 'Material Icons Round'
                    font-size: 24px
                    width: 60px
                    text-align: center
                    align-self: center
                    z-index: 1
                    position: relative

                .image
                    margin: 0 18px
                    width: 24px
                    height: 24px
                    object-fit: contain
                    border-radius: 100px
                    align-self: center
                    z-index: 1
                    position: relative

                .text
                    font-family: var(--text-font)
                    font-size: var(--button-size)
                    font-weight: 500 !important
                    letter-spacing: 1px
                    color: inherit
                    white-space: nowrap
                    overflow: hidden
                    text-align: left
                    text-overflow: ellipsis
                    text-transform: uppercase
                    align-self: center
                    user-select: none
                    flex: 1
                    z-index: 1
                    position: relative

                    &.bold
                        font-weight: 600

                .notifications
                    height: 16px
                    min-width: 16px
                    line-height: 16px
                    border-radius: 20px
                    background: var(--error)
                    font-size: 11px
                    font-weight: 500
                    padding: 0 6px
                    color: white
                    margin: 0 10px
                    align-self: center
                    pointer-events: none
                    z-index: 1
                    position: relative

                .logout-button
                    align-self: center
                    z-index: 1
                    position: relative
                    margin: 0 5px

                &::before
                    content: ''
                    height: 100%
                    width: 100%
                    position: absolute
                    left: 0
                    top: 0
                    background: var(--primary-shade)
                    transition: clip-path 200ms
                    clip-path: circle(0 at -10% 50%)

                &::after
                    content: ''
                    height: calc(100% - 10px)
                    width: 0px
                    position: absolute
                    left: 0
                    top: 5px
                    background: var(--primary)
                    border-radius: 0 10px 10px 0
                    transition: width 100ms

                &:hover::after,
                &.active::after
                    width: 4px

                &.active::before
                    clip-path: circle(180% at -10% 50%)

                &:hover,
                &.active
                    color: var(--primary)

        &:not(.navbar-open)
            grid-template-columns: 60px auto

            nav
                #logo
                    height: 60px
                    margin: 0 0 10px

                    img
                        width: 100%
                        padding: 5px

                .logo-divider
                    height: 0

                .button
                    .text,
                    .notifications
                        display: none


        main
            height: 100%
            display: block
            margin-left: var(--menu-width)
            
            .transition-group
                width: 100%

            .page
                transition: all 100ms

                &.opacity-slide-down-enter,
                &.opacity-slide-up-leave-to
                    opacity: 0
                    transform: translateY(-70px)

                &.opacity-slide-down-leave-to,
                &.opacity-slide-up-enter
                    opacity: 0
                    transform: translateY(70px)
</style>