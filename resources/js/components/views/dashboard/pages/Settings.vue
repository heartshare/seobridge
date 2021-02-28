<template>
    <div class="page-container limiter">
        <h1>Settings</h1>

        <fieldset>
            <legend>Account settings</legend>

            <ui-button icon="&#984421;" :loading="passwordChange.loading" @click="openPasswordChangeDialog()">Change Password</ui-button><br><br>
            <ui-button icon="&#983394;" text border>Request Data (WIP)</ui-button>
        </fieldset><br>

        <fieldset>
            <legend>Close Account</legend>
            <ui-button error :loading="accountClose.loading" @click="openAccountCloseDialog()">Close your account</ui-button>
        </fieldset>

        <ui-option-dialog ref="passwordChangeDialog" @close="resetPasswordChange()">
            <template v-slot:heading>
                Change your password
            </template>

            <template v-slot:inputs>
                <ui-password-input label="Current Password" v-model="passwordChange.current"></ui-password-input>
                <ui-password-input label="New Password" ac="new-password" rating v-model="passwordChange.new"></ui-password-input>
            </template>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetPasswordChange()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button :loading="passwordChange.loading" :disabled="passwordChange.current.length < 8 || passwordChange.new.length < 8" @click="changePassword()">Change Now</ui-button>
            </template>
        </ui-option-dialog>

        <ui-option-dialog ref="accountCloseDialog" @close="resetAccountClose()">
            <template v-slot:heading>
                Close your account
            </template>

            <span>
                Do you want to permanently close your account?
            </span>

            <template v-slot:inputs>
                <ui-password-input label="Current Password" v-model="accountClose.password"></ui-password-input>
            </template>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetAccountClose()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button error :loading="accountClose.loading" :disabled="accountClose.password.length < 8" @click="closeAccount()">Close Now</ui-button>
            </template>
        </ui-option-dialog>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                passwordChange: {
                    current: '',
                    new: '',
                    loading: false,
                },

                accountClose: {
                    password: '',
                    loading: false,
                }
            }
        },

        computed: {
            user() {
                return this.$store.getters.user
            },
        },

        methods: {
            openPasswordChangeDialog() {
                this.$refs.passwordChangeDialog.open()
            },

            resetPasswordChange() {
                this.passwordChange.current = ''
                this.passwordChange.new = ''
                this.$refs.passwordChangeDialog.close()
            },

            changePassword() {
                this.passwordChange.loading = true

                axios.post('/auth/user/change-password', {
                    password: this.passwordChange.current,
                    newPassword: this.passwordChange.new,
                })
                .then(response => {
                    setTimeout(() => {
                        this.resetPasswordChange()
                        this.passwordChange.loading = false
                    }, 1000)
                })
                .catch(error => {
                    console.log(error.response)
                    this.passwordChange.loading = false
                })
            },



            openAccountCloseDialog() {
                this.$refs.accountCloseDialog.open()
            },

            resetAccountClose() {
                this.accountClose.password = ''
                this.$refs.accountCloseDialog.close()
            },

            closeAccount() {
                this.accountClose.loading = true

                axios.post('/auth/user/close-account', {
                    password: this.accountClose.password,
                })
                .then(response => {
                    setTimeout(() => {
                        this.resetAccountClose()
                        this.accountClose.loading = false
                        this.$store.dispatch('logout', ()=>{
                            window.location = '/'
                        })
                    }, 1000)
                })
                .catch(error => {
                    console.log(error.response)
                    this.accountClose.loading = false
                })
            },
        }
    }
</script>

<style lang="sass" scoped>
    .page-container
        width: 100%
</style>