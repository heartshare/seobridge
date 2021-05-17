<template>
    <div class="page-container">
        <div class="page-header">
            <div class="limiter">
                <div class="page-header-wrapper">
                    <div class="row">
                        <h1>Notifications</h1>
                        <div class="spacer"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="limiter overlap">
            <div class="sheet padding">
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
                    <ui-icon-button>&#985296;</ui-icon-button>
                    <ui-icon-button disabled>&#985296;</ui-icon-button>
                    <ui-icon-button loading>&#985296;</ui-icon-button><br>

                    <ui-icon-button info>&#985296;</ui-icon-button>
                    <ui-icon-button info disabled>&#985296;</ui-icon-button>
                    <ui-icon-button info loading>&#985296;</ui-icon-button><br>

                    <ui-icon-button error>&#985296;</ui-icon-button>
                    <ui-icon-button error disabled>&#985296;</ui-icon-button>
                    <ui-icon-button error loading>&#985296;</ui-icon-button><br>

                    <ui-icon-button border>&#985296;</ui-icon-button>
                    <ui-icon-button border disabled>&#985296;</ui-icon-button>
                    <ui-icon-button border loading>&#985296;</ui-icon-button><br>

                    <ui-icon-button border info>&#985296;</ui-icon-button>
                    <ui-icon-button border info disabled>&#985296;</ui-icon-button>
                    <ui-icon-button border info loading>&#985296;</ui-icon-button><br>

                    <ui-icon-button border error>&#985296;</ui-icon-button>
                    <ui-icon-button border error disabled>&#985296;</ui-icon-button>
                    <ui-icon-button border error loading>&#985296;</ui-icon-button><br>

                    <ui-icon-button solid>&#985296;</ui-icon-button>
                    <ui-icon-button solid disabled>&#985296;</ui-icon-button>
                    <ui-icon-button solid loading>&#985296;</ui-icon-button><br>

                    <ui-icon-button solid info>&#985296;</ui-icon-button>
                    <ui-icon-button solid info disabled>&#985296;</ui-icon-button>
                    <ui-icon-button solid info loading>&#985296;</ui-icon-button><br>

                    <ui-icon-button solid error>&#985296;</ui-icon-button>
                    <ui-icon-button solid error disabled>&#985296;</ui-icon-button>
                    <ui-icon-button solid error loading>&#985296;</ui-icon-button><br>
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
        </div>
    </div>
</template>

<style lang="sass" scoped>
    .page-container
        width: 100%
</style>