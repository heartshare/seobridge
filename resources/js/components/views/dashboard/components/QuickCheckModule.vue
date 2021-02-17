<template>
    <div class="container block">
        <p class="wrapper">
            <ui-text-input class="url-input" label="URL" v-model="url"></ui-text-input>
            <ui-button class="submit-button" icon="&#983881;" @click="analyse(url)">Analyse</ui-button>
        </p>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                url: 'https://freuwort.com/'
            }
        },

        methods: {
            analyse(url) {

                axios.post('/auth/analyse/url', {url})
                .then(response => {
                    console.log(response.data)
                })
                .catch(error => {

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
</style>