<script setup lang="ts">
import DOMPurify from 'dompurify';
import { marked } from 'marked';
import { computed } from 'vue';

const props = defineProps<{
    content: string,
}>();

const compiledMarkdown = computed(() => {
    const rawHtml = marked.parse(props.content || '');

    return DOMPurify.sanitize(rawHtml as string);
})
</script>

<template>
    <div v-html="compiledMarkdown" class=""></div>
</template>
