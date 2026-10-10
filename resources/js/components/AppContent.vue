<script setup lang="ts">
import { computed } from 'vue';
import type { AppVariant } from '@/types';

type Props = {
    variant?: AppVariant;
    class?: string;
};

const props = withDefaults(defineProps<Props>(), {
    variant: 'header',
});
const className = computed(() => props.class);
</script>

<template>
    <main class="container mx-auto flex min-w-3/4 flex-col" :class="className">
        <slot />
    </main>
</template>
