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
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <!-- Editor Panel -->
        <div
            class="flex flex-col overflow-hidden rounded-lg border border-border bg-card shadow-sm dark:border-border dark:bg-card">
            <div class="flex items-center justify-between border-b border-border bg-muted px-4 py-3">
                <span class="text-sm font-medium text-foreground">Editor</span>
                <span class="text-xs text-muted-foreground">Markdown</span>
            </div>
            <div class="max-h-150 min-h-100 flex-1 overflow-hidden">
                <textarea v-model="markdown" name="markdown-input" id="markdown-input"
                    class="h-full min-h-100 w-full resize-none border-0 bg-transparent p-4 text-sm leading-relaxed text-foreground placeholder:text-muted-foreground focus:ring-0 focus:outline-none"
                    placeholder="Escribe tu contenido en Markdown..." spellcheck="false"></textarea>
            </div>
        </div>

        <!-- Preview Panel -->
        <div
            class="flex flex-col overflow-hidden rounded-lg border border-border bg-card shadow-sm dark:border-border dark:bg-card">
            <div class="flex items-center justify-between border-b border-border bg-muted px-4 py-3">
                <span class="text-sm font-medium text-foreground">Preview</span>
                <span class="text-xs text-muted-foreground">Real-time rendering</span>
            </div>
            <div class="max-h-150 min-h-100 flex-1 overflow-hidden">
                <div v-if="!markdown"
                    class="flex h-full min-h-100 flex-col items-center justify-center gap-3 text-muted-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="opacity-50">
                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                        <polyline points="14 2 14 8 20 8" />
                    </svg>
                    <p class="text-sm">Preview will appear here</p>
                </div>
                <div v-else v-html="compiledMarkdown"
                    class="prose prose-sm dark:prose-invert h-full overflow-y-auto p-4 text-foreground **:text-foreground [&_a]:text-primary [&_a]:underline [&_a]:underline-offset-2 hover:[&_a]:opacity-80 [&_blockquote]:mb-3 [&_blockquote]:ml-0 [&_blockquote]:border-l-2 [&_blockquote]:border-border [&_blockquote]:pl-4 [&_blockquote]:text-muted-foreground [&_blockquote]:italic [&_code]:rounded [&_code]:bg-muted [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:text-sm [&_h1]:mb-3 [&_h1]:text-2xl [&_h1]:font-semibold [&_h2]:mt-6 [&_h2]:mb-2 [&_h2]:text-xl [&_h2]:font-semibold [&_h3]:mt-5 [&_h3]:mb-2 [&_h3]:text-lg [&_h3]:font-semibold [&_hr]:my-6 [&_hr]:border-0 [&_hr]:border-t [&_hr]:border-border [&_img]:h-auto [&_img]:max-w-full [&_img]:rounded-md [&_li]:mb-1 [&_ol]:mb-3 [&_ol]:pl-6 [&_p]:mb-3 [&_p]:leading-relaxed [&_pre]:mb-3 [&_pre]:rounded-lg [&_pre]:bg-muted [&_pre]:p-4 [&_pre_code]:bg-transparent [&_pre_code]:p-0 [&_table]:mb-3 [&_table]:w-full [&_table]:border-collapse [&_td]:border [&_td]:border-border [&_td]:p-2 [&_td]:text-left [&_th]:border [&_th]:border-border [&_th]:bg-muted [&_th]:p-2 [&_th]:text-left [&_th]:font-semibold [&_ul]:mb-3 [&_ul]:pl-6">
                </div>
            </div>
        </div>
    </div>
</template>
