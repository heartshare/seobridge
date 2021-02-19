<template>
    <div class="page-container limiter">
        <quick-check-module @details="openDetails($event)"></quick-check-module>

        <div class="details" v-if="details">

            <fieldset>
                <legend>Appearence</legend>
                <h2>{{details.title}}</h2>
                <img :src="details.favicon" width="30"><br>
                Description: <b>{{details.metaDescription}}</b><br>
                Favicon: <b>{{details.favicon}}</b><br>
                <img :src="details.preview" width="300"><br>
                <a :href="details.url.href" target="_blank">{{details.url.href}}</a><br>
            </fieldset>

            <fieldset class="half">
                <legend>Checklist</legend>
                <p>
                    Title: <b>{{details.title ? 'ok' : 'missing'}}</b><br>
                    Favicon: <b>{{details.favicon ? 'ok' : 'missing'}}</b><br>
                    Description: <b>{{details.metaDescription ? 'ok' : 'missing'}}</b><br>
                    Viewport Meta-Tag: <b>{{details.meta.findIndex(e => e.name === 'viewport') >= 0 ? 'ok' : 'missing'}}</b><br>
                </p>
            </fieldset>

            <fieldset class="half">
                <legend>System Info</legend>
                <p>
                    CMS / Generator: <b>{{details.meta.find(e => e.name === 'generator') ? details.meta.find(e => e.name === 'generator').content : 'no generator'}}</b><br>
                    Keywords: <b>{{details.meta.find(e => e.name === 'keywords') ? details.meta.find(e => e.name === 'keywords').content : 'no keywords'}}</b><br>
                </p>
            </fieldset><br>

            <fieldset v-show="details.meta.length > 0">
                <legend>Meta Tags</legend>
                <p v-for="(meta, i) in details.meta" :key="i">
                    Content: <span>{{meta.content}}</span><br>
                    Name: <span>{{meta.name}}</span><br>
                    Property: <span>{{meta.property}}</span><br>
                    Charset: <span>{{meta.charset}}</span><br>
                </p>
            </fieldset><br>

            <!-- <fieldset v-show="details.links.length > 0">
                <legend>Links</legend>
                <p v-for="(link, i) in details.links" :key="i">
                    HREF: <span>{{link.href}}</span><br>
                    <a :href="link.href">{{link.text || 'MISSING'}}</a>
                </p>
            </fieldset>

            <fieldset v-show="details.images.length > 0">
                <legend>Images</legend>
                <p v-for="(image, i) in details.images" :key="i">
                    SRC: <span>{{image.src}}</span><br>
                    Alt-Tag: <b>{{image.alt || 'MISSING'}}</b>
                </p>
            </fieldset> -->
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                details: null,
            }
        },

        methods: {
            openDetails(details) {
                console.log(details)
                this.details = details
            },
        },

        components: {
            QuickCheckModule: require('../components/QuickCheckModule.vue').default,
        },
    }
</script>

<style lang="sass" scoped>
    .page-container
        width: 100%

        .details
            width: 100%

            fieldset
                font-size: var(--text-size)
                border: var(--border)
                border-radius: 5px
                display: inline-block
                width: 100%
                margin: 0
                vertical-align: top
                overflow: hidden

                &.half
                    width: 50%

                p
                    margin: 0
                    margin-bottom: 15px
</style>