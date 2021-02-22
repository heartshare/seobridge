// Lodash
window._ = require('lodash')

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



const store = new Vuex.Store(require('./store/index'))



const app = new Vue({
    el: '#app',
    components: {
        DashboardView: require('./components/views/dashboard/Index.vue').default,
    },
    mounted() {
        this.$store.dispatch('setPage', this.$refs.initialPage.value)
        window.addEventListener('popstate', this.userNavigate)
    },
    methods: {
        userNavigate() {
            if (history.state) this.$store.commit('page', history.state)
        },
    },
    store,
})




// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });