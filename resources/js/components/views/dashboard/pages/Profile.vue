<template>
    <div class="page-container limiter">
        <h1>My Profile</h1>

        <p>
            <ui-text-input label="Firstname" v-model="nameChange.firstname"></ui-text-input>
            <ui-text-input label="Lastname" v-model="nameChange.lastname"></ui-text-input>
            <ui-button @click="changeName()">Change Name</ui-button>
        </p>

        <p>
            <ui-password-input label="Current Password" v-model="passwordChange.current"></ui-password-input>
            <ui-password-input label="New Password" v-model="passwordChange.new"></ui-password-input>
            <ui-button @click="changePassword()">Change Password</ui-button>
        </p>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                nameChange: {
                    firstname: '',
                    lastname: '',
                    loading: false,
                },

                passwordChange: {
                    current: '',
                    new: '',
                    loading: false,
                }
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
                    this.nameChange.firstname = this.user.firstname
                    this.nameChange.lastname = this.user.lastname
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
                    this.nameChange.loading = false
                })
                .catch(error => {
                    console.log(error.response)
                    this.nameChange.loading = false
                })
            },

            changePassword() {
                this.passwordChange.loading = true

                axios.post('/auth/user/change-password', {
                    password: this.passwordChange.current,
                    newPassword: this.passwordChange.new,
                })
                .then(response => {
                    console.log(response.data)
                    this.passwordChange.loading = false
                })
                .catch(error => {
                    console.log(error.response)
                    this.passwordChange.loading = false
                })
            },
        }
    }
</script>

<style lang="sass" scoped>
    .page-container
        width: 100%
</style>