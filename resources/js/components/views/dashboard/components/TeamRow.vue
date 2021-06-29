<template>
    <div class="team-row-wrapper">
        <div class="team-header">
            <div class="name">
                <b>{{team.name}}</b>
            </div>

            <div class="description">
                <span v-if="team.description">{{team.description}}</span>
                <i v-else>No description</i>
            </div>

            <div class="tag-wrapper">
                <div class="tag">{{team.category}}</div>
            </div>

            <ui-popover-menu>
                <template v-slot:trigger>
                    <ui-icon-button class="more-button">&#983513;</ui-icon-button>
                </template>

                <ui-menu-item icon="&#984270;" :disabled="user.active_team_id === team.id" @click="$emit('setActiveTeam', team)">Select As Main Team</ui-menu-item>
                <ui-menu-item v-if="team.is_owner" icon="&#984043;" @click="$emit('edit', team)">Edit Team</ui-menu-item>
                <ui-menu-item v-if="team.is_owner" icon="&#983060;" @click="$emit('addMember', team)">Add Member</ui-menu-item>
                <ui-menu-item v-if="team.is_owner" icon="&#987309;" @click="$emit('addNamespace', team)">Add Namespace</ui-menu-item>
                <ui-menu-divider v-if="team.is_owner"></ui-menu-divider>
                <ui-menu-item v-if="team.is_owner" icon="&#985721;" @click="$emit('delete', team)">Delete Team</ui-menu-item>
                <ui-menu-item v-else icon="&#983558;" @click="$emit('leave', team)">Leave Team</ui-menu-item>
            </ui-popover-menu>

            <ui-icon-button class="expand-button" :class="{'expanded': expand}" @click="expand = !expand">&#983360;</ui-icon-button>
        </div>

        <div class="member-container" v-show="expand">
            <transition-group name="scale" class="block">
                <div class="member" :class="{'read-only': !team.is_owner}" v-for="member in team.members" :key="member.id">
                    <div class="icon">person</div>

                    <b class="text" v-if="member.user.firstname || member.user.lastname">
                        {{member.user.firstname}}
                        {{member.user.lastname}}
                        <span v-if="member.user_id === user.id">(you)</span>
                    </b>
                    <b class="text" v-else>
                        {{member.user.username}}
                        <span v-if="member.user_id === user.id">(you)</span>
                    </b>

                    <div class="role">{{member.roles.join(', ')}}</div>

                    <div class="icon-button-wrapper" v-if="team.is_owner">
                        <!-- <button class="icon-button">edit</button> -->
                        <button class="icon-button" @click="$emit('deleteMember', {team, member})">delete</button>
                    </div>
                </div>

                <div class="member add-button" key="add-member" v-if="team.is_owner" @click="$emit('addMember', team)">
                    <div class="icon">add</div>
                    <div class="text">Add Member</div>
                </div>
            </transition-group>
        </div>

        <!-- <div class="team-content" v-show="expand">
            <div class="member-row" v-for="member in team.members" :key="member.id">
                <img src="/images/defaults/default_profile_image.svg" class="profile-image">

                

                <div class="role" v-for="(role, i) in member.roles" :key="i" :class="[{'owner': role == 'owner'}]">{{role}}</div>    

                <ui-popover-menu class="more-button" v-if="team.is_owner">
                    <template v-slot:trigger>
                        <ui-icon-button>&#983513;</ui-icon-button>
                    </template>

                    <ui-menu-item icon="&#984043;" disabled>Edit Member (WIP)</ui-menu-item>
                    <ui-menu-item icon="&#983213;" @click="$emit('deleteMember', {team, member})">Remove Member</ui-menu-item>
                </ui-popover-menu>
            </div>

            <div class="centerer" >
                <ui-button icon="&#983060;" text @click="$emit('addMember', team)">Add Member</ui-button>
            </div>
        </div> -->
    </div>
</template>

<script>
    export default {
        props: {
            team: Object,
        },

        data() {
            return {
                expand: false,
            }
        },

        computed: {
            user() {
                return this.$store.getters.user
            },
        },

        methods: {

        },
    }
</script>

<style lang="sass" scoped>
    .team-row-wrapper
        width: 100%
        display: flex
        flex-direction: column
        transition: all 300ms
        border-bottom: var(--border)

        &:last-of-type
            border: none

        &.slide-enter
            transform: translateY(-100px)
            opacity: 0

        &.slide-leave-to
            transform: scale(0)
            opacity: 0

        &.slide-leave-active
            position: absolute

        .team-header
            width: 100%
            display: flex
            align-items: center
            padding: 10px 15px
            gap: 10px

            .active-team-button
                color: var(--text-gray)
                background: none

                &:hover
                    color: var(--heading-gray)
                    background: none

                &.active
                    color: var(--warning) !important
                    background: none

            .tag-wrapper
                flex: 1

                .tag
                    font-size: 10px
                    color: var(--primary)
                    background: var(--primary-shade)
                    padding: 1px 8px
                    line-height: 18px
                    border-radius: 30px
                    letter-spacing: 1px
                    font-weight: 600
                    text-transform: uppercase
                    user-select: none
                    vertical-align: top

            .name
                flex: 2
                text-transform: uppercase
                font-weight: 600
                color: var(--heading-gray)

            .description
                flex: 5
                line-height: 20px

            .more-button
                margin: 0
            
            .expand-button
                transition: transform 200ms

                &.expanded
                    transform: rotate(180deg)

        .member-container
            width: 100%
            padding: 5px 7.5px 0
            font-size: 0

            .member
                width: calc(25% - 15px)
                height: 170px
                margin: 0 7.5px 15px
                display: inline-grid
                place-content: center
                position: relative
                border-radius: 7px
                user-select: none
                color: var(--bg)
                background: var(--primary)
                text-align: center
                vertical-align: top
                filter: var(--elevation-1)
                overflow: hidden
                transition: all 300ms, padding 100ms
                padding: 15px
                padding-top: 35px

                &.scale-enter,
                &.scale-leave-to
                    transform: scale(0)
                    opacity: 0

                &.scale-leave-active
                    position: absolute

                &.add-button
                    padding: 15px !important
                    cursor: pointer
                    transition: all 300ms, filter 100ms
                    color: var(--heading-gray)
                    background: var(--bg)

                    &:hover
                        filter: var(--elevation-4)

                &.read-only
                    padding: 15px !important
                    padding-top: 35px !important

                .icon
                    font-size: 35px
                    color: inherit
                    font-family: 'Material Icons Round'
                    user-select: none
                    margin-bottom: 20px
                    opacity: 0.7

                .text
                    flex: 1
                    font-size: 13px
                    letter-spacing: 1px
                    color: inherit
                    text-transform: uppercase
                    font-weight: 600

                .icon-button-wrapper
                    position: absolute
                    bottom: 0
                    left: 0
                    transform: translateY(40px)
                    background: var(--bg)
                    width: 100%
                    height: 40px
                    display: flex
                    align-items: center
                    transition: transform 100ms

                    .icon-button
                        flex: 1
                        background: none
                        display: grid
                        place-content: center
                        cursor: pointer
                        user-select: none
                        height: 100%
                        font-size: 20px
                        border: none
                        font-family: 'Material Icons Round'
                        color: var(--text-gray)
                        transition: all 100ms

                        &:hover
                            color: var(--primary)
                            background: var(--primary-shade)

                &:hover
                    padding-bottom: 40px

                    .icon-button-wrapper
                        transform: translateY(0)

                .role
                    line-height: 30px
                    font-size: 10px
                    letter-spacing: 1px
                    font-weight: 700
                    color: var(--bg)
                    text-align: left
                    padding: 0 10px
                    width: 100%
                    position: absolute
                    top: 0
                    left: 0
                    white-space: nowrap
                    text-transform: uppercase
                    user-select: none
</style>