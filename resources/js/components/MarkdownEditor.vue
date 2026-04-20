<script setup lang="ts">
import DOMPurify from 'dompurify';
import { marked } from 'marked';
import { ref, computed } from 'vue';

const markdown = ref('');

const compiledMarkdown = computed(() => {
    const rawHtml = marked.parse(markdown.value || '');

    return DOMPurify.sanitize(rawHtml as string);
});
</script>

<template>
    <div id="editor">
        <textarea v-model="markdown" name="markdown-input" id="markdown-input"></textarea>
        <div v-html="compiledMarkdown"></div>
    </div>
</template>
