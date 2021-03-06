window._ = require('lodash')

window.axios = require('axios')
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

window.Vue = require('vue').default

const files = require.context('./components/ui/', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.use(require('vue-silentbox').default)

const app = new Vue({
    el: '#app',

    // Idealy we dont have any specific components in here...
    // TODO: put component out of static.js
    components: {
        GoProForm: require('./components/views/go-pro/components/GoProForm.vue').default,
        ToolMetadata: require('./components/views/tools/Metadata.vue').default,
    }
})
