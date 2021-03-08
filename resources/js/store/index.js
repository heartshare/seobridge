module.exports = {
    state: {
        page: null,
        validPages: [
            'overview',
            'reports',
            'teams',
            'notifications',
            'settings',
            'profile',
        ],
        pageTransitionDirection: 'down',
        navbar: 'open',
        user: {},
        teams: [],
        invites: [],
        reports: [],
        paginatedReportGroups: {},
        notifications: [],
    },

    getters: {
        page(state) {
            return state.page
        },

        pageTransitionDirection(state) {
            return state.pageTransitionDirection
        },

        navbar(state) {
            return state.navbar
        },

        user(state) {
            return state.user
        },

        teams(state) {
            return state.teams
        },

        invites(state) {
            return state.invites
        },

        reports(state) {
            return state.reports
        },

        paginatedReportGroups(state) {
            return state.paginatedReportGroups
        },

        notifications(state) {
            return state.notifications
        },
    },

    actions: {
        setPage(store, data) {
            store.commit('page', data)
            history.pushState(data, data, '/dashboard/'+data)
        },

        setNavbar(store, data) {
            store.commit('navbar', data)
        },

        logout(store, callback) {
            axios.post('/logout').then(() => { callback() }).catch(error => {})
        },



        fetchUser(store) {
            axios.post('/auth/user/get-user')
            .then(response => {
                store.commit('user', response.data)
            })
            .catch(error => {
                console.log(error.response)
            })
        },

        fetchAllTeams(store) {
            axios.post('/auth/team/get-all-teams')
            .then(response => {
                store.commit('teams', response.data)
            })
            .catch(error => {
                console.log(error.response)
            })
        },

        fetchAllInvites(store) {
            axios.post('/auth/team/get-all-invites')
            .then(response => {
                store.commit('invites', response.data)
            })
            .catch(error => {
                console.log(error.response)
            })
        },

        fetchPaginatedReportGroups(store, data = {}) {
            axios.post(`/auth/reports/get-paginated-report-groups`, {
                order: data.order || 'DESC',
                page: data.page || 1,
                size: data.size || 20,
                searchKey: data.searchKey || '',
            })
            .then(response => {
                store.commit('paginatedReportGroups', {...response.data, loading: false})
            })
            .catch(error => {
                console.log(error.response)
            })
        },

        fetchAllNotifications(store) {
            axios.post('/auth/notifications/get-all-notifications')
            .then(response => {
                store.commit('notifications', response.data)
            })
            .catch(error => {
                console.log(error.response)
            })
        },

        initialFetch(store) {
            store.dispatch('fetchUser')
            store.dispatch('fetchAllTeams')
            store.dispatch('fetchAllInvites')
            store.dispatch('fetchPaginatedReportGroups')
            store.dispatch('fetchAllNotifications')
        },
    },

    mutations: {
        page(state, data) {
            let prevIndex = state.validPages.indexOf(state.page)
            let newIndex = state.validPages.indexOf(data)

            state.page = data
            state.pageTransitionDirection = (prevIndex !== -1 && newIndex !== -1 && newIndex > prevIndex) ? 'up' : 'down'
        },



        navbar(state, data) {
            state.navbar = data
        },



        user(state, data) {
            state.user = data
        },

        userFirstname(state, data) {
            state.user.firstname = data
        },

        userLastname(state, data) {
            state.user.firstname = data
        },



        teams(state, data) {
            state.teams = data
        },

        setTeam(state, data) {
            let index = state.teams.findIndex(e => e.id === data.id)

            if (index >= 0)
            {
                Vue.set(state.teams, index, data)
            }
            else
            {
                state.teams.unshift(data)
            }
        },

        deleteTeam(state, data) {
            let index = state.teams.findIndex(e => e.id === data)

            if (index >= 0)
            {
                state.teams.splice(index, 1)
            }
        },



        invites(state, data) {
            state.invites = data
        },



        reports(state, data) {
            state.reports = data
        },

        deleteReport(state, data) {
            let index = state.reports.findIndex(e => e.id === data)

            if (index >= 0)
            {
                state.reports.splice(index, 1)
            }
        },



        paginatedReportGroups(state, data) {
            state.paginatedReportGroups = data
        },

        deleteReportGroup(state, data) {
            let index = state.paginatedReportGroups.data.findIndex(e => e.id === data)

            if (index >= 0)
            {
                state.paginatedReportGroups.data.splice(index, 1)
            }
        },



        notifications(state, data) {
            state.notifications = data
        },
    },
}