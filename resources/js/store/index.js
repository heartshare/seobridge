module.exports = {
    state: {
        page: null,
        validPages: [
            'overview',
            'reports',
            'team',
            'notifications',
            'settings',
            'profile',
        ],
        pageTransitionDirection: 'down',
        navbar: 'open',
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
    },

    actions: {
        setPage(context, data) {
            context.commit('page', data)
            history.pushState(data, data, '/dashboard/'+data)
        },

        setNavbar(context, data) {
            context.commit('navbar', data)
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
    },
}