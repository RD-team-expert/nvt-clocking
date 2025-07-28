<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

// Helper function to check if user has a specific permission
const page = usePage();
const can = (permissionName: string): boolean => {
    const permissions = [...(page.props.permissions || [])];
    return permissions.some(permission => permission.name === permissionName);
};

// Check if user has both users.index and roles.index permissions
const hasUserManagementAccess = can('users.index') || can('roles.index');

// Define sidebar navigation items conditionally
const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: '/settings/profile',
    },
    {
        title: 'Password',
        href: '/settings/password',
    },
    {
        title: 'Appearance',
        href: '/settings/appearance',
    },
];

// Only add User Management if user has both required permissions
if (hasUserManagementAccess) {
    sidebarNavItems.push({
        title: 'User Management',
        href: '/settings/user-management',
    });
}

const currentPath = page.props.ziggy?.location ? new URL(page.props.ziggy.location).pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <Heading title="Settings" description="Manage your profile and account settings" />

        <div class="flex flex-col  space-y-8 md:space-y-0 lg:flex-row lg:space-y-0 lg:space-x-12">
            <aside class="w-full mb-2 md:mb-4 max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1 space-x-0">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="item.href"
                        variant="ghost"
                        :class="['w-full justify-start', { 'bg-muted': currentPath === item.href }]"
                        as-child
                    >
                        <Link :href="item.href">
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 md:hidden" />

            <div class="flex-1 md:max-w-4xl">
                <section :class="[currentPath === '/settings/user-management' ? 'w-full' : 'max-w-xl', 'space-y-12']">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
