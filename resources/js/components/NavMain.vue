<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Home, Rss, BookOpen } from 'lucide-vue-next'
import {
    NavigationMenu,
    NavigationMenuContent,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
    NavigationMenuTrigger,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu';
import { cn } from "@/lib/utils";
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
]
</script>

<template>
    <NavigationMenu>
        <NavigationMenuList class="space-x-8">
            <NavigationMenuItem v-for="item in items" :key="item.title">
                <NavigationMenuLink :class="cn(
                    'group relative inline-flex h-9 w-max items-center justify-center px-0.5 py-2 text-sm font-medium',
                    'before:absolute before:inset-x-0 before:bottom-0 before:h-[2px] before:scale-x-0 before:bg-primary before:transition-transform',
                    'hover:text-accent-foreground hover:before:scale-x-100',
                    'focus:text-accent-foreground focus:outline-none focus:before:scale-x-100'
                )">
                    <a :href="item.href" class="flex items-center gap-2.5">
                        <component :is="item.icon" class="h-5 w-5 shrink-0" />
                        {{ item.title }}
                    </a>
                </NavigationMenuLink>
            </NavigationMenuItem>
        </NavigationMenuList>
    </NavigationMenu>
    <!-- <NavigationMenu :viewport="false">
        <NavigationMenuList>
            <NavigationMenuItem>
                <NavigationMenuLink as-child>
                    <a href="/">Home</a>
                </NavigationMenuLink>
            </NavigationMenuItem>
            <NavigationMenuItem>
                <NavigationMenuTrigger>Tags</NavigationMenuTrigger>
                <NavigationMenuContent>
                    <ul class="grid w-[200px] gap-4">
                        <li>
                            <NavigationMenuLink as-child>
                                <a href="#">Tag 1</a>
                            </NavigationMenuLink>
                            <NavigationMenuLink as-child>
                                <a href="#">Tag 2</a>
                            </NavigationMenuLink>
                            <NavigationMenuLink as-child>
                                <a href="#">Tag 3</a>
                            </NavigationMenuLink>
                        </li>
                    </ul>
                </NavigationMenuContent>
            </NavigationMenuItem>
            <NavigationMenuItem>
                <NavigationMenuLink as-child :class="navigationMenuTriggerStyle()">
                    <a href="/docs">About</a>
                </NavigationMenuLink>
            </NavigationMenuItem>
            <NavigationMenuItem>
                <NavigationMenuLink v-if="$page.props.auth.user.isAdmin" :href="dashboard()"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                    Dashboard
                </NavigationMenuLink>
                <template v-else>
                    <NavigationMenuLink
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]">
                        <Link :href="logout()">Logout</Link>
                    </NavigationMenuLink>
                    <NavigationMenuLink v-if="canRegister" :href="register()"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                        Register
                    </NavigationMenuLink>
                </template>
</NavigationMenuItem>
</NavigationMenuList>
</NavigationMenu> -->
</template>
