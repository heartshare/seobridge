<template>
    <div class="page-container limiter">
        <h1>Notifications</h1>

        <p>
            <ui-button>Normal</ui-button>
            <ui-button disabled>Disabled</ui-button>
            <ui-button loading>loading</ui-button><br>

            <ui-button text>Normal</ui-button>
            <ui-button text disabled>Disabled</ui-button>
            <ui-button text loading>loading</ui-button><br>

            <ui-button text border>Normal</ui-button>
            <ui-button text border disabled>Disabled</ui-button>
            <ui-button text border loading>loading</ui-button>
        </p>

        <p>
            <ui-button error>Normal</ui-button>
            <ui-button error disabled>Disabled</ui-button>
            <ui-button error loading>loading</ui-button><br>

            <ui-button text error>Normal</ui-button>
            <ui-button text error disabled>Disabled</ui-button>
            <ui-button text error loading>loading</ui-button><br>

            <ui-button text error border>Normal</ui-button>
            <ui-button text error border disabled>Disabled</ui-button>
            <ui-button text error border loading>loading</ui-button><br>
        </p>

        <p>
            <ui-popover-menu>
                <template v-slot:trigger>
                    <ui-icon-button>&#983513;</ui-icon-button>
                </template>

                <ui-menu-item icon="&#984214;">Share report</ui-menu-item>
                <ui-menu-item icon="&#983048;">Assign report</ui-menu-item>
                <ui-menu-item icon="&#985721;">Delete report</ui-menu-item>
            </ui-popover-menu>
        </p>
    </div>
</template>

<style lang="sass" scoped>
    .page-container
        width: 100%
</style>