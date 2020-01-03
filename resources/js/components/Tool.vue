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
                class="w-1/3 m-2 p-6 bg-danger text-white"
                :class="{ 'bg-success': status.opcache_enabled }"
            >
                <heading>
<!--                    <boolean-icon :value="status.opcache_enabled" class="bg-white"/>-->
                    <span v-if="status.opcache_enabled" class="text-white">Enabled</span>
                    <span v-else class="text-danger">Disabled</span>
                </heading>
                <p class="mt-2">
                    {{config.version.opcache_product_name}} {{ config.version.version }}
                    <span v-if="production">- Optimized for production</span>
                    <span class="text-black" v-else>- Not optimized for production</span>
                </p>
            </card>
            <card
                class="w-1/3 m-2 p-4 bg-black"
            >
                <heading class="text-white">
                </heading>
            </card>
            <card
                class="w-1/3 m-2 p-4 bg-black"
            >
                <heading class="text-white"></heading>
            </card>
        </div>
        <div class="flex bg-gray-200">
            <card
                class="w-1/3 m-2 p-8 bg-black text-white"
            >
                <heading class="text-white mb-4">
                    Status
                </heading>
                <ul>
                    <li class="mb-1">
                        cache_full: <strong>{{ status.cache_full }}</strong>
                    </li>
                    <li class="mb-1">
                        restart_pending: <strong>{{ status.restart_pending }}</strong>
                    </li>
                    <li class="mb-4">
                        restart_in_progress: <strong>{{ status.restart_in_progress }}</strong>
                    </li>

                    <li class="mb-1" v-for="(item, key) in status.opcache_statistics">
                        {{ key }}: <strong>{{ item }}</strong>
                    </li>
                </ul>
            </card>
            <card
                class="w-2/3 m-2 p-4 bg-black text-white"
            >
                <heading class="text-white mb-4">Config</heading>
                <ul v-if="config">
                    <li class="mb-1" :class="{'text-warning': notOptimizedKeys.includes(key)}" v-for="(item, key) in config.directives">
                        {{ key }}: <strong>{{ item }}</strong>
                    </li>
                </ul>
            </card>
        </div>


        <pre>{{ status }}</pre>

        <pre>{{ config }}</pre>

    </div>
</template>

<script>
import Vue from "vue";

export default {
    data() {
        return {
            status: [],
            config: [],
            loading: false
        }
    },

    computed: {
        production() {

            if (
                this.config.directives["opcache.memory_consumption"] >= 128000000 &&
                this.config.directives["opcache.interned_strings_buffer"] >= 16 &&
                !this.config.directives["opcache.validate_timestamps"]
            ) {
                return true;
            }

            return false;
        },

        notOptimizedKeys() {
            let keys = [];

            if (!this.config.directives["opcache.enable"]) {
                keys.push("opcache.enable");
            }

            if (!this.config.directives["opcache.dups_fix"]) {
                keys.push("opcache.dups_fix");
            }

            if (this.config.directives["opcache.memory_consumption"] < 128000000) {
                keys.push("opcache.memory_consumption");
            }

            if (this.config.directives["opcache.interned_strings_buffer"] < 16) {
                keys.push("opcache.interned_strings_buffer");
            }

            if (this.config.directives["opcache.validate_timestamps"]) {
                keys.push("opcache.validate_timestamps");
            }


            return keys;
        }

    },

    mounted() {
        this.getStatus();
        this.getConfig();
    },
    methods: {
        getStatus() {
            Nova.request().get('/nova-vendor/nova-opcache/status').then(response => {
                this.status = response.data;
            });
        },

        getConfig() {
            Nova.request().get('/nova-vendor/nova-opcache/config').then(response => {
                this.config = response.data;
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
