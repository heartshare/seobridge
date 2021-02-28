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
        reports: [],
        reportScanning: false,
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

        reports(state) {
            return state.reports
        },

        reportScanning(state) {
            return state.reportScanning
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



        createReport(store, data) {
            store.commit('reportScanning', true)

            axios.post('/auth/reports/url', {url: data})
            .then(response => {
                // console.log(response.data)
                store.commit('reportScanning', false)
                store.commit('addReport', response.data)
            })
            .catch(error => {
                console.log(error.response)
                store.commit('reportScanning', false)
            })
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

        fetchAllReports(store) {
            axios.post('/auth/reports/get-all-reports')
            .then(response => {
                store.commit('reports', response.data)
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
            store.dispatch('fetchAllReports')
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
                state.teams.push(data)
            }
        },

        deleteTeam(state, data) {
            let index = state.teams.findIndex(e => e.id === data)

            if (index >= 0)
            {
                state.teams.splice(index, 1)
            }
        },



        reports(state, data) {
            state.reports = data
        },

        addReport(state, data) {
            state.reports.unshift(data)
        },

        reportScanning(state, data) {
            state.reportScanning = data
        },



        notifications(state, data) {
            state.notifications = data
        },
    },
}