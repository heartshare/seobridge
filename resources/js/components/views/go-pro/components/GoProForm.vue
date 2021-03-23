<template>
    <form method="POST" action="/go-pro/complete" @submit.prevent>
        <p>
            <ui-text-input label="Street" ac="street"></ui-text-input>
        </p>
        <p>
            <ui-text-input label="City" ac="city"></ui-text-input>
        </p>
        <p>
            <ui-text-input label="Country" ac="country"></ui-text-input>
        </p>
        <p>
            <ui-text-input label="Zip code" ac="zip"></ui-text-input>
        </p>
        <p>&nbsp;</p>
        <p>
            <ui-text-input label="Card holder name"></ui-text-input>
        </p>
        <p>
            <!-- Stripe Elements Placeholder -->
            <span class="block" id="card-element"></span>
        </p>
        <p>
            <ui-button @click="addCreditCard()">Subscribe</ui-button>
        </p>
    </form>
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

    export default {
        props: {
            secret: String,
        },

        mounted() {
            cardElement.mount('#card-element')
        },

        methods: {
            async addCreditCard() {
                const { setupIntent, error } = await stripe.confirmCardSetup(this.secret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: { name: 'Card Holder Name' }
                    }
                })
    
                if (error)
                {
                    console.log(error.message)
                }
                else
                {
                    console.log('CARD ADDED!')
                    console.log(setupIntent);

                    axios.post('/auth/go-pro/complete', {
                        paymentMethodId: setupIntent.payment_method,
                    })
                    .then(response => {
                        console.log(response.data)
                    })
                    .catch(error => {
                        console.log(error.response)
                    })
                }
            },
        },
    }
</script>

<style lang="sass" scoped>
    #card-element
        border: var(--border)
        border-radius: 5px
        padding: 15px
        width: 100%
</style>