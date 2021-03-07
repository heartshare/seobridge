<template>
    <div class="page-container limiter">

        <div class="header">
            <div class="background-image"></div>
            <img src="/images/defaults/default_profile_image.svg" class="profile-image">
        
            <form @submit.stop.prevent>
                <div class="name-wrapper">
                    <ui-text-input label="Firstname" ac="firstname" no-border v-model="nameChange.firstname"></ui-text-input>
                    <ui-text-input label="Lastname" ac="lastname" no-border v-model="nameChange.lastname"></ui-text-input>
                    <ui-button class="submit-button" @click="changeName()" :disabled="(nameChange.firstname || '') + (nameChange.lastname || '') === nameChange.legacy" :loading="nameChange.loading">Save</ui-button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                nameChange: {
                    firstname: '',
                    lastname: '',
                    legacy: null,
                    loading: false,
                },
            }
        },

        computed: {
            user() {
                return this.$store.getters.user
            },
        },

        watch: {
            user: {
                handler() {
                    this.nameChange.firstname = this.user.firstname || ''
                    this.nameChange.lastname = this.user.lastname || ''
                    this.nameChange.legacy = this.nameChange.firstname + this.nameChange.lastname
                },
                immediate: true,
            }
        },

        methods: {
            changeName() {
                this.nameChange.loading = true

                axios.post('/auth/user/change-name', {
                    firstname: this.nameChange.firstname,
                    lastname: this.nameChange.lastname,
                })
                .then(response => {
                    this.$store.commit('userFirstname', response.data.firstname)
                    this.$store.commit('userLastname', response.data.lastname)

                    this.nameChange.legacy = (response.data.firstname || '') + (response.data.lastname || '')

                    setTimeout(() => { this.nameChange.loading = false }, 500)
                })
                .catch(error => {
                    console.log(error.response)
                    this.nameChange.loading = false
                })
            },
        }
    }
</script>

<style lang="sass" scoped>
    .page-container
        width: 100%

        .header
            width: 100%
            background: var(--bg)
            filter: var(--elevation-2)
            border-radius: 7px
            margin-top: 15px

            .background-image
                height: 250px
                width: 100%
                background: var(--bg-dark)
                background-image: url('/images/app/dashboard/pattern.svg')
                background-size: cover
                background-position: center
                background-repeat: no-repeat
                border-radius: 7px 7px 0 0
                display: block

            .profile-image
                height: 140px
                width: 140px
                object-fit: cover
                border-radius: 100%
                margin: -70px auto 25px
                display: block
                padding: 5px
                background: var(--bg)

            .name-wrapper
                background: var(--bg)
                border-radius: 5px
                position: relative
                display: flex
                width: 100%
                max-width: 600px
                margin: 0 auto 30px
                
                .submit-button
                    margin: 5px

                &::after
                    content: ''
                    height: 100%
                    width: 100%
                    position: absolute
                    top: 0
                    left: 0
                    border-radius: 5px
                    border: var(--input-border)
                    pointer-events: none
                    box-sizing: border-box
</style>