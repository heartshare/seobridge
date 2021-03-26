<template>
    <div class="page-container limiter">
        <div class="header">
            <div class="background-image"></div>
            <img src="/images/defaults/default_profile_image.svg" class="profile-image">
        </div>

        <ui-text-input label="New Article Title" v-model="articleCreate.title"></ui-text-input>
        <ui-button @click="createArticle()" :loading="articleCreate.loading">Create Article</ui-button>

        <div class="article-editor">
            <ui-spinner v-show="articleEdit.loading"></ui-spinner>

            <ui-text-input label="Article Title" v-model="articleEdit.title"></ui-text-input>
            <ui-text-input label="Article URL" v-model="articleEdit.url"></ui-text-input>
            <ui-text-input label="Intro Image URL" v-model="articleEdit.introImage"></ui-text-input>
            <ui-textarea label="Intro text" :max="500" show-max v-model="articleEdit.introText"></ui-textarea>

            <div class="editor">
                <editor-menu-bar :editor="editor" v-slot="{ commands, isActive }">
                    <div class="editor-menubar">
                        <div class="group">
                            <button class="menubar-button" :class="{ 'is-active': isActive.paragraph() }" @click="commands.paragraph">&#983677;</button>
                            <button class="menubar-button" :class="{ 'is-active': isActive.bold() }" @click="commands.bold">&#983652;</button>
                            <button class="menubar-button" :class="{ 'is-active': isActive.italic() }" @click="commands.italic">&#983671;</button>
                            <button class="menubar-button" :class="{ 'is-active': isActive.strike() }" @click="commands.strike">&#983680;</button>
                            <button class="menubar-button" :class="{ 'is-active': isActive.underline() }" @click="commands.underline">&#983687;</button>
                        </div>
                        <div class="group">
                            <button class="menubar-button" :class="{ 'is-active': isActive.heading({ level: 2 }) }" @click="commands.heading({ level: 2 })">&#983660;</button>
                            <button class="menubar-button" :class="{ 'is-active': isActive.heading({ level: 3 }) }" @click="commands.heading({ level: 3 })">&#983661;</button>
                            <button class="menubar-button" :class="{ 'is-active': isActive.heading({ level: 4 }) }" @click="commands.heading({ level: 4 })">&#983662;</button>
                            <button class="menubar-button" :class="{ 'is-active': isActive.heading({ level: 5 }) }" @click="commands.heading({ level: 5 })">&#983663;</button>
                            <button class="menubar-button" :class="{ 'is-active': isActive.heading({ level: 5 }) }" @click="commands.heading({ level: 6 })">&#983664;</button>
                        </div>
                        <!-- <div class="group">
                            <button class="menubar-button" :class="{ 'is-active': isActive.bullet_list() }" @click="commands.bullet_list">&#983673;</button>
                            <button class="menubar-button" :class="{ 'is-active': isActive.ordered_list() }" @click="commands.ordered_list">&#983675;</button>
                        </div> -->
                        <!-- <div class="group">
                            <button class="menubar-button" :class="{ 'is-active': isActive.blockquote() }" @click="commands.blockquote">&#983678;</button>
                            <button class="menubar-button" :class="{ 'is-active': isActive.code() }" @click="commands.code">&#983412;</button>
                            <button class="menubar-button" :class="{ 'is-active': isActive.code_block() }" @click="commands.code_block">&#983402;</button>
                        </div> -->
                        <button class="menubar-button" @click="commands.undo">&#984397;</button>
                        <button class="menubar-button" @click="commands.redo">&#984143;</button>
                    </div>
                </editor-menu-bar>
                <editor-content class="editor-content" :editor="editor" />
            </div>
        </div>

    </div>
</template>

<script>
    import
    {
        Editor,
        EditorContent,
        EditorMenuBar,
    } from 'tiptap'

    import
    {
        Blockquote,
        CodeBlock,
        HardBreak,
        Heading,
        OrderedList,
        BulletList,
        ListItem,
        TodoItem,
        TodoList,
        Bold,
        Code,
        Italic,
        Link,
        Strike,
        Underline,
        History,
    } from 'tiptap-extensions'
    
    export default {
        data() {
            return {
                articleCreate: {
                    title: '',
                    loading: false,
                },

                articleEdit: {
                    id: null,
                    url: '',
                    title: '',
                    introImage: '',
                    introText: '',
                    fullText: '',
                    loading: false,
                },

                editor: new Editor({
                    content: '<p>This is just a boring paragraph</p>',
                    extensions: [
                        new Blockquote(),
                        new BulletList(),
                        new CodeBlock(),
                        new HardBreak(),
                        new Heading({level: [2,3,4,5,6]}),
                        new ListItem(),
                        new OrderedList(),
                        new TodoItem(),
                        new TodoList(),
                        new Link(),
                        new Bold(),
                        new Code(),
                        new Italic(),
                        new Strike(),
                        new Underline(),
                        new History(),
                    ],
                    onUpdate: ({ getHTML }) => {
                        this.articleEdit.fullText = getHTML()
                    },
                }),
            }
        },

        watch: {
            'articleEdit.title': function() {
                this.articleEdit.loading = true
                this.debouncedArticleSave()
            },
            'articleEdit.url': function() {
                this.articleEdit.loading = true
                this.debouncedArticleSave()
            },
            'articleEdit.introText': function() {
                this.articleEdit.loading = true
                this.debouncedArticleSave()
            },
            'articleEdit.introImage': function() {
                this.articleEdit.loading = true
                this.debouncedArticleSave()
            },
            'articleEdit.fullText': function() {
                this.articleEdit.loading = true
                this.debouncedArticleSave()
            },
        },

        computed: {
            user() {
                return this.$store.getters.user
            },
        },

        methods: {
            createArticle() {
                this.articleCreate.loading = true
                
                axios.post('/auth/author/create-article', {
                    title: this.articleCreate.title,
                })
                .then(response => {
                    console.log(response.data)
                    this.articleEdit.id = response.data.id
                    this.articleEdit.url = response.data.url
                    this.articleEdit.title = response.data.title
                    this.articleCreate.loading = false
                })
                .catch(error => {
                    console.log(error.response)
                    this.articleCreate.loading = false
                })
            },

            debouncedArticleSave: debounce(function() {
                this.saveArticle()
            }, 1000),

            saveArticle() {
                this.articleEdit.loading = true

                axios.post('/auth/author/update-article', {
                    title: this.articleEdit.title,
                })
                .then(response => {
                    this.articleEdit.loading = false
                })
                .catch(error => {
                    console.log(error.response)
                    this.articleEdit.loading = false
                })
            },
        },

        beforeDestroy() {
            this.editor.destroy()
        },

        components: {
            EditorContent,
            EditorMenuBar,
        },
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
                background-image: url('/images/static/assets/terrain.svg')
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

        .article-editor
            background: var(--bg)
            filter: var(--elevation-2)
            width: 100%
            border-radius: 7px
            padding: 15px
            display: flex
            flex-direction: column
            gap: 15px

            .editor
                border-radius: 5px
                background: var(--bg)
                width: 100%
                display: flex
                flex-direction: column
                border: var(--border)

                .editor-menubar
                    width: 100%
                    display: flex
                    gap: 5px
                    padding: 5px
                    border-bottom: var(--border)
                    flex-wrap: wrap
                    align-items: center

                    .group
                        display: flex
                        gap: 5px
                        padding: 2px
                        border: var(--border)
                        border-radius: 6px

                    .menubar-button
                        border-radius: 5px
                        display: grid
                        place-content: center
                        height: 30px
                        width: 40px
                        border: none
                        padding: 0
                        background: transparent
                        font-size: 20px
                        font-family: 'Material Icons'
                        color: var(--heading-gray)

                        &:hover
                            background: var(--text-gray-shade)

                        &.is-active
                            background: var(--primary-shade)
                            color: var(--primary)

                .editor-content
                    padding: 5px 15px
                    height: 500px
                    overflow-y: auto
</style>