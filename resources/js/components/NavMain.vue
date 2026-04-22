<script setup lang="ts">
import { Home, Rss, BookOpen } from 'lucide-vue-next';
import {
    NavigationMenu,
    NavigationMenuContent,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
    NavigationMenuTrigger,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu';
import { cn } from '@/lib/utils';
import { home, dashboard, login, logout, register } from '@/routes';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const items = [
    {
        title: 'Home',
        href: home().url,
        icon: Home,
    },
    {
        title: 'Blog',
        href: 'blog.index',
        icon: Rss,
    },
    {
        title: 'Docs',
        href: 'docs.index',
        icon: BookOpen,
    },
];
</script>

<template>
    <NavigationMenu>
        <NavigationMenuList class="space-x-8">
            <NavigationMenuItem v-for="item in items" :key="item.title">
                <NavigationMenuLink
                    :class="
                        cn(
                            'group relative inline-flex h-9 w-max items-center justify-center px-0.5 py-2 text-sm font-medium',
                            'before:absolute before:inset-x-0 before:bottom-0 before:h-[2px] before:scale-x-0 before:bg-primary before:transition-transform',
                            'hover:text-accent-foreground hover:before:scale-x-100',
                            'focus:text-accent-foreground focus:outline-none focus:before:scale-x-100',
                        )
                    "
                >
                    <a :href="item.href" class="flex items-center gap-2.5">
                        <component :is="item.icon" class="h-5 w-5 shrink-0" />
                        {{ item.title }}
                    </a>
                </NavigationMenuLink>
            </NavigationMenuItem>
        </NavigationMenuList>
    </NavigationMenu>
</template>
