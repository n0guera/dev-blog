<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    tag: { id: number; name: string; slug: string };
    clickable?: boolean;
}>();
</script>

<template>
    <Link
        v-if="clickable !== false"
        :href="`/tags/${tag.slug}`"
        class="inline-flex items-center rounded-full bg-secondary px-2.5 py-0.5 text-xs font-medium text-secondary-foreground transition-opacity hover:opacity-80"
    >
        {{ tag.name }}
    </Link>
    <span
        v-else
        class="inline-flex items-center rounded-full bg-secondary px-2.5 py-0.5 text-xs font-medium text-secondary-foreground"
    >
        {{ tag.name }}
    </span>
</template>
