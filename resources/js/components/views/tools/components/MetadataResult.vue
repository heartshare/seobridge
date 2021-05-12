<template>
    <div class="result-container">
        <div class="title-bar">
            <div class="title">
                <span v-if="data['title']">{{data['title']}}</span>
                <i v-else>NO PAGE TITLE</i>
            </div>

            <div class="description">
                <span v-if="data['description']">{{data['description']}}</span>
                <i v-else>NO PAGE DESCRIPTION</i>
            </div>

            <ui-icon-button class="expand-button" @click="isExpanded = !isExpanded">&#983360;</ui-icon-button>
        </div>

        <div class="deco-top"></div>

        <div class="detail-bar" v-show="isExpanded">
            <img height="200" v-if="data['image']" :src="data['image']"><br>
            Author: <div v-if="data['author']">{{data['author']}}</div><br>
            Theme Color: <div v-if="data['theme-color']">{{data['theme-color']}}</div><br>
            Viewport: <div v-if="data['viewport']">{{data['viewport']}}</div><br>
            Generator: <div v-if="data['generator']">{{data['generator']}}</div><br><br>

            <img height="200" v-if="data['og:image']" :src="data['og:image']"><br>
            OG Type: <div v-if="data['og:type']"><b>{{data['og:type']}}</b></div><br>
            OG Title: <div v-if="data['og:title']"><b>{{data['og:title']}}</b></div><br>
            OG Description: <div v-if="data['og:description']">{{data['og:description']}}</div><br>
            OG URL: <div v-if="data['og:url']">{{data['og:url']}}</div><br><br>

            <img height="200" v-if="data['twitter:image']" :src="data['twitter:image']"><br>
            Twitter Card: <div v-if="data['twitter:card']"><b>{{data['twitter:card']}}</b></div><br>
            Twitter Title: <div v-if="data['twitter:title']"><b>{{data['twitter:title']}}</b></div><br>
            Twitter Description: <div v-if="data['twitter:description']">{{data['twitter:description']}}</div><br>
            Twitter Site: <div v-if="data['twitter:site']">{{data['twitter:site']}}</div><br>
        </div>

        <div class="deco-bottom"></div>

        <div class="bottom-bar">
            {{href}}
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            href: String,
            data: Object,
        },

        data() {
            return {
                isExpanded: false,
            }
        },

        methods: {
            close() {
                this.isExpanded = false
            },

            expand() {
                this.isExpanded = true
            },
        }
    }
</script>

<style lang="sass" scoped>
    .result-container
        border-radius: 7px
        display: grid
        width: 100%
        grid-template-rows: 60px auto 30px
        grid-auto-columns: 1fr
        grid-template-areas: "titlebar" "detail" "bottombar"
        margin: 20px 0
        font-size: 14px
        line-height: 120%

        .title-bar
            padding: 7px 15px
            grid-area: titlebar
            display: grid
            grid-template-rows: 1fr 1fr
            grid-auto-columns: 1fr 40px
            grid-template-areas: "title button" "description button"
            align-items: center
            border: var(--border)
            border-bottom: none
            border-radius: 7px 7px 0 0

            .title
                font-size: 14px
                font-weight: 700
                color: var(--heading-gray)
                grid-area: title
                display: block
                white-space: nowrap
                overflow: hidden
                text-overflow: ellipsis

            .description
                grid-area: description
                font-size: 14px
                color: var(--heading-gray)
                display: block
                white-space: nowrap
                overflow: hidden
                text-overflow: ellipsis

            .expand-button
                grid-area: button
                align-self: center
                justify-self: center

        .detail-bar
            grid-area: detail
            display: block
            padding: 15px 15px
            // box-shadow: inset 0 0 12px #00000030
            border: var(--border)
            border-bottom: none
            background-color: var(--bg-dark)

        .bottom-bar
            padding: 0 15px
            grid-area: bottombar
            display: block
            height: 100%
            line-height: 29px
            white-space: nowrap
            font-size: 11px
            text-transform: uppercase
            font-weight: 700
            letter-spacing: 1px
            align-self: center
            color: var(--text-gray)
            border: var(--border)
            border-radius: 0 0 7px 7px
</style>