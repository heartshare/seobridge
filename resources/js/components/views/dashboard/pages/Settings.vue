<template>
    <div class="page-container">
        <div class="page-header">
            <div class="limiter">
                <div class="page-header-wrapper">
                    <div class="row">
                        <h1>Settings</h1>
                        <div class="spacer"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="limiter overlap">
            <div class="sheet">
                <ui-tabs-header :tabs="{'general': 'General', 'profile': 'Profile', 'subscriptions': 'Subscriptions', 'security': 'Security'}" v-model="tab"></ui-tabs-header>

                <div class="tab-box" v-show="tab === 'general'">
                    <div class="row-wrapper">
                        <b>Close your account</b>
                        <div class="spacer"></div>
                        <ui-button small error :loading="accountClose.loading" @click="openAccountCloseDialog()">Close</ui-button>
                    </div>
                </div>

                <div class="tab-box" v-show="tab === 'profile'">
                    <form @submit.stop.prevent>
                        <div class="row-wrapper border">
                            <b>Change Your Name</b>
                            <div class="spacer"></div>
                            <ui-button small class="submit-button" @click="changeName()" :disabled="(nameChange.firstname || '') + (nameChange.lastname || '') === nameChange.legacy" :loading="nameChange.loading">Save</ui-button>
                        </div>

                        <div class="row-wrapper border">
                            <ui-text-input label="Firstname" no-border ac="firstname" v-model="nameChange.firstname"></ui-text-input>
                            <ui-text-input label="Lastname" no-border ac="lastname" v-model="nameChange.lastname"></ui-text-input>
                        </div>
                    </form>
                </div>

                <div class="tab-box" v-show="tab === 'security'">
                    <div class="row-wrapper border">
                        <b>Change Password</b>
                        <div class="spacer"></div>
                        <ui-button small :loading="passwordChange.loading" @click="openPasswordChangeDialog()">Edit</ui-button>
                    </div>
                    
                    <div class="row-wrapper border">
                        <b>Multi Factor Authentication</b>
                        <div class="spacer"></div>
                        <ui-switch style="margin: 5px 0" :value="user.is_mfa_enabled" @input="setMFAStatus($event)"></ui-switch>
                    </div>

                    <div class="row-wrapper">
                        <span>App Authentication <b v-show="userMFAMethods.TOTP && userMFAMethods.TOTP.is_verified">(aktive)</b></span>
                        <div class="spacer"></div>
                        <ui-button v-if="userMFAMethods.TOTP === null" small text border :loading="TOTPMFASetup.generationLoading" @click="generateTOTPMFA()">Setup</ui-button>
                        
                        <ui-button text small v-if="userMFAMethods.TOTP !== null" v-show="!TOTPMFASetup.expand" @click="TOTPMFASetup.expand = true">Expand</ui-button>
                        <ui-button text small v-if="userMFAMethods.TOTP !== null" v-show="TOTPMFASetup.expand" @click="TOTPMFASetup.expand = false">Minimize</ui-button>
                        <!-- <ui-icon-button v-if="userMFAMethods.TOTP !== null" @click="TOTPMFASetup.expand = !TOTPMFASetup.expand">
                            {{TOTPMFASetup.expand ? '&#983363;' : '&#983360;'}}
                        </ui-icon-button> -->
                    </div>

                    <div class="row-wrapper" v-if="userMFAMethods.TOTP !== null" v-show="TOTPMFASetup.expand">
                        <qr-code v-if="TOTPMFASetup.url" :value="TOTPMFASetup.url" :options="{width: 140, margin: 0}"></qr-code>
                        <ui-button v-if="!TOTPMFASetup.url" @click="getTOTPMFA()">Show Code</ui-button>
                        <div class="spacer"></div>
                        <ui-button small text border icon="&#984144;" @click="generateTOTPMFA()">New Code</ui-button>
                        <ui-button small error icon="&#985721;" @click="deleteTOTPMFA()">Delete</ui-button>
                    </div>
                    <div class="row-wrapper" v-if="userMFAMethods.TOTP && userMFAMethods.TOTP.is_verified === false" v-show="TOTPMFASetup.expand">
                        <ui-text-input style="width: 160px" label="Passnumber" v-model="TOTPMFASetup.TOTPInput"></ui-text-input>
                        <ui-button @click="verifyTOTPMFA()">Verify</ui-button>
                        <div class="spacer"></div>
                    </div>

                    <div class="row-wrapper">
                        <span>SMS Authentication (coming later)</span>
                        <div class="spacer"></div>
                        <ui-button small text border disabled>Setup</ui-button>
                        <!-- <ui-icon-button>&#983360;</ui-icon-button> -->
                    </div>

                    <div class="row-wrapper">
                        <span>Security Key (coming later)</span>
                        <div class="spacer"></div>
                        <ui-button small text border disabled>Setup</ui-button>
                        <!-- <ui-icon-button>&#983360;</ui-icon-button> -->
                    </div>
                </div>

                <div class="block" v-show="tab === 'subscriptions'">
                    <div class="tab-box" style="padding: 0">
                        <div class="row-wrapper border" style="padding: 15px">
                            <b>Your billing portal powered by Stripe</b>
                            <div class="spacer"></div>
                            <ui-button href="/auth/subscriptions/billing-portal" icon="&#984012;" small text>Billing Portal</ui-button>
                        </div>
                        <div class="row-wrapper border" style="padding: 15px">
                            <b>Your billing methods</b>
                            <div class="spacer"></div>
                            <ui-button small text border :loading="setupIntent.loading" @click="getSetupIntent()">Add Credit Card</ui-button>
                        </div>
                    </div>

                    <div class="tab-box" v-show="setupIntent.intent">
                        <ui-text-input label="Card holder name" v-model="setupIntent.cardHolderName"></ui-text-input>

                        <div id="card-element"></div>

                        <ui-button @click="addCreditCard()">Add Card now</ui-button>
                    </div>

                    <payment-method-row
                        v-for="method in paymentMethod.methods"
                        :key="method.id"
                        :method="method"
                        :is-default="method.id === paymentMethod.default"
                        @setDefault="setDefaultPaymentMethod($event)"
                        @delete="deletePaymentMethod($event)"
                    ></payment-method-row>
                </div>
            </div>
        </div>

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
    const stripe = Stripe('pk_test_51IPwiCDa1TGHitv5NwiZUsCe9Yy28YRgBjzXUVoFSs5eqQnRgUeXoUNO6hEl3CXWWq63E954U8Cw3nt0vSo3Yx8C0089OD4j7Z')

    const elements = stripe.elements()
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontFamily: 'Roboto, Open Sans, Segoe UI, sans-serif',
                fontSize: '15px',
                color: '#2F3542',
                '::placeholder': {
                    color: '#666F79',
                },
            },
        },
    })

    import VueQrcode from '@chenfengyuan/vue-qrcode';
    
    export default {
        data() {
            return {
                tab: 'general',

                nameChange: {
                    firstname: '',
                    lastname: '',
                    legacy: null,
                    loading: false,
                },

                passwordChange: {
                    current: '',
                    new: '',
                    loading: false,
                },

                accountClose: {
                    password: '',
                    loading: false,
                },

                TOTPMFASetup: {
                    url: null,
                    TOTPInput: '',
                    expand: false,
                    generationLoading: false,
                },

                setupIntent: {
                    intent: null,
                    cardHolderName: '',
                    loading: false,
                },

                paymentMethod: {
                    methods: [],
                    default: null
                }
            }
        },

        mounted() {
            cardElement.mount('#card-element')

            axios.post('/auth/subscriptions/get-all-payment-methods', {})
            .then(response => {
                this.paymentMethod.methods = response.data.methods
                this.paymentMethod.default = response.data.default
            })
            .catch(error => {
                console.log(error.response)
            })
        },

        computed: {
            user() {
                return this.$store.getters.user
            },

            userMFAMethods() {
                let methods = {
                    TOTP: null,
                    SMS: null,
                    WEBAUTHN: null,
                }

                if (!this.user || typeof this.user.mfa_methods !== 'object')
                {
                    return methods
                }

                for (const method of this.user.mfa_methods)
                {
                    methods[method.type] = method
                }

                return methods
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



            changeName() {
                this.nameChange.loading = true

                axios.post('/auth/user/change-name', {
                    firstname: this.nameChange.firstname,
                    lastname: this.nameChange.lastname,
                })
                .then(response => {
                    this.$store.commit('userInfo', {
                        firstname: response.data.firstname,
                        lastname: response.data.lastname,
                    })

                    this.nameChange.legacy = (response.data.firstname || '') + (response.data.lastname || '')

                    setTimeout(() => { this.nameChange.loading = false }, 500)
                })
                .catch(error => {
                    console.log(error.response)
                    this.nameChange.loading = false
                })
            },



            setMFAStatus(status) {
                axios.post('/auth/user/set-mfa-status', {
                    status,
                })
                .then(response => {
                    console.log(response)
                })
                .catch(error => {
                    console.log(error.response)
                })
            },



            generateTOTPMFA() {
                this.TOTPMFASetup.generationLoading = true

                axios.post('/auth/user/generate-totp-mfa')
                .then(response => {
                    this.$store.dispatch('fetchUser')
                    this.TOTPMFASetup.url = response.data
                    this.TOTPMFASetup.expand = true
                    this.TOTPMFASetup.generationLoading = false
                })
                .catch(error => {
                    console.log(error.response)
                    this.TOTPMFASetup.generationLoading = false
                })
            },

            getTOTPMFA() {
                axios.post('/auth/user/get-totp-mfa-url')
                .then(response => {
                    this.TOTPMFASetup.url = response.data
                })
                .catch(error => {
                    console.log(error.response)
                })
            },

            deleteTOTPMFA() {
                axios.post('/auth/user/delete-totp-mfa')
                .then(response => {
                    this.$store.dispatch('fetchUser')
                    this.TOTPMFASetup.expand = false
                })
                .catch(error => {
                    console.log(error.response)
                })
            },

            verifyTOTPMFA() {
                axios.post('/auth/user/verify-totp-mfa', {
                    secret: this.TOTPMFASetup.TOTPInput,
                })
                .then(response => {
                    console.log(response)
                    this.TOTPMFASetup.TOTPInput = ''
                    this.$store.dispatch('fetchUser')
                })
                .catch(error => {
                    console.log(error.response)
                    this.TOTPMFASetup.TOTPInput = ''
                })
            },



            getSetupIntent() {
                this.setupIntent.loading = true

                axios.post('/auth/subscriptions/get-setup-intent')
                .then(response => {
                    setTimeout(() => {
                        this.setupIntent.intent = response.data
                        this.setupIntent.loading = false
                    }, 1000)
                })
                .catch(error => {
                    console.log(error.response)
                    this.setupIntent.loading = false
                })
            },

            async addCreditCard() {
                const { setupIntent, error } = await stripe.confirmCardSetup(this.setupIntent.intent.client_secret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: { name: this.setupIntent.cardHolderName }
                    }
                })

                if (error)
                {
                    console.log(error.message)
                    return
                }

                this.setupIntent.intent = null
                this.setupIntent.cardHolderName = ''

                axios.post('/auth/subscriptions/add-payment-method-to-user', {
                    paymentMethod: setupIntent.payment_method,
                })
                .then(response => {
                    console.log('CARD ADDED!')
                })
                .catch(error => {
                    console.log(error.response)
                })
            },



            setDefaultPaymentMethod(method) {
                axios.post('/auth/subscriptions/set-default-payment-method', {
                    paymentMethod: method.id
                })
                .then(response => {
                    this.paymentMethod.default = response.data
                    console.log('CHANGED DEFAULT METHOD!')
                })
                .catch(error => {
                    console.log(error.response)
                })
            },

            deletePaymentMethod(method) {
                axios.post('/auth/subscriptions/delete-payment-method', {
                    paymentMethod: method.id
                })
                .then(response => {
                    console.log(response.data)
                    console.log('DELETED!')
                })
                .catch(error => {
                    console.log(error.response)
                })
            },



            createSubscription() {
                axios.post('/auth/subscriptions/create-test-subscription')
                .then(response => {
                    console.log(response.data)
                    console.log('YAY! IT WORKED!')
                })
                .catch(error => {
                    console.log(error.response)
                })
            },
        },

        components: {
            'qr-code': VueQrcode,
            PaymentMethodRow: require('./../components/PaymentMethodRow').default,
        }
    }
</script>

<style lang="sass" scoped>
    .page-container
        width: 100%
        display: inline-block !important

        .tab-box
            padding: 15px
            display: block
            width: 100%

            .row-wrapper
                display: flex
                align-items: center
                padding: 10px 0
                gap: 10px
                
                &.border
                    border-bottom: var(--border)

                .spacer
                    flex: 1

        .name-wrapper
            background: var(--bg)
            border-radius: 5px
            position: relative
            display: flex
            width: 100%
            max-width: 600px
            
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

        #card-element
            border: var(--border)
            border-radius: 5px
            padding: 15px
            width: 100%
</style>