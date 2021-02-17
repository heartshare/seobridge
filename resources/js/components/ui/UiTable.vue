<template>
    <div class="table">
        <div class="title-row" :style="'background: '+(options.titleBar.background ? options.titleBar.background : 'var(--bg-dark)')" v-if="options.titleBar">
            <div class="title" :style="'color: '+(options.titleBar.color ? options.titleBar.color : 'var(--heading-gray)')">{{options.titleBar.title}}</div>
        </div>



        <div class="settings-row" v-if="options.settings">
            <ui-select-input class="limit-input" v-model="limit" :options="[20, 50, 100]"></ui-select-input>
            <ui-text-input class="search-bar" label="Search"></ui-text-input>
        </div>
        


        <div class="scroll-row">
            <div class="header-row" v-show="content.length > 0">
                <div class="column" :style="'width:' + column.width + 'px'" @click="setSortColumn(column)" v-for="(column, j) in options_.columns" :key="j">
                    <div class="text" :title="column.name">{{column.name}}</div>
                    <div class="sort-indicator" v-if="column.sort !== false" v-show="sort.column === column.key">
                        {{sort.asc ? '&#62652;' : '&#62653;'}}
                    </div>
                    <div class="handle" v-if="column.handle !== false" @click.stop @mousedown.stop="dragDown($event, column.key)"></div>
                </div>
            </div>



            <div class="content-row" v-show="content.length > 0" v-for="(row, i) in content" :key="i">
                <div class="column" :style="'width:' + column.width + 'px'" v-for="(column, j) in options.columns" :key="j">
                    
                    <!-- DATE -->
                    <div class="text" :class="{'bold': column.bold}" v-if="column.format === 'date'" :title="new Date(R(row, column.key)).toLocaleString(undefined, column.dateFormat)">
                        {{new Date(R(row, column.key)).toLocaleString(undefined, column.dateFormat)}}
                    </div>
                    
                    <!-- CURRENCY -->
                    <div class="text" :class="{'bold': column.bold}" v-else-if="column.format === 'currency'" :title="new Intl.NumberFormat('en-US', { style: 'currency', currency: column.currencyColumn ? R(row, column.currencyColumn) : column.currency, minimumFractionDigits: 2, maximumFractionDigits: 2}).format(R(row, column.key) / 100)">
                        {{new Intl.NumberFormat('en-US', { style: 'currency', currency: column.currencyColumn ? R(row, column.currencyColumn) : column.currency, minimumFractionDigits: 2, maximumFractionDigits: 2}).format(R(row, column.key) / 100)}}
                    </div>
                    
                    <!-- ICON BUTTONS -->
                    <div class="icon-buttons" v-else-if="column.format === 'icon-buttons'">
                        <ui-icon-button v-for="(button, j) in column.buttons" :key="'icon_button_'+i+'_'+j" :type="button.type" :title="button.title" @click="$emit(button.returnEvent, R(row, button.returnColumn))" v-html="button.icon"></ui-icon-button>
                    </div>
                    
                    <!-- BREADCRUMBS -->
                    <div class="breadcrumb" :class="column.breadcrumbType" v-else-if="column.format === 'breadcrumbs'" :title="R(row, column.key)">
                        <div class="icon" v-if="column.breadcrumbIcon" v-html="column.breadcrumbIcon"></div>
                        {{R(row, column.key)}}
                    </div>
                    
                    <!-- CIRCLE IMAGE -->
                    <img class="circle-image" v-else-if="column.format === 'circle-image'" :src="R(row, column.key)">

                    <!-- OTHER -->
                    <div class="text" :class="{'bold': column.bold}" v-else :title="R(row, column.key)">
                        {{R(row, column.key)}}
                    </div>
                </div>
            </div>



            <div class="placeholder" v-show="content.length == 0">
                {{options.placeholder}}
            </div>

            <div class="horizontal-spacer" :style="'width:' + verticalPlaceholderWidth + 'px'"></div>
        </div>



        <div class="pagination-row" v-if="options.pagination">
            <div class="page-pre-container">
                <ui-button class="page-button" light :class="{'disabled': button.page === null}" @click="setPagination(button.page)" v-for="(button, i) in paginationButtonsPre" :key="i" icon="none">{{button.label}}</ui-button>
            </div>
            <ui-number-input class="page-number-input" v-model="pageNumber" :min="1" :max="pageCount"></ui-number-input>
            <div class="page-post-container">
                <ui-button class="page-button" light :class="{'disabled': button.page === null}" @click="setPagination(button.page)" v-for="(button, i) in paginationButtonsPost" :key="i" icon="none">{{button.label}}</ui-button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            content: Array,
            options: Object,
        },

        data() {
            return {
                pageNumber: 6,
                pageCount: 30,
                pageButtonCount: 5,
                limit: 20,
                verticalPlaceholderWidth: 0,

                sort: {
                    column: null,
                    asc: true,
                },

                options_: {
                    columns: [],
                },

                drag: {
                    column: null,
                    state: false,
                    startPoint: {x:0, y:0},
                    startWidth: 0,
                }
            }
        },

        mounted() {
            this.options_ = this.options

            this.computeVerticalPlaceholderWidth()

            window.addEventListener('mouseup', (event) => {
                this.dragUp()
            })

            window.addEventListener('mousemove', (event) => {
                this.dragMove(event)
            })
        },

        computed: {
            paginationButtonsPre() {
                let pre = []

                this.pageNumber // = 4

                let iterate = this.pageButtonCount < this.pageNumber - 1 ? 3 : 5

                for (let i = ((this.pageNumber - iterate > 0) ? this.pageNumber - iterate : 1); i < this.pageNumber; i++)
                {
                    // if (i <= this.pageCount - this.pageNumber)
                    // {                  
                    //     }
                    pre.push({page: i, label: i + ''})
                }

                if (iterate === 3)
                {
                    pre.unshift({page: null, label: '…'})
                    pre.unshift({page: 1, label: 1 + ''}) 
                }

                return pre
            },

            paginationButtonsPost() {
                let post = []

                let iterate = this.pageCount - this.pageNumber > this.pageButtonCount ? 3 : 5

                for (let i = this.pageNumber + 1; i < (this.pageNumber + iterate + 1); i++)
                {
                    if (i <= this.pageCount)
                    {
                        post.push({page: i, label: i + ''})                    
                    }
                }

                if (iterate === 3)
                {
                    post.push({page: null, label: '…'})
                    post.push({page: this.pageCount, label: this.pageCount + ''})
                }

                return post
            }
        },

        methods: {
            setPagination(page) {
                this.pageNumber = page
            },

            setSortColumn(column) {
                if (column.sort === false) return

                if (this.sort.column === column.key)
                {
                    if (this.sort.asc === true)
                    {
                        this.sort.asc = false
                    }
                    else
                    {
                        this.sort.column = null
                        this.sort.asc = false
                    }
                }
                else
                {
                    this.sort.column = column.key
                    this.sort.asc = true
                }
            },

            dragDown(event, columnKey) {
                let column = this.options_.columns.find(e => e.key === columnKey)

                if (!column) return

                this.drag.startPoint = {x: event.clientX, y: event.clientY}
                this.drag.column = columnKey
                this.drag.startWidth = column.width
                this.drag.state = true
            },

            dragMove(event) {
                if (!this.drag.state) return

                this.options_.columns.find(e => e.key === this.drag.column).width = Math.max(this.drag.startWidth + event.clientX - this.drag.startPoint.x, 50)
            },

            dragUp() {
                this.drag.state = false
                this.drag.column = null
                this.computeVerticalPlaceholderWidth()
            },

            computeVerticalPlaceholderWidth() {
                this.verticalPlaceholderWidth = this.options_.columns.map(e => e.width).reduce((b, a) => a + b, 0) + 5
            },

            // TODO: CHECK SECURITY
            R(row, selector) {
                if (/[^a-zA-Z0-9\._\[\]]/g.test(selector))
                {
                    console.log('ERROR ERROR HACKER ATTACK!!!')
                    return null
                }
                
                return eval('row.'+selector)
            },
        },
    }
</script>

<style lang="sass" scoped>
    .table
        width: 100%
        margin: 20px 0 0
        background: var(--bg)
        border-radius: 7px
        overflow: hidden
        overflow-x: auto
        filter: drop-shadow(0 1px 1px #00000037)

        .title-row
            width: 100%
            padding: 15px

            .title
                height: 20px
                line-height: 18px
                font-size: 15px
                font-family: sofia-pro
                font-weight: 700
                text-transform: uppercase

        .settings-row
            width: 100%
            padding: 10px 15px
            background: var(--bg-dark)

            .search-bar
                max-width: 300px
                vertical-align: top
                float: right

            .limit-input
                width: 80px
                vertical-align: top

        .scroll-row
            width: 100%
            max-width: 100%
            overflow: auto
            position: relative

            .horizontal-spacer
                display: block
                width: 1px
                height: 1px

            .column
                white-space: nowrap
                min-height: 50px

                .text
                    width: 100%
                    font-size: var(--text-size)
                    overflow: hidden
                    white-space: nowrap
                    text-overflow: ellipsis
                    height: 50px
                    line-height: 23px
                    padding: 15px

                    &.bold
                        font-weight: bold

                .icon-buttons
                    padding: 5px
                    gap: 5px
                    display: flex

                .circle-image
                    height: 36px
                    width: 36px
                    border-radius: 40px
                    object-fit: cover
                    margin: 7px

                .breadcrumb
                    margin: 10px 15px
                    height: 30px
                    line-height: 33px
                    border-radius: 30px
                    background: var(--bg-dark)
                    color: var(--heading-gray)
                    font-size: 15px
                    padding: 0 15px

                    &.success
                        color: var(--success)
                        background: var(--success-shade)

                    &.warning
                        color: var(--warning)
                        background: var(--warning-shade)

                    &.error
                        color: var(--error)
                        background: var(--error-shade)

                    &.info
                        color: var(--primary)
                        background: var(--primary-shade)

                    .icon
                        font-family: 'Material Icons'
                        font-size: 18px
                        line-height: 30px
                        height: 30px
                        width: 30px
                        text-align: center
                        vertical-align: top
                        margin-left: -15px

            .header-row
                min-width: 100%
                user-select: none
                color: var(--text-gray)
                text-transform: uppercase
                white-space: nowrap
            
                .column
                    font-weight: bold
                    display: inline-flex
                    position: relative
                    cursor: pointer
                    vertical-align: top

                    .sort-indicator
                        font-size: 20px
                        font-family: 'Material Icons'
                        color: var(--text-gray)
                        height: 50px
                        width: 30px
                        text-align: center
                        line-height: 50px

                    .text
                        font-size: var(--text-size)
                        line-height: 23px
                        height: 50px
                        padding: 15px
                        padding-right: 5px
                        text-align: left
                        flex: 1

                    .handle
                        position: absolute
                        top: 5px
                        right: -4.5px
                        right: 0
                        height: calc(100% - 10px)
                        width: 8px
                        border-radius: 6px
                        // background: transparent
                        background: var(--text-gray)
                        opacity: 0.1
                        cursor: pointer
                        z-index: 1

                        &:hover
                            opacity: 1

            .content-row
                min-width: 100%
                white-space: nowrap

                .column
                    vertical-align: top

            .placeholder
                font-size: var(--text-size)
                line-height: 150%
                height: 140px
                display: grid
                wisth: 100%
                padding-top: 3px
                place-content: center

        .pagination-row
            width: 100%
            display: grid
            padding: 10px 15px
            background: var(--bg-dark)
            grid-template: 40px / 1fr 60px 1fr
            gap: 5px

            .page-pre-container
                justify-content: flex-end
                display: flex
                gap: 5px

            .page-post-container
                justify-content: flex-start
                display: flex
                gap: 5px

            .page-number-input
                --height: 40px

            .page-button
                background: transparent
                min-width: 40px
                height: 40px
                border-radius: 50px
                padding-right: 0
                padding-left: 0
                color: var(--primary)
            
                &.disabled
                    cursor: auto
                    color: var(--text-gray)

                &:hover
                    box-shadow: none
                    
                    &:not(.disabled)
                        background: var(--primary-shade)
                        color: var(--primary)
</style>