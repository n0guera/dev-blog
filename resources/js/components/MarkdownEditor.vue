<script setup lang="ts">

const markdown = {
    content: ''
};

const compiledMarkdown = {
    content: '',
};

</script>

<template>
    <div id="editor">
        <textarea v-model="markdown.content" name="markdown-input" id="markdown-input"></textarea>
        <div v-html="compiledMarkdown.content"></div>
    </div>
</template>
