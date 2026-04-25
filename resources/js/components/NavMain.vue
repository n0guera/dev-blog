<script setup lang="ts">
import { Home, FileText, Info } from 'lucide-vue-next';
import PostController from '@/actions/App/Http/Controllers/PostController';
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
} from '@/components/ui/navigation-menu';
import { cn } from '@/lib/utils';
import { home } from '@/routes';

const navItems = [
    {
        title: 'Home',
        href: home().url,
        icon: Home,
    },
    {
        title: 'Posts',
        href: PostController.index().url,
        icon: FileText,
    },
    {
        title: 'About',
        href: '/about',
        icon: Info,
    },
];
</script>

<template>
    <NavigationMenu>
        <NavigationMenuList class="space-x-8">
            <NavigationMenuItem v-for="item in navItems" :key="item.title">
                <NavigationMenuLink :class="cn(
                    'group relative inline-flex h-9 w-max items-center justify-center px-0.5 py-2 text-sm font-medium',
                    'before:absolute before:inset-x-0 before:bottom-0 before:h-0.5 before:scale-x-0 before:bg-primary before:transition-transform',
                    'hover:text-accent-foreground hover:before:scale-x-100',
                    'focus:text-accent-foreground focus:outline-none focus:before:scale-x-100',
                )
                    ">
                    <a :href="item.href" class="flex items-center gap-2.5">
                        <component :is="item.icon" class="h-5 w-5 shrink-0" />
                        {{ item.title }}
                    </a>
                </NavigationMenuLink>
            </NavigationMenuItem>
        </NavigationMenuList>
    </NavigationMenu>
</template>
