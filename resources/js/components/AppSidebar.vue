<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { FileText, LayoutGrid } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

// Get the page props to access permissions
const page = usePage();

// Helper function to check permissions
const can = (permissionName: string): boolean => {
    // Check both possible permission locations
    const permissions = [...(page.props.permissions || [])];

    return permissions.some((permission) => permission.name === permissionName);
};

// Main navigation items with Forms added only if user has permission
const mainNavItems: NavItem[] = [];
// Only add Dashboard to navigation if user has clocking.index permission
if (can('clcokings.index')) {
    mainNavItems.push({
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    });
}

// Only add Forms to navigation if user has forms.index permission
if (can('forms.index')) {
    mainNavItems.push({
        title: 'Forms',
        href: '/forms',
        icon: FileText,
    });
}

// Footer navigation items
const footerNavItems: NavItem[] = [
    // {{
    //     title: 'Github Repo',
    //     href: 'https://github.com/laravel/vue-starter-kit',
    //     icon: Folder,
    // },
    // {{
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#vue',
    //     icon: BookOpen,
    // },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
            <NavFooter :items="footerNavItems" />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
