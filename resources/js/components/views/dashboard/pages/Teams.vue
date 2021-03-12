<template>
    <div class="page-container limiter">
        <fieldset v-for="invite in invites" :key="invite.id">
            <legend>{{invite.team.name}}</legend>
            <p>
                You've got invited to join <b>{{invite.team.name}}</b>
            </p>
            <ui-button text border icon="&#983213;" icon-left @click="openInviteIgnoreDialog(invite.id, invite.team)">Decline</ui-button>
            <ui-button icon="&#983340;" @click="handleInvite(invite.id, 'accepted')">Accept</ui-button>
        </fieldset>

        <transition-group name="slide" class="block">
            <div class="team-wrapper" v-for="team in teams" :key="team.id">
                <div class="team-header">
                    <div class="title">
                        <b>{{team.name}}</b><br>
                        {{team.description}}
                    </div>

                    <div class="tag">{{team.category}}</div>

                    <ui-popover-menu>
                        <template v-slot:trigger>
                            <ui-icon-button class="more-button">&#983513;</ui-icon-button>
                        </template>

                        <ui-menu-item v-if="team.is_owner" icon="&#984043;" @click="openTeamEditor(team)">Edit Team</ui-menu-item>
                        <ui-menu-item v-if="team.is_owner" icon="&#983060;" @click="openTeamInviteDialog(team)">Add Member</ui-menu-item>
                        <ui-menu-divider v-if="team.is_owner"></ui-menu-divider>
                        <ui-menu-item v-if="team.is_owner" icon="&#985721;" @click="openTeamDeletionDialog(team)">Delete Team</ui-menu-item>
                        <ui-menu-item v-else icon="&#983558;" @click="openTeamLeaveDialog(team)">Leave Team</ui-menu-item>
                    </ui-popover-menu>
                </div>

                <div class="team-content">
                    <div class="member-card" v-for="member in team.members" :key="member.id">
                        <div class="background">
                            <ui-popover-menu class="more-button" v-if="team.is_owner">
                                <template v-slot:trigger>
                                    <ui-icon-button>&#983513;</ui-icon-button>
                                </template>

                                <ui-menu-item icon="&#984043;">Edit Member</ui-menu-item>
                                <ui-menu-item icon="&#983213;" @click="openMemberDeleteDialog(team, member)">Remove Member</ui-menu-item>
                            </ui-popover-menu>
                        </div>

                        <img src="/images/defaults/default_profile_image.svg" class="profile-image">

                        <div class="name" v-if="member.user.firstname || member.user.lastname">
                            {{member.user.firstname}}
                            {{member.user.lastname}}
                        </div>
                        <div class="name" v-else>
                            {{member.user.username}}
                        </div>

                        <div class="role" v-for="(role, i) in member.roles" :key="i" :class="[{'owner': role == 'owner'}]">{{role}}</div>    
                    </div>

                    <div class="add-member-card" @click="openTeamInviteDialog(team)" v-if="team.is_owner">
                        <div class="icon">&#984085;</div>
                        <div class="text">Add Member</div>
                    </div>
                </div>
            </div>
        </transition-group>



        <button class="fab" @click="openTeamEditor()">&#984085;</button>



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
    export default {
        data() {
            return {
                teamCategories: [
                    {'technology': 'Technology'},
                    {'marketing': 'Marketing'},
                    {'other': 'Other'},
                ],

                teamEdit: {
                    id: null,
                    name: '',
                    description: '',
                    category: '',
                    loading: true,
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
            teams() {
                return this.$store.getters.teams
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
                
                axios.post('/auth/team/update-or-create-team', {
                    id: this.teamEdit.id,
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

            deleteTeam(team = this.teamDelete) {
                this.teamDelete.loading = true
                
                axios.post('/auth/team/delete-team', {
                    id: team.id,
                    name: team.name,
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
                    id: this.teamLeave.id,
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
                    id: this.memberDelete.teamId,
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
                axios.post('/auth/team/handle-invite', {id, action})
                .then(response => {
                    if (action === 'accepted') this.$store.commit('setTeam', response.data)
                    else this.resetInviteIgnore()

                    this.$store.dispatch('fetchAllInvites')
                })
                .catch(error => {
                    console.log(error.response)
                })
            },
        },
    }
</script>

<style lang="sass" scoped>
    .page-container
        width: 100%

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

        .team-description-input
            resize: none
            height: 150px

        .team-wrapper
            width: 100%
            display: inline-flex
            flex-direction: column
            background: white
            border-radius: 7px
            filter: var(--elevation-2)
            margin: 15px 0
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
                padding: 5px 0
                border-radius: 7px 7px 0 0

                .tag
                    margin-left: 15px
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
                    padding: 7px 15px
                    
                    b
                        text-transform: uppercase
                        font-weight: 600
                        color: var(--heading-gray)
                        display: inline-block

                .more-button
                    margin: 0 5px

            .team-content
                display: flex
                gap: 15px
                padding: 5px 15px 15px
                position: relative
                flex-wrap: wrap

                .add-member-card
                    border-radius: 5px
                    background: var(--bg)
                    border: var(--border)
                    width: 170px
                    height: 220px
                    display: grid
                    place-content: center
                    cursor: pointer

                    &:hover
                        border-color: transparent
                        filter: var(--elevation-3)

                    .icon
                        font-family: 'Material Icons'
                        font-size: 28px
                        color: var(--text-gray)
                        user-select: none
                        text-align: center
                        margin-bottom: 10px

                    .text
                        font-size: var(--button-size)
                        color: var(--text-gray)
                        font-weight: 600
                        letter-spacing: 1px
                        text-transform: uppercase

                .member-card
                    border-radius: 5px
                    border: var(--border)
                    width: 170px
                    height: 220px
                    position: relative

                    &:hover .more-button
                        opacity: 1

                    .more-button
                        position: absolute
                        top: 0
                        right: 0
                        display: block !important
                        opacity: 0

                    .background
                        height: 80px
                        width: 100%
                        border-bottom: var(--border)
                        background: var(--bg)
                        background-image: url('/images/app/dashboard/pattern.svg')
                        background-size: 1000px
                        background-position: top
                        background-repeat: no-repeat
                        border-radius: 5px 5px 0 0
                        display: block

                    .profile-image
                        height: 70px
                        width: 70px
                        object-fit: cover
                        border-radius: 100%
                        margin: -35px auto 10px
                        display: block
                        background: var(--bg)
                        font-size: 15px

                    .name
                        width: 100%
                        font-size: var(--text-size)
                        color: var(--heading-gray)
                        font-weight: 600
                        height: 30px
                        display: grid
                        place-content: center
                        margin-bottom: 10px

                    .role
                        font-size: 13px
                        color: var(--primary)
                        background: var(--primary-shade)
                        padding: 2px 10px 0
                        border-radius: 30px
                        letter-spacing: 1px
                        font-weight: 600
                        text-transform: uppercase
                        display: block
                        margin: 0 auto
                        width: min-content
                        text-overflow: ellipsis
                        overflow: hidden
                        white-space: nowrap
                        user-select: none

                        &.owner
                            background: var(--primary)
                            color: white
</style>