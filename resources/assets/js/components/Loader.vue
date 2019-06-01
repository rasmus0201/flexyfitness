<template>
    <v-layout v-show="show" align-center justify-center row>
        <half-circle-spinner
            :animation-duration="duration"
            :size="size"
            :color="color"
        />
    </v-layout>
</template>

<script>
    import { HalfCircleSpinner  } from 'epic-spinners'

    export default {
        components: {
            HalfCircleSpinner
        },

        props: {
            loading: { default: true },
            size: { default: 120 },
            color: {
                default: function () {
                    return this.$vuetify.theme.primary;
                }
            },
            duration: { default: 800 },
        },

        data() {
            return {
                show: this.loading
            }
        },

        watch: {
            loading: function (newValue, oldValue) {
                this.show = newValue;
            }
        }
    }
</script>
