<template>
    <div class="container block">
        <p class="wrapper">
            <ui-text-input class="url-input" label="URL" v-model="url"></ui-text-input>
            <ui-button class="submit-button" icon="&#983881;" @click="analyse(url)">Analyse</ui-button>
        </p>

        <div class="report block" v-if="report" v-show="!loading">
            <h1>{{report.title}}</h1>
            <p>
                Description: <b>{{report.metaDescription}}</b><br>
            </p>

            <fieldset v-show="report.links.length > 0">
                <legend>Links</legend>
                <p v-for="(link, i) in report.links" :key="i">
                    HREF: <span>{{link.href}}</span><br>
                    <a :href="link.href">{{link.text || 'MISSING'}}</a>
                </p>
            </fieldset>

            <fieldset v-show="report.images.length > 0">
                <legend>Images</legend>
                <p v-for="(image, i) in report.images" :key="i">
                    SRC: <span>{{image.src}}</span><br>
                    Alt-Tag: <b>{{image.alt || 'MISSING'}}</b>
                </p>
            </fieldset>

        </div>

        <ui-spinner style="display: block; margin: 20px auto" v-show="loading" color="var(--primary)" :size="50"></ui-spinner>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                url: 'https://fireship.io/courses/react-next-firebase/',
                report: null,
                loading: false
            }
        },

        methods: {
            analyse(url) {
                this.loading = true

                axios.post('https://puppeteer.seobridge.test/analyse?url='+url)
                .then(response => {
                    console.log(response.data)
                    this.loading = false
                    this.report = response.data
                })
                .catch(error => {
                    this.loading = false
                })
            },
        }
    }
</script>

<style lang="sass" scoped>
    .container
        .wrapper
            position: relative

            .url-input
                padding-right: 130px

            .submit-button
                position: absolute
                top: 5px
                right: 5px

        .report
            font-size: var(--text-size)
            margin-top: 50px

            fieldset
                border: var(--border)
                border-radius: 5px
                display: block
                width: 100%
                overflow: hidden
</style>