<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppFooter from '@/components/AppFooter.vue';
import AppHeader from '@/components/AppHeader.vue';
</script>

<template>
    <div class="min-w-1/2">
        <AppHeader />
        <AppContent>
            <slot />
        </AppContent>
        <AppFooter />
    </div>
</template>
