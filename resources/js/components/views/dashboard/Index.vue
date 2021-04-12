<template>
    <div id="wrapper" :class="{'navbar-open': $store.getters.navbar === 'open'}">
        <header>
            
        </header>

        <nav>
            <a id="logo" href="/">
                <img src="/images/app/logo.svg" alt="SEO Bridge logo">
            </a>

            <div class="logo-divider"></div>

            <a href="/dashboard/overview" class="button" :class="{'active': $store.getters.page === 'overview'}" @click.prevent="$store.dispatch('setPage', 'overview')">
                <div class="icon">&#984432;</div>
                <div class="text">Overview</div>
            </a>

            <a href="/dashboard/teams" class="button" :class="{'active': $store.getters.page === 'teams'}" @click.prevent="$store.dispatch('setPage', 'teams')">
                <div class="icon">&#985740;</div>
                <div class="text">My Team</div>
            </a>

            <a href="/dashboard/reports" class="button" :class="{'active': $store.getters.page === 'reports'}" @click.prevent="$store.dispatch('setPage', 'reports')">
                <div class="icon">&#988494;</div>
                <div class="text">SEO Reports</div>
            </a>

            <!-- <a href="/dashboard/reports" class="button" :class="{'active': $store.getters.page === 'reports'}" @click.prevent="$store.dispatch('setPage', 'reports')">
                <div class="icon">&#983881;</div>
                <div class="text">Keyword Generator</div>
            </a> -->

            <!-- <a href="/dashboard/reports" class="button" :class="{'active': $store.getters.page === 'reports'}" @click.prevent="$store.dispatch('setPage', 'reports')">
                <div class="icon">&#987766;</div>
                <div class="text">Keyword Analyzer</div>
            </a> -->

            <!-- <a href="/dashboard/social-media" class="button" :class="{'active': $store.getters.page === 'social-media'}" @click.prevent="$store.dispatch('setPage', 'social-media')">
                <div class="icon">&#985161;</div>
                <div class="text">Social Media</div>
            </a> -->

            <a href="/dashboard/author" class="button" :class="{'active': $store.getters.page === 'author'}" v-if="authorProfile" @click.prevent="$store.dispatch('setPage', 'author')">
                <div class="icon">&#984787;</div>
                <div class="text">Author</div>
            </a>

            <!-- <a href="/dashboard/notifications" class="button" :class="{'active': $store.getters.page === 'notifications'}" @click.prevent="$store.dispatch('setPage', 'notifications')">
                <div class="icon">&#986458;</div>
                <div class="text">Notifications</div>
                <div class="notifications">200</div>
            </a> -->

            <div class="bottom">
                <a href="/dashboard/settings" class="button" :class="{'active': $store.getters.page === 'settings'}" @click.prevent="$store.dispatch('setPage', 'settings')">
                    <div class="icon">&#984211;</div>
                    <div class="text">Settings</div>
                </a>

                <a href="/dashboard/profile" class="button" :class="{'active': $store.getters.page === 'profile'}" @click.prevent="$store.dispatch('setPage', 'profile')">
                    <img class="image" src="/images/defaults/default_profile_image.svg">
                    <div class="text bold">{{user.username}}</div>
                    <ui-icon-button class="logout-button" @click="logout()">&#984573;</ui-icon-button>
                </a>
            </div>
        </nav>

        <main>
            <transition class="transition-group" :name="'opacity-slide-'+$store.getters.pageTransitionDirection" mode="out-in">
                <overview-page class="page" key="overview-page" v-if="$store.getters.page === 'overview'"></overview-page>
                <reports-page class="page" key="reports-page" v-if="$store.getters.page === 'reports'"></reports-page>
                <teams-page class="page" key="teams-page" v-if="$store.getters.page === 'teams'"></teams-page>
                <!-- <notifications-page class="page" key="notifications-page" v-if="$store.getters.page === 'notifications'"></notifications-page> -->
                <settings-page class="page" key="settings-page" v-if="$store.getters.page === 'settings'"></settings-page>
                <profile-page class="page" key="profile-page" v-if="$store.getters.page === 'profile'"></profile-page>
                <author-page class="page" key="author-page" v-if="$store.getters.page === 'author' && authorProfile"></author-page>
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

            authorProfile() {
                return this.$store.getters.authorProfile
            },
        },

        methods: {
            logout() {
                this.$store.dispatch('logout', () => {
                    window.location = '/'
                })
            }
        },

        components: {
            OverviewPage: require('./pages/Overview.vue').default,
            ReportsPage: require('./pages/Reports.vue').default,
            TeamsPage: require('./pages/Teams.vue').default,
            NotificationsPage: require('./pages/Notifications.vue').default,
            SettingsPage: require('./pages/Settings.vue').default,
            ProfilePage: require('./pages/Profile.vue').default,
            AuthorPage: require('./pages/Author.vue').default,
        },
    }
</script>

<style lang="sass">
    #wrapper
        --menu-width: 280px

        main
            .limiter
                max-width: 1240px !important
                padding-left: 15px !important
                padding-right: 15px !important
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
            text-align: left
            position: fixed
            left: 0
            top: 0
            width: var(--menu-width)
            height: 100%
            z-index: 100
            color: var(--text-gray)
            font-size: var(--text-size)
            background: var(--bg)
            border-right: var(--border)

            #logo
                height: 60px
                width: 100%
                margin: 10px 0
                text-align: center

                img
                    height: 100%

            .logo-divider
                width: 100%
                height: 20px
                display: block

            .bottom
                position: absolute
                bottom: 0
                left: 0
                width: 100%

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

                .icon
                    font-family: 'Material Icons'
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
                    font-size: var(--button-size)
                    font-weight: 500
                    letter-spacing: 1px
                    color: inherit
                    white-space: nowrap
                    overflow: hidden
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
                    transform: translateY(-20px)

                &.opacity-slide-down-leave-to,
                &.opacity-slide-up-enter
                    opacity: 0
                    transform: translateY(20px)
</style>