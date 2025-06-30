<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Plus, Download, Trash2, Calendar, User, File, Edit } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

// Component props interface
interface Form {
    id: number;
    name: string;
    date: string;
    file_path: string | null;
    file_original_name: string | null;
    file_url: string | null;
    created_at: string;
}

interface Props {
    forms: {
        data: Form[];
        links: any[];
        meta: any;
    };
}

defineProps<Props>();

// Breadcrumb navigation setup
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Forms',
        href: '/forms',
    },
];

// Delete form handler
const deleteForm = (formId: number) => {
    if (confirm('Are you sure you want to delete this form?')) {
        // Use Inertia router to delete the form
        router.delete(route('forms.destroy', formId));

    }
};
</script>

<template>
    <Head title="Forms" />

    <!-- Main layout with breadcrumbs -->
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Page header section -->
            <div class="flex items-center justify-between">
                <div class="flex flex-col space-y-2">
                    <h1 class="text-2xl font-semibold tracking-tight">Forms</h1>
                    <p class="text-muted-foreground">Manage your form submissions.</p>
                </div>

                <!-- Create new form button -->
                <Button as-child>
                    <Link :href="route('forms.create')" class="flex items-center gap-2">
                        <Plus class="h-4 w-4" />
                        Create Form
                    </Link>
                </Button>
            </div>

            <!-- Forms list section -->
            <div v-if="forms.data.length > 0" class="space-y-4">
                <!-- Individual form cards -->
                <Card v-for="form in forms.data" :key="form.id">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle class="flex items-center gap-2">
                                    <User class="h-4 w-4" />
                                    {{ form.name }}
                                </CardTitle>
                                <CardDescription class="flex items-center gap-4 mt-1">
                                    <span class="flex items-center gap-1">
                                        <Calendar class="h-3 w-3" />
                                        {{ new Date(form.date).toLocaleDateString() }}
                                    </span>
                                    <span class="text-xs">
                                        Submitted: {{ new Date(form.created_at).toLocaleDateString() }}
                                    </span>
                                </CardDescription>
                            </div>

                            <!-- Action buttons -->
                            <div class="flex items-center gap-2">
                                <!-- Edit form button -->
                                <Button
                                    variant="outline"
                                    size="sm"
                                    as-child
                                >
                                    <Link :href="route('forms.edit', form.id)" class="flex items-center gap-1">
                                        <Edit class="h-3 w-3" />
                                        Edit
                                    </Link>
                                </Button>

                                <!-- Download file button -->
                                <Button
                                    v-if="form.file_url"
                                    variant="outline"
                                    size="sm"
                                    as-child
                                >
                                    <a :href="form.file_url" :download="form.file_original_name" class="flex items-center gap-1">
                                        <Download class="h-3 w-3" />
                                        Download
                                    </a>
                                </Button>

                                <!-- Delete form button -->
                                <Button
                                    variant="outline"
                                    size="sm"
                                    @click="deleteForm(form.id)"
                                    class="text-destructive hover:text-destructive"
                                >
                                    <Trash2 class="h-3 w-3" />
                                </Button>
                            </div>
                        </div>
                    </CardHeader>

                    <!-- File information section -->
                    <CardContent v-if="form.file_original_name">
                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                            <File class="h-3 w-3" />
                            <span>{{ form.file_original_name }}</span>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Empty state section -->
            <Card v-else>
                <CardContent class="flex flex-col items-center justify-center py-12">
                    <File class="h-12 w-12 text-muted-foreground mb-4" />
                    <h3 class="text-lg font-medium mb-2">No forms yet</h3>
                    <p class="text-muted-foreground text-center mb-4">
                        You haven't created any forms yet. Get started by creating your first form.
                    </p>
                    <Button as-child>
                        <Link :href="route('forms.create')" class="flex items-center gap-2">
                            <Plus class="h-4 w-4" />
                            Create Your First Form
                        </Link>
                    </Button>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
