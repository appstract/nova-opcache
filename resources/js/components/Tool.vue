<template>
    <div class="bg-gray-900">
        <div class="flex">
            <div class="w-full flex items-center mb-6">
                <div class="flex w-full  items-center mx-3">
                    <heading class="mb-6">OPcache</heading>
                </div>
                <div class="flex-no-shrink ml-auto">
                    <button type="button"
                            @click.stop="compile"
                            :disabled="loading"
                            class="btn btn-default btn-primary"
                    >
                        Compile scripts
                    </button>
                    <button type="button"
                       @click.stop="clear"
                       :disabled="loading"
                       class="btn btn-default btn-primary"
                    >
                    Clear OPcache
                    </button>
                </div>
            </div>
        </div>

        <div class="flex bg-gray-200">
            <card
                class="w-1/3 m-2 p-8 bg-black"
            >
                <heading class="text-white">
                    <boolean-icon :value="status.opcache_enabled"/>
                    <span v-if="status.opcache_enabled">OPcache Enabled</span>
                    <span v-else>OPcache disabled</span>
                </heading>
            </card>
            <card
                class="w-1/3 m-2 p-4 bg-black"
            >
                <heading class="text-white">
                    Stats</heading>
            </card>
            <card
                class="w-1/3 m-2 p-4 bg-black"
            >
                <heading class="text-white">
                    nog iets</heading>
            </card>
        </div>

        <card
            class="flex flex-col m-2 p-4 bg-black text-white text-sm"
            style="min-height: 300px"
        >
            <pre>{{ status }}</pre>
        </card>
    </div>
</template>

<script>
import Vue from "vue";

export default {
    data() {
        return {
            status: [],
            loading: false
        }
    },
    mounted() {
        this.getStatus();
    },
    methods: {
        getStatus() {
            Nova.request().get('/nova-vendor/nova-opcache/status').then(response => {
                this.status = response.data;
            });
        },

        compile() {
            this.loading = true;

            Nova.request().get('/nova-vendor/nova-opcache/compile').then(response => {
                //this.status = response.data;
                this.loading = false;
                this.$toasted.show('Scripts compiled!', { type: 'success' });
            }).catch(response => {
                this.loading = false;
                this.$toasted.show(response.data, { type: 'error' });
            });
        },

        clear() {
            this.loading = true;

            Nova.request().get('/nova-vendor/nova-opcache/clear').then(response => {
                //this.status = response.data;
                this.loading = false;
                this.$toasted.show('OPcache cleared!', { type: 'success' });
            });
        },
    },
}
</script>

<style>
/* Scoped Styles */
</style>
