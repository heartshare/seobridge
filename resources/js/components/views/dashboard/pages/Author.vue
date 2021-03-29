<template>
    <div class="page-container limiter">
        <div class="header">
            <div class="author-profile-wrapper">
                <div class="background-image"></div>
                <img :src="authorProfile.image" class="profile-image">

                <div class="text-wrapper">
                    <h4 class="name">{{authorProfile.display_name}}</h4>
                    <p class="biography">{{authorProfile.biography}}</p>
                </div>
            </div>

            <div class="author-article-list">
                <div class="article" v-for="article in authorArticles" :key="article.id">
                    <div class="indicator" :class="{'published': article.published_at}"></div>

                    <div class="article-info">
                        <p class="title"><b>{{article.title}}</b></p>
                        <p class="intro-text">{{article.intro_text || 'No intro text!'}}</p>
                    </div>

                    <ui-icon-button class="button" @click="openArticleEditForm(article)">&#984043;</ui-icon-button>
                    <ui-icon-button class="button" error @click="openArticleDeleteDialog(article)">&#985721;</ui-icon-button>
                </div>
            </div>
        </div>

        <div class="article-editor">
            <div class="flex-bar">
                <ui-button text>Close</ui-button>
                <div class="spacer"></div>
                <ui-button :loading="articleEdit.loading" text border @click="saveArticle()">Save</ui-button>
                <ui-button v-if="!articleEdit.publishedAt" icon="&#984743;">Publish</ui-button>
                <ui-button v-else icon="&#983561;">Unpublish</ui-button>
            </div>

            <div class="flex-bar">
                <ui-text-input label="Article Title" v-model="articleEdit.title"></ui-text-input>
                <ui-text-input label="Article URL" v-model="articleEdit.url"></ui-text-input>
                <ui-select-input label="Category" v-model="articleEdit.category" :options="articleCategoryOptions"></ui-select-input>
            </div>

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

        <button class="fab" @click="openArticleCreateDialog()">&#984085;</button>



        <ui-option-dialog ref="articleCreateDialog" @close="resetArticleCreate()">
            <template v-slot:heading>
                Create a new article
            </template>

            <template v-slot:inputs>
                <ui-text-input label="New Article Title" v-model="articleCreate.title"></ui-text-input>
            </template>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetArticleCreate()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button icon="&#984085;" :loading="articleCreate.loading" @click="createArticle()">Create Article</ui-button>
            </template>
        </ui-option-dialog>

        <ui-option-dialog ref="articleDeleteDialog" @close="resetArticleDelete()">
            <template v-slot:heading>
                Delete <b>{{articleDelete.title}}</b>
            </template>

            <span>
                Do you want to permanently delete your article<br>
                <b>{{articleDelete.title}}</b>?
            </span>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetArticleDelete()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button error icon="&#985721;" @click="deleteArticle()">Delete Now</ui-button>
            </template>
        </ui-option-dialog>
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
                    category: null,
                    punlishedAt: null,
                    loading: false,
                    autoSave: false,
                },

                articleDelete: {
                    id: null,
                    title: '',
                    loading: false,
                },

                editor: new Editor({
                    content: '',
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
            'articleEdit.title': function() { this.fireArticleEditUpdate() },
            'articleEdit.url': function() { this.fireArticleEditUpdate() },
            'articleEdit.introText': function() { this.fireArticleEditUpdate() },
            'articleEdit.introImage': function() { this.fireArticleEditUpdate() },
            'articleEdit.fullText': function() { this.fireArticleEditUpdate() },
        },

        computed: {
            user() {
                return this.$store.getters.user
            },

            authorProfile() {
                return this.$store.getters.authorProfile
            },

            authorArticles() {
                return this.$store.getters.authorArticles
            },

            articleCategories() {
                return this.$store.getters.articleCategories
            },

            articleCategoryOptions() {
                return this.articleCategories.map(e => ({'value': e.id, 'label': e.name}))
            },
        },

        methods: {
            openArticleCreateDialog() {
                this.$refs.articleCreateDialog.open()
            },

            resetArticleCreate() {
                this.articleCreate.title = ''
                this.$refs.articleCreateDialog.close()
            },

            createArticle() {
                this.articleCreate.loading = true
                
                axios.post('/auth/author/create-article', {
                    title: this.articleCreate.title,
                })
                .then(response => {
                    this.resetArticleCreate()
                    this.openArticleEditForm(response.data)
                    this.$store.commit('addAuthorArticle', response.data)
                    this.articleCreate.loading = false
                })
                .catch(error => {
                    console.log(error.response)
                    this.articleCreate.loading = false
                })
            },



            openArticleEditForm(article) {
                this.articleEdit.autoSave = false
                this.articleEdit.id = article.id
                this.articleEdit.url = article.url
                this.articleEdit.title = article.title
                this.articleEdit.introImage = article.intro_image
                this.articleEdit.introText = article.intro_text
                this.articleEdit.fullText = article.full_text
                this.editor.setContent(article.full_text)
                this.articleEdit.publishedAt = article.published_at
                this.articleEdit.category = article.category

                // Skip fireing update when inserting values programmatically
                this.$nextTick(() => {
                    this.articleEdit.autoSave = true
                })
            },

            fireArticleEditUpdate() {
                if (this.articleEdit.autoSave)
                {
                    this.articleEdit.loading = true
                    this.debouncedArticleSave()
                }
            },

            debouncedArticleSave: debounce(function() {
                this.saveArticle()
            }, 1000),

            saveArticle() {
                this.articleEdit.loading = true

                axios.post('/auth/author/update-article', {
                    articleId: this.articleEdit.id,
                    url: this.articleEdit.url,
                    title: this.articleEdit.title,
                    introImage: this.articleEdit.introImage,
                    introText: this.articleEdit.introText,
                    fullText: this.articleEdit.fullText,
                })
                .then(response => {
                    this.articleEdit.loading = false
                })
                .catch(error => {
                    console.log(error.response)
                    this.articleEdit.loading = false
                })
            },



            openArticleDeleteDialog(article) {
                this.articleDelete.id = article.id
                this.articleDelete.title = article.title
                this.$refs.articleDeleteDialog.open()
            },

            resetArticleDelete() {
                this.articleDelete.id = null
                this.articleDelete.title = ''
                this.$refs.articleDeleteDialog.close()
            },

            deleteArticle() {
                this.articleDelete.loading = true
                
                axios.post('/auth/author/delete-article', {
                    articleId: this.articleDelete.id,
                })
                .then(response => {
                    this.resetArticleDelete()
                    this.$store.commit('deleteAuthorArticle', response.data)
                    this.articleDelete.loading = false
                })
                .catch(error => {
                    console.log(error.response)
                    this.articleDelete.loading = false
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
            font-size: 0

            .author-profile-wrapper
                width: 350px
                padding: 15px
                padding-right: 0
                display: inline-block
        
                .background-image
                    height: 120px
                    width: 100%
                    background: var(--primary)
                    background-image: url('/images/static/assets/terrain_white.svg')
                    background-size: 800px
                    background-position: center
                    background-repeat: no-repeat
                    border-radius: 5px
                    display: block

                .profile-image
                    height: 140px
                    width: 140px
                    object-fit: cover
                    border-radius: 100%
                    margin: -70px auto 10px
                    display: block
                    padding: 5px
                    background: var(--bg)

                .text-wrapper
                    padding: 0 10px

                    .name
                        text-align: center
                        display: block
                        margin: 0

                    .biography
                        text-align: left
                        width: 100%
                        margin: 10px auto
                        font-size: var(--text-size)

            .author-article-list
                width: calc(100% - 350px)
                display: inline-block
                vertical-align: top
                padding: 15px

                .article
                    width: 100%
                    padding: 8px 5px 8px 17px
                    position: relative

                    .indicator
                        width: 4px
                        position: absolute
                        top: 50%
                        left: 0
                        height: 30px
                        border-radius: 5px
                        background: var(--text-gray)
                        transform: translateY(-50%)

                        &.published
                            background: var(--primary)

                    .button
                        vertical-align: middle

                    .article-info
                        width: calc(100% - 90px)
                        display: inline-block
                        vertical-align: middle

                        .title
                            color: var(--heading-gray)
                            margin: 0
                            line-height: 22px
                            font-size: var(--text-size)

                        .intro-text
                            max-width: 100%
                            margin: 0
                            font-size: 13px
                            line-height: 15px
                            white-space: nowrap
                            overflow: hidden
                            text-overflow: ellipsis

        .article-editor
            background: var(--bg)
            filter: var(--elevation-2)
            width: 100%
            border-radius: 7px
            padding: 15px
            display: flex
            flex-direction: column
            gap: 15px
            margin-top: 15px

            .flex-bar
                display: flex
                gap: 15px

                .spacer
                    flex: 1

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
                    border-bottom: var(--border)
                    flex-wrap: wrap
                    align-items: center
                    overflow: hidden

                    .group
                        display: flex
                        border-right: var(--border)

                    .menubar-button
                        display: grid
                        place-content: center
                        height: 40px
                        width: 50px
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

        .fab
            height: 56px
            width: 56px
            font-family: 'Material Icons'
            color: white
            background: var(--primary)
            display: grid
            place-content: center
            font-size: 24px
            position: fixed
            bottom: 30px
            right: 30px
            border-radius: 100%
            border: none
            filter: var(--elevation-2)
            cursor: pointer
            user-select: none
            transition: all 200ms
            z-index: 100

            &:hover
                filter: var(--elevation-4)
</style>