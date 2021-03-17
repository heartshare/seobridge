// Laravel Echo
window.Echo = require('laravel-echo').default

// Pusher
window.Pusher = require('pusher-js')

// Lodash
window._ = require('lodash')

// Custom helper methods
window.debounce = require('./helper/debounce')

// Axios
window.axios = require('axios')
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// Vue + Vuex 
window.Vue = require('vue').default
window.Vuex = require('vuex').default
window.Vue.use(window.Vuex)

// Global Vue Components
const files = require.context('./components/ui/', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue ApexCharts
window.VueApexCharts = require('vue-apexcharts')
Vue.component('apexchart', VueApexCharts)

window.VTooltip = require('v-tooltip')
Vue.use(VTooltip)



const store = new Vuex.Store(require('./store/index'))



const app = new Vue({
    el: '#app',
    components: {
        DashboardView: require('./components/views/dashboard/Index.vue').default,
    },
    mounted() {
        this.$store.dispatch('setPage', this.$refs.initialPage.value)
        this.$store.dispatch('initialFetch')
        window.addEventListener('popstate', this.userNavigate)
    },
    computed: {
        user() {
            return this.$store.getters.user
        },
    },
    watch: {
        user() {
            if (this.user && this.user.id)
            {
                this.subscribeToUserReportTask(this.user.id)
            }
        },
    },
    methods: {
        userNavigate() {
            if (history.state) this.$store.commit('page', history.state)
        },
        subscribeToUserReportTask(userId) {
            let userReportTaskEventChannel = window.Echo.private(`App.Models.UserReportTask.${userId}`)

            userReportTaskEventChannel.listen('.status.update', (e) => {
                this.$store.commit('setPaginatedReportGroupTask', {
                    id: e.jobId,
                    task: {
                        status: e.status,
                        progress: e.data.progress,
                        progress_max: e.data.progress_max,
                    },
                })
                console.log(e)
            })

            userReportTaskEventChannel.listen('.page.add', (e) => {
                this.$store.commit('addReportToPaginatedReportGroup', {
                    id: e.jobId,
                    report: e.data,
                })
                console.log(e)
            })
        },
    },
    store,
})

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
})