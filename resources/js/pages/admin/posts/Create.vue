<script setup lang="ts">
import MarkdownEditor from '@/components/MarkdownEditor.vue';
defineProps<{ statuses: any }>();
</script>

<template>
    <div>Create Post</div>
    <MarkdownEditor />
</template>
