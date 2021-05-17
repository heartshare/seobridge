<template>
    <div class="page-container">
        <div class="page-header">
            <div class="limiter">
                <div class="page-header-wrapper">
                    <div class="row">
                        <h1>My Team</h1>
                        <div class="spacer"></div>
                        <ui-icon-button class="icon-button" @click="openTeamEditor()">&#984085;</ui-icon-button>
                    </div>
                </div>
            </div>
        </div>
        <div class="limiter overlap">
            <div class="sheet">
                <ui-tabs-header :tabs="{'overview': 'Overview', 'teams': 'Team List', 'invites': 'Invites'}" v-model="tab"></ui-tabs-header>

                <div class="block" v-show="tab === 'overview'" v-if="activeTeam">
                    <div class="active-team-wrapper">
                        <h2>{{activeTeam.name}}</h2>
                        <span v-if="activeTeam.description">{{activeTeam.description}}</span>
                    </div>

                    <div class="block">
                        <div class="namespace-container">
                            <div class="namespace" :style="`background: ${stc(site.host)}; color: ${contrast(stc(site.host))};`" v-for="site in activeTeam.sites" :key="site.id">
                                <div class="icon">language</div>

                                <b class="text">{{site.host}}</b>

                                <div class="icon-button-wrapper">
                                    <!-- <button class="icon-button">edit</button> -->
                                    <button class="icon-button" @click="openTeamSiteDeleteDialog(activeTeam, site)">delete</button>
                                </div>
                            </div>

                            <div class="namespace add-button" v-if="activeTeam.is_owner" @click="openTeamSiteCreateDialog(activeTeam)">
                                <div class="icon">add</div>
                                <div class="text">Add namespace</div>
                            </div>
                        </div>

                        <div class="placeholder grid-centered" v-if="activeTeam.sites.length === 0 && !activeTeam.is_owner">
                            <p>Your team doesn't have any Site Namespaces added yet</p>
                        </div>
                    </div>
                </div>

                <transition-group name="slide" class="block" v-show="tab === 'teams'">
                    <div class="team-wrapper" v-for="team in teams" :key="team.id">
                        <div class="team-header">
                            <div class="title">
                                <b>{{team.name}}</b><br>
                                <span v-if="team.description">{{team.description}}</span>
                                <i v-else>No Description</i>
                            </div>

                            <div class="tag">{{team.category}}</div>

                            <ui-popover-menu>
                                <template v-slot:trigger>
                                    <ui-icon-button class="more-button">&#983513;</ui-icon-button>
                                </template>

                                <ui-menu-item icon="&#984270;" :disabled="user.active_team_id === team.id" @click="setActiveTeamId(team)">Select As Main Team</ui-menu-item>
                                <ui-menu-item v-if="team.is_owner" icon="&#984043;" @click="openTeamEditor(team)">Edit Team</ui-menu-item>
                                <ui-menu-item v-if="team.is_owner" icon="&#983060;" @click="openTeamInviteDialog(team)">Add Member</ui-menu-item>
                                <ui-menu-item v-if="team.is_owner" icon="&#987309;" @click="openTeamSiteCreateDialog(team)">Add Namespace</ui-menu-item>
                                <ui-menu-divider v-if="team.is_owner"></ui-menu-divider>
                                <ui-menu-item v-if="team.is_owner" icon="&#985721;" @click="openTeamDeleteDialog(team)">Delete Team</ui-menu-item>
                                <ui-menu-item v-else icon="&#983558;" @click="openTeamLeaveDialog(team)">Leave Team</ui-menu-item>
                            </ui-popover-menu>

                            <ui-icon-button class="expand-button" :class="{'expanded': team.id === openedTeamSheet}" @click="openedTeamSheet = team.id">&#983360;</ui-icon-button>

                            <ui-button style="margin: 0 10px 0 5px" small v-if="!activeTeam" @click="setActiveTeamId(team)">Join</ui-button>
                        </div>

                        <div class="team-content" v-show="team.id === openedTeamSheet">
                            <div class="member-row" v-for="member in team.members" :key="member.id">
                                <img src="/images/defaults/default_profile_image.svg" class="profile-image">

                                <div class="name" v-if="member.user.firstname || member.user.lastname">
                                    {{member.user.firstname}}
                                    {{member.user.lastname}}
                                    <span v-if="member.user_id === user.id">(you)</span>
                                </div>
                                <div class="name" v-else>
                                    {{member.user.username}}
                                    <span v-if="member.user_id === user.id">(you)</span>
                                </div>

                                <div class="role" v-for="(role, i) in member.roles" :key="i" :class="[{'owner': role == 'owner'}]">{{role}}</div>    

                                <ui-popover-menu class="more-button" v-if="team.is_owner">
                                    <template v-slot:trigger>
                                        <ui-icon-button>&#983513;</ui-icon-button>
                                    </template>

                                    <ui-menu-item icon="&#984043;" disabled>Edit Member (WIP)</ui-menu-item>
                                    <ui-menu-item icon="&#983213;" @click="openMemberDeleteDialog(team, member)">Remove Member</ui-menu-item>
                                </ui-popover-menu>
                            </div>

                            <div class="centerer" v-if="team.is_owner">
                                <ui-button icon="&#983060;" text @click="openTeamInviteDialog(team)">Add Member</ui-button>
                            </div>
                        </div>
                    </div>
                </transition-group>

                <div class="invite-wrapper" v-show="tab === 'invites'" v-if="invites.length > 0">
                    <div class="invite" v-for="invite in invites" :key="invite.id">
                        <span>You're invited to join <b>{{invite.team.name}}</b></span>
                        <div class="spacer"></div>
                        <ui-button text border icon="&#983213;" icon-left @click="openInviteIgnoreDialog(invite.id, invite.team)">Decline</ui-button>
                        <ui-button icon="&#983340;" @click="handleInvite(invite.id, 'accepted')">Accept</ui-button>
                    </div>
                </div>



                <div class="placeholder grid-centered" v-show="['overview', 'teams'].includes(tab)" v-if="!activeTeam && teams.length > 0">
                    <p>
                        You don't have an active team.<br>
                        Join one to get started!
                    </p>
                </div>

                <div class="placeholder grid-centered" v-show="['overview', 'teams'].includes(tab)" v-if="teams.length === 0">
                    <p>Create a team to get started.</p>
                    <ui-button @click="openTeamEditor()">Create a team</ui-button>
                </div>

                <div class="placeholder grid-centered" v-show="tab === 'invites'" v-if="invites.length === 0">
                    <p>You don't have any pending invites.</p>
                </div>
            </div>
        </div>



        <ui-option-dialog ref="teamEditDialog" @close="resetTeamEditor()">
            <template v-slot:heading>
                Create or edit your team
            </template>

            <template v-slot:inputs>
                <ui-text-input label="Team name" v-model="teamEdit.name"></ui-text-input>
                <ui-select-input label="Team category" v-model="teamEdit.category" :options="teamCategories"></ui-select-input>
                <ui-textarea label="Team description" class="team-description-input" :max="1000" show-max v-model="teamEdit.description"></ui-textarea>
            </template>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetTeamEditor()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button icon="&#983443;" @click="saveTeam()">Save</ui-button>
            </template>
        </ui-option-dialog>

        <ui-option-dialog ref="teamSiteCreateDialog" @close="resetTeamSiteCreate()">
            <template v-slot:heading>
                Create a Site Namespace
            </template>

            <template v-slot:inputs>
                <ui-text-input label="Domain" v-model="teamSiteCreate.host"></ui-text-input>
            </template>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetTeamSiteCreate()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button icon="&#984085;" @click="createTeamSite()">Add Namespace</ui-button>
            </template>
        </ui-option-dialog>

        <ui-option-dialog ref="teamSiteDeleteDialog" @close="resetTeamSiteDelete()">
            <template v-slot:heading>
                Delete <b>{{teamSiteDelete.host}}</b> from {{teamSiteDelete.name}}
            </template>

            <span>
                Do you want to delete <b>{{teamSiteDelete.host}}</b> from {{teamSiteDelete.name}}?
            </span>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetTeamSiteDelete()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button error icon="&#985721;" :loading="teamSiteDelete.loading" @click="deleteTeamSite()">Delete Now</ui-button>
            </template>
        </ui-option-dialog>

        <ui-option-dialog ref="teamDeleteDialog" @close="resetTeamDelete()">
            <template v-slot:heading>
                Delete: <b>{{teamDelete.name}}</b>
            </template>

            <span>
                Do you want to permanently delete your team: <b>{{teamDelete.name}}</b>?
            </span>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetTeamDelete()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button error icon="&#985721;" @click="deleteTeam()">Delete Now</ui-button>
            </template>
        </ui-option-dialog>

        <ui-option-dialog ref="teamLeaveDialog" @close="resetTeamLeave()">
            <template v-slot:heading>
                Leave <b>{{teamLeave.name}}</b>
            </template>

            <span>
                Do you want to leave <b>{{teamLeave.name}}</b>?
            </span>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetTeamLeave()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button error icon="&#983558;" @click="leaveTeam()">Leave Now</ui-button>
            </template>
        </ui-option-dialog>

        <ui-option-dialog ref="inviteIgnoreDialog" @close="resetInviteIgnore()">
            <template v-slot:heading>
                Decline invite to <b>{{inviteIgnore.name}}</b>
            </template>

            <span>
                Do you want to decline your invite to <b>{{inviteIgnore.name}}</b>?
            </span>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetInviteIgnore()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button icon="&#983213;" @click="handleInvite(inviteIgnore.id, 'ignored')">Decline</ui-button>
            </template>
        </ui-option-dialog>

        <ui-option-dialog ref="memberDeleteDialog" @close="resetMemberDelete()">
            <template v-slot:heading>
                Remove <b>{{memberDelete.memberName}}</b> from {{memberDelete.teamName}}
            </template>

            <span>
                Do you want to remove <b>{{memberDelete.memberName}}</b> from {{memberDelete.teamName}}?
            </span>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetMemberDelete()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button error icon="&#983213;" @click="deleteMember()">Remove</ui-button>
            </template>
        </ui-option-dialog>

        <ui-option-dialog ref="teamInviteDialog" @close="resetTeamInvite()">
            <template v-slot:heading>
                Add team member to: <b>{{teamInvite.name}}</b>
            </template>

            <template v-slot:inputs>
                <ui-text-input label="Invite via email" v-model="teamInvite.inviteName"></ui-text-input>
            </template>

            <template v-slot:button-1>
                <ui-button text border icon-left icon="&#983382;" @click="resetTeamInvite()">Cancel</ui-button>
            </template>
            <template v-slot:button-2>
                <ui-button icon="&#984202;" @click="sendTeamInvitation()">Send Invitation</ui-button>
            </template>
        </ui-option-dialog>
    </div>
</template>

<script>
    import invert from 'invert-color'

    export default {
        data() {
            return {
                teamCategories: [
                    {'technology': 'Technology'},
                    {'marketing': 'Marketing'},
                    {'other': 'Other'},
                ],

                openedTeamSheet: '',

                tab: 'overview',

                teamEdit: {
                    id: null,
                    name: '',
                    description: '',
                    category: '',
                    loading: false,
                },

                teamSiteCreate: {
                    id: null,
                    name: '',
                    host: '',
                    loading: false,
                },

                teamSiteDelete: {
                    id: null,
                    name: '',
                    siteId: null,
                    host: '',
                    loading: false,
                },

                teamInvite: {
                    id: null,
                    name: '',
                    inviteName: '',
                    loading: false,
                },

                inviteIgnore: {
                    id: null,
                    name: '',
                    loading: false,
                },

                teamDelete: {
                    name: '',
                    id: null,
                    loading: false,
                },

                teamLeave: {
                    name: '',
                    id: null,
                    loading: false,
                },

                memberDelete: {
                    memberId: null,
                    memberName: '',
                    teamId: null,
                    teamName: '',
                    loading: false,
                },
            }
        },

        computed: {
            user() {
                return this.$store.getters.user
            },

            teams() {
                return this.$store.getters.teams
            },

            activeTeam() {
                let activeTeam = this.teams.find(e => e.id === this.user.active_team_id)
                return activeTeam || false
            },

            invites() {
                return this.$store.getters.invites
            },
        },

        methods: {
            openTeamEditor(team = null) {
                if (team)
                {
                    this.teamEdit.id = team.id
                    this.teamEdit.name = team.name
                    this.teamEdit.description = team.description
                    this.teamEdit.category = team.category
                }
                else
                {
                    this.resetTeamEditor()
                }

                this.$refs.teamEditDialog.open()
            },

            resetTeamEditor() {
                this.teamEdit.id = null
                this.teamEdit.name = ''
                this.teamEdit.description = ''
                this.teamEdit.category = ''
                this.$refs.teamEditDialog.close()
            },

            saveTeam() {
                this.teamEdit.loading = true
                
                let route = this.teamEdit.id ? '/auth/team/update-team' : '/auth/team/create-team'
                
                axios.post(route, {
                    teamId: this.teamEdit.id,
                    name: this.teamEdit.name,
                    category: this.teamEdit.category,
                    description: this.teamEdit.description,
                })
                .then(response => {
                    this.$store.commit('setTeam', response.data)
                    this.teamEdit.loading = false
                    this.resetTeamEditor()
                })
                .catch(error => {
                    console.log(error.response)
                    this.teamEdit.loading = false
                })
            },



            openTeamSiteCreateDialog(team) {
                this.teamSiteCreate.id = team.id
                this.teamSiteCreate.name = team.name
                this.$refs.teamSiteCreateDialog.open()
            },

            resetTeamSiteCreate() {
                this.teamSiteCreate.id = null
                this.teamSiteCreate.name = ''
                this.teamSiteCreate.host = ''
                this.$refs.teamSiteCreateDialog.close()
            },

            createTeamSite() {
                this.teamSiteCreate.loading = true
                
                axios.post('/auth/team/create-team-site', {
                    teamId: this.teamSiteCreate.id,
                    host: this.teamSiteCreate.host,
                })
                .then(response => {
                    // this.$store.commit('deleteTeam', response.data)
                    this.teamSiteCreate.loading = false
                    this.resetTeamSiteCreate()
                })
                .catch(error => {
                    console.log(error.response)
                    this.teamSiteCreate.loading = false
                })
            },



            openTeamSiteDeleteDialog(team, site) {
                this.teamSiteDelete.id = team.id
                this.teamSiteDelete.name = team.name
                this.teamSiteDelete.siteId = site.id
                this.teamSiteDelete.host = site.host
                this.$refs.teamSiteDeleteDialog.open()
            },

            resetTeamSiteDelete() {
                this.teamSiteDelete.id = null
                this.teamSiteDelete.name = ''
                this.teamSiteDelete.siteId = null
                this.teamSiteDelete.host = ''
                this.$refs.teamSiteDeleteDialog.close()
            },

            deleteTeamSite() {
                this.teamSiteDelete.loading = true
                
                axios.post('/auth/team/delete-team-site', {
                    teamId: this.teamSiteDelete.id,
                    siteId: this.teamSiteDelete.siteId,
                })
                .then(response => {
                    this.teamSiteDelete.loading = false
                    this.resetTeamSiteDelete()
                })
                .catch(error => {
                    console.log(error.response)
                    this.teamSiteDelete.loading = false
                })
            },



            openTeamDeleteDialog(team) {
                this.teamDelete.id = team.id
                this.teamDelete.name = team.name
                this.$refs.teamDeleteDialog.open()
            },

            resetTeamDelete() {
                this.teamDelete.id = null
                this.teamDelete.name = ''
                this.$refs.teamDeleteDialog.close()
            },

            deleteTeam() {
                this.teamDelete.loading = true
                
                axios.post('/auth/team/delete-team', {
                    teamId: this.teamDelete.id,
                    name: this.teamDelete.name,
                })
                .then(response => {
                    this.$store.commit('deleteTeam', response.data)
                    this.teamDelete.loading = false
                    this.resetTeamDelete()
                })
                .catch(error => {
                    console.log(error.response)
                    this.teamDelete.loading = false
                })
            },



            openTeamLeaveDialog(team) {
                this.teamLeave.id = team.id
                this.teamLeave.name = team.name
                this.$refs.teamLeaveDialog.open()
            },

            resetTeamLeave() {
                this.teamLeave.id = null
                this.teamLeave.name = ''
                this.$refs.teamLeaveDialog.close()
            },

            leaveTeam() {
                this.teamLeave.loading = true
                
                axios.post('/auth/team/leave-team', {
                    teamId: this.teamLeave.id,
                })
                .then(response => {
                    // deleteTeam is applicable because it only removes the team locally
                    this.$store.commit('deleteTeam', response.data)
                    this.teamLeave.loading = false
                    this.resetTeamLeave()
                })
                .catch(error => {
                    console.log(error.response)
                    this.teamLeave.loading = false
                })
            },



            openMemberDeleteDialog(team, member) {
                this.memberDelete.teamId = team.id
                this.memberDelete.teamName = team.name
                this.memberDelete.memberId = member.id
                this.memberDelete.memberName = member.user.username
                this.$refs.memberDeleteDialog.open()
            },

            resetMemberDelete() {
                this.memberDelete.teamId = null
                this.memberDelete.teamName = ''
                this.memberDelete.memberId = null
                this.memberDelete.memberName = ''
                this.$refs.memberDeleteDialog.close()
            },
            
            deleteMember() {
                this.memberDelete.loading = true
                
                axios.post('/auth/team/delete-member', {
                    teamId: this.memberDelete.teamId,
                    memberId: this.memberDelete.memberId,
                })
                .then(response => {
                    // this.$store.commit('deleteTeam', response.data)
                    this.memberDelete.loading = false
                    this.resetMemberDelete()
                })
                .catch(error => {
                    console.log(error.response)
                    this.memberDelete.loading = false
                })
            },



            openTeamInviteDialog(team) {
                this.teamInvite.id = team.id
                this.teamInvite.name = team.name
                this.$refs.teamInviteDialog.open()
            },

            resetTeamInvite() {
                this.teamInvite.id = null
                this.teamInvite.name = ''
                this.teamInvite.inviteName = ''
                this.$refs.teamInviteDialog.close()
            },

            sendTeamInvitation() {
                this.teamInvite.loading = true
                
                axios.post('/auth/team/create-invite', {
                    teamId: this.teamInvite.id,
                    inviteName: this.teamInvite.inviteName,
                })
                .then(response => {
                    console.log(response.data)
                    this.teamInvite.loading = false
                    this.resetTeamInvite()
                })
                .catch(error => {
                    console.log(error.response)
                    this.teamInvite.loading = false
                })
            },



            setActiveTeamId(team) {
                // this.teamDelete.loading = true
                
                axios.post('/auth/team/set-active-team-id', {
                    teamId: team.id,
                })
                .then(response => {
                    this.$store.commit('userInfo', {active_team_id: response.data})
                    // this.teamDelete.loading = false
                    // this.resetTeamDelete()
                })
                .catch(error => {
                    console.log(error.response)
                    // this.teamDelete.loading = false
                })
            },



            openInviteIgnoreDialog(inviteId, team) {
                this.inviteIgnore.id = inviteId
                this.inviteIgnore.name = team.name
                this.$refs.inviteIgnoreDialog.open()
            },

            resetInviteIgnore() {
                this.inviteIgnore.id = null
                this.inviteIgnore.name = ''
                this.$refs.inviteIgnoreDialog.close()
            },

            handleInvite(id, action) {
                axios.post('/auth/team/handle-invite', {
                    inviteId: id,
                    action
                })
                .then(response => {
                    if (action === 'accepted') this.$store.commit('setTeam', response.data)
                    else this.resetInviteIgnore()

                    this.$store.dispatch('fetchAllInvites')
                })
                .catch(error => {
                    console.log(error.response)
                })
            },

            hash(str)
            {
                var hash = 0, i, chr

                if (str.length === 0) return hash

                for (i = 0; i < str.length; i++)
                {
                    chr   = str.charCodeAt(i)
                    hash  = ((hash << 5) - hash) + chr
                    hash |= 0 // Convert to 32bit integer
                }

                return Math.abs(hash)
            },

            stc(str)
            {
                let hash = this.hash(str)

                let colors = [
                    '#a55eea','#8854d0','#fc5c65','#eb3b5a','#fd9644','#fa8231','#fed330','#f7b731',
                    '#26de81','#20bf6b','#2bcbba','#0fb9b1','#45aaf2','#0a3d62','#4b7bec','#3867d6',
                    '#a5b1c2','#778ca3','#4b6584',
                ]

                return colors[hash % colors.length]
            },

            contrast(color) {
                return invert(color, true)
            }
        },
    }
</script>

<style lang="sass" scoped>
    .page-container
        width: 100%
        height: 100%

        .placeholder
            height: 200px
            width: 100%
            text-align: center

        .invite-wrapper
            display: block

            .invite
                display: flex
                padding: 15px
                border-bottom: var(--border)
                gap: 15px
                align-items: center

                .spacer
                    flex: 1

                &:last-of-type
                    border-bottom: none

        .active-team-wrapper
            padding: 30px 15px
            display: block
            text-align: center
            border-bottom: var(--border)

            h2
                margin: 0

        .namespace-container
            width: 100%
            padding: 7.5px
            font-size: 0

            .namespace
                width: calc(25% - 15px)
                height: 170px
                margin: 7.5px
                display: inline-grid
                place-content: center
                position: relative
                border-radius: 7px
                color: var(--heading-gray)
                user-select: none
                background: var(--bg)
                text-align: center
                vertical-align: top
                filter: var(--elevation-1)
                overflow: hidden
                transition: padding 100ms

                &.add-button
                    padding-bottom: 0 !important
                    cursor: pointer
                    transition: all 100ms

                    &:hover
                        filter: var(--elevation-4)

                .icon
                    font-size: 35px
                    color: inherit
                    font-family: 'Material Icons Round'
                    user-select: none
                    margin-bottom: 20px
                    opacity: 0.8

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

        .team-description-input
            resize: none
            height: 150px

        .team-wrapper
            width: 100%
            display: inline-flex
            flex-direction: column
            transition: all 300ms

            &.slide-enter
                transform: translateY(-100px)
                opacity: 0

            &.slide-leave-to
                transform: scale(0)
                opacity: 0

            &.slide-leave-active
                position: absolute

            .team-header
                display: flex
                align-items: center
                padding: 5px 5px 5px 15px
                gap: 5px
                border-radius: 7px 7px 0 0

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

                .title
                    flex: 1
                    font-size: 16px
                    line-height: 20px
                    padding: 7px 0
                    
                    b
                        text-transform: uppercase
                        font-weight: 600
                        color: var(--heading-gray)
                        display: inline-block

                .more-button
                    margin: 0
                
                .expand-button
                    transition: transform 200ms

                    &.expanded
                        transform: rotate(180deg)

            .team-content
                display: flex
                gap: 15px
                padding: 5px 15px 15px
                position: relative
                flex-wrap: wrap

                .centerer
                    width: 100%
                    display: grid
                    place-content: center
                    height: 50px

                .member-row
                    border-radius: 5px
                    border: var(--border)
                    width: 100%
                    display: flex
                    height: 50px
                    padding: 5px
                    gap: 5px

                    .more-button
                        display: block !important
                        align-self: center

                    .profile-image
                        height: 40px
                        width: 40px
                        object-fit: cover
                        border-radius: 100%
                        background: var(--bg)
                        align-self: center

                    .name
                        flex: 1
                        font-size: var(--text-size)
                        color: var(--heading-gray)
                        font-weight: 600
                        align-self: center
                        padding: 0 5px

                    .role
                        height: 20px
                        line-height: 20px
                        font-size: 12px
                        color: var(--success)
                        background: var(--success-shade)
                        padding: 0 10px
                        border-radius: 30px
                        letter-spacing: 1px
                        font-weight: 600
                        text-transform: uppercase
                        user-select: none
                        align-self: center

                        &.owner
                            background: var(--success)
                            color: white
</style>