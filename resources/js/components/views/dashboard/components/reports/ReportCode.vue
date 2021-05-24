<template>
    <div class="code code-hightlight-in-code-view-2157689" v-highlight>
        <pre class="language-html"><code>{{formattedCode}}</code></pre>
    </div>
</template>

<script>
    const beautify = require('js-beautify').html
    export default {
        props: {
            code: String,
        },

        computed: {
            formattedCode() {
                return beautify(this.code, {
                    "indent_size": "4",
                    "indent_char": " ",
                    "max_preserve_newlines": "5",
                    "preserve_newlines": true,
                    "keep_array_indentation": false,
                    "break_chained_methods": false,
                    "indent_scripts": "normal",
                    "brace_style": "collapse",
                    "space_before_conditional": true,
                    "unescape_strings": false,
                    "jslint_happy": false,
                    "end_with_newline": false,
                    "wrap_line_length": "0",
                    "indent_inner_html": true,
                    "comma_first": false,
                    "e4x": false,
                    "indent_empty_lines": false
                }) || ''
            },
        },

        data() {
            return {

            }
        },
    }
</script>

<style lang="sass">
    .code-hightlight-in-code-view-2157689
        span
            display: inline
</style>

<style lang="sass" scoped>
    .code
        width: 100%
        overflow: auto
        max-height: 700px
        display: block

        pre
            margin: 0
</style>