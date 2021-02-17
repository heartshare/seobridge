<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Example Component</div>

                    <div class="card-body">
                        {{radius.chrome.x}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const caniuse = require('caniuse-api')
    export default {
        computed: {
            radius() {
                return caniuse.getSupport('clip-path')
            }
        },
    }
</script>
