<template>
    <div class="bg-gray-900">
        <div class="flex w-full items-center mb-8">
            <div class="flex w-full items-center mx-3">
                <heading>OPcache</heading>
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

        <div class="flex items-stretch bg-gray-200 mb-4">
            <div class="flex w-1/3 p-2">
                <card
                    class="w-full p-4 bg-danger text-white"
                    :class="{ 'bg-success': status.opcache_enabled }"
                >
                    <heading>
                    <!-- <boolean-icon :value="status.opcache_enabled" class="bg-white"/> -->
                        <span v-if="status.opcache_enabled" class="text-white">Enabled</span>
                        <span v-else class="text-danger">Disabled</span>
                    </heading>
                    <p class="mt-2">
                        {{config.version.opcache_product_name}} {{ config.version.version }}
                    </p>
                    <div class="flex justify-between mt-2">
                        <p>
                            <span v-if="production">Optimized for production</span>
                            <span class="text-warning" v-else>Not optimized for production</span>
                        </p>
                        <button class="underline" @click="showStatus = !showStatus">Status info</button>
                    </div>

                    <card class="w-full p-4 mt-4 bg-black text-white" v-if="showStatus">
                        <heading class="text-white mb-4">
                            Status
                        </heading>
                        <p class="mb-2">Cache full: <strong>{{ status.cache_full }}</strong><p>
                        <p class="mb-2">Restart pending: <strong>{{ status.restart_pending }}</strong></p>
                        <p class="mb-4">Restart in progress: <strong>{{ status.restart_in_progress }}</strong></p>
                        <p class="mb-2">Start time: <strong>{{ new Date(status.opcache_statistics.start_time) }}</strong></p>
                        <p class="mb-4">Last restart time: <strong>{{ new Date(status.opcache_statistics.last_restart_time) }}</strong</p>
                        <p class="mb-2">Oom restarts: <strong>{{ status.opcache_statistics.oom_restarts }}</strong></p>
                        <p class="mb-2">Hash restarts: <strong>{{ status.opcache_statistics.hash_restarts }}</strong></p>
                        <p class="mb-2">Manual restarts: <strong>{{ status.opcache_statistics.manual_restarts }}</strong></p>
                    </card>
                </card>
            </div>

            <div class="flex w-1/3 p-2">
                <BasePartitionMetric
                    title="Memory Usage"
                    help-text="Memory Usage in MB"
                    help-width="600"
                    :chart-data="memoryChart"
                    :loading="false"
                />
            </div>

            <div class="flex w-1/3 p-2">
                <BasePartitionMetric
                    title="Hit Amount"
                    :chart-data="hitRatioData"
                    :loading="false"
                />
            </div>
        </div>

        <div class="flex items-stretch bg-gray-200 mb-4">
            <div class="flex w-1/3 p-2">
                <card class="w-full p-4 bg-black text-white">
                    <heading class="text-white mb-4">
                        Cached
                    </heading>
                    <p class="mb-2">Scripts: <strong>{{ status.opcache_statistics.num_cached_scripts}}</strong></p>
                    <p class="mb-2">Keys: <strong>{{ status.opcache_statistics.num_cached_keys}}</strong> (max {{ status.opcache_statistics.max_cached_keys}})</p>
                </card>
            </div>

            <div class="flex w-1/3 p-2">
                <card class="w-full p-4 bg-black text-white">
                    <heading class="text-white mb-4">
                        Hit Ratio
                    </heading>
                    <p class="mb-2 text-right font-bold text-5xl">{{ status.opcache_statistics.opcache_hit_rate.toFixed(2) }}%</p>
                </card>
            </div>

            <div class="flex w-1/3 p-2">
                <card class="w-full p-4 bg-black text-white">
                    <heading class="text-white mb-4">
                        Blacklist
                    </heading>
                    <p class="mb-2">Misses: <strong>{{ status.opcache_statistics.blacklist_misses }}</strong></p>
                    <p class="mb-2">Miss rate: <strong>{{ status.opcache_statistics.blacklist_miss_ratio }}</strong></p>
                </card>
            </div>
        </div>

        <div class="flex items-stretch bg-gray-200 mb-4">
            <div class="flex flex-col w-full p-2">
                <card class="w-full p-4 bg-black text-white">
                    <heading class="text-white mb-4">Config</heading>
                    <ul class="list-style-none" v-if="config">
                        <li class="mb-1" :class="{'text-warning': notOptimizedKeys.includes(key)}" v-for="(item, key) in config.directives" :key="key">
                            {{ key }}: <strong>{{ item }}</strong>
                        </li>
                    </ul>
                </card>
            </div>
        </div>
    </div>
</template>

<script>
import Vue from "vue";
import BasePartitionMetric from './PartitionMetric'

export default {
    components: {
        BasePartitionMetric,
    },

    data() {
        return {
            status: [],
            config: [],
            loading: false,
            showStatus: false,
        }
    },

    computed: {
        production() {
            if (this.config.length) {
                if (
                    this.config.directives["opcache.memory_consumption"] >= 128 &&
                    this.config.directives["opcache.interned_strings_buffer"] >= 16 &&
                    !this.config.directives["opcache.validate_timestamps"]
                ) {
                    return true;
                }
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

            if (this.config.directives["opcache.memory_consumption"] < 128) {
                keys.push("opcache.memory_consumption");
            }

            if (this.config.directives["opcache.interned_strings_buffer"] < 16) {
                keys.push("opcache.interned_strings_buffer");
            }

            if (this.config.directives["opcache.validate_timestamps"]) {
                keys.push("opcache.validate_timestamps");
            }


            return keys;
        },

        hitRatioData() {
            return [
                {
                    label: 'Hits',
                    value: this.status.opcache_statistics.hits
                },
                {
                    label: 'Misses',
                    value: this.status.opcache_statistics.misses
                }
            ]
        },

        memoryChart() {
            // if (!this.status.length) {
            //     return [];
            // }

            return [
                {
                    label: 'Used',
                    value: Math.round(this.status.memory_usage.used_memory / 1048576)
                },
                {
                    label: 'Free',
                    value: Math.round(this.status.memory_usage.free_memory / 1048576)
                },
            ]
        }

    },

    mounted() {
        this.refresh();
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
                this.refresh();
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
                this.refresh();
            });
        },

        refresh() {
            this.getStatus();
            this.getConfig();
        }
    },
}
</script>

<style>
.card {
    min-height: 200px;
}
/* Scoped Styles */
</style>
