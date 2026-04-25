<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppFooter from '@/components/AppFooter.vue';
import AppHeader from '@/components/AppHeader.vue';
</script>

<template>
    <div class="flex flex-col items-center container mx-auto">
        <AppHeader></AppHeader>
        <AppContent></AppContent>
        <AppFooter></AppFooter>
    </div>
</template>
