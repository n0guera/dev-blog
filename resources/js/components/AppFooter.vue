<script setup lang="ts">
import { Github } from 'lucide-vue-next';
</script>

<template>
    <footer class="container mx-auto mt-4 w-full border-t p-2 text-center">
        <p>
            &copy; 2026 n0guera. All rights reserved. &middot;
            <a
                href="https://github.com/n0guera/dev-blog"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-1 px-1 text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
            >
                <Github class="size-4" aria-hidden="true" />
                GitHub
            </a>
        </p>
    </footer>
</template>
