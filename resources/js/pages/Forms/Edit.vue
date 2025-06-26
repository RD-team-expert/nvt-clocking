<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import FileUpload from '@/components/ui/file-upload/FileUpload.vue';
import { LoaderCircle, ArrowLeft } from 'lucide-vue-next';
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
    form: Form;
}

const props = defineProps<Props>();

// Breadcrumb navigation setup
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Forms',
        href: '/forms',
    },
    {
        title: 'Edit',
        href: `/forms/${props.form.id}/edit`,
    },
];

// Form state management with existing data
const form = useForm({
    name: props.form.name,
    date: props.form.date,
    file: null as File | null,
    _method: 'PUT', // Laravel method spoofing for PUT request
});

// Form submission handler
const submit = () => {
    form.post(route('forms.update', props.form.id), {
        forceFormData: true, // Required for file uploads
        onSuccess: () => {
            // Form will redirect to forms.index on success
        },
    });
};
</script>

<template>
    <Head title="Edit Form" />

    <!-- Main layout with breadcrumbs -->
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Page header section -->
            <div class="flex items-center gap-4">
                <!-- Back button -->
                <Button
                    variant="outline"
                    size="sm"
                    @click="$inertia.visit(route('forms.index'))"
                    class="flex items-center gap-2"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Back to Forms
                </Button>
                
                <div class="flex flex-col space-y-2">
                    <h1 class="text-2xl font-semibold tracking-tight">Edit Form</h1>
                    <p class="text-muted-foreground">Update your form information below.</p>
                </div>
            </div>
            
            <!-- Form card container -->
            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Edit Form Submission</CardTitle>
                    <CardDescription>
                        Modify your name, date, and optionally update the file.
                    </CardDescription>
                </CardHeader>
                
                <CardContent>
                    <!-- Form element -->
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Name input field -->
                        <div class="space-y-2">
                            <Label for="name">Name *</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Enter your full name"
                                required
                                :disabled="form.processing"
                            />
                            <InputError :message="form.errors.name" />
                        </div>
                        
                        <!-- Date input field -->
                        <div class="space-y-2">
                            <Label for="date">Date *</Label>
                            <Input
                                id="date"
                                v-model="form.date"
                                type="date"
                                required
                                :disabled="form.processing"
                            />
                            <InputError :message="form.errors.date" />
                        </div>
                        
                        <!-- Current file display section -->
                        <div v-if="form.file_original_name && !form.file" class="space-y-2">
                            <Label>Current File</Label>
                            <div class="flex items-center justify-between p-3 border rounded-lg bg-muted/30">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium">{{ form.file_original_name }}</span>
                                    <span class="text-xs text-muted-foreground">(Current file)</span>
                                </div>
                                <Button
                                    v-if="form.file_url"
                                    variant="outline"
                                    size="sm"
                                    as-child
                                >
                                    <a :href="form.file_url" :download="form.file_original_name" class="flex items-center gap-1">
                                        Download
                                    </a>
                                </Button>
                            </div>
                        </div>
                        
                        <!-- File upload field -->
                        <div class="space-y-2">
                            <Label>{{ form.file_original_name ? 'Replace File (Optional)' : 'File Upload (Optional)' }}</Label>
                            <FileUpload
                                v-model="form.file"
                                :error="form.errors.file"
                                accept=".xlsx,.xls,.csv"
                                :max-size="10"
                                :placeholder="form.file_original_name ? 'Choose a new file to replace current one' : 'Choose a file or drag and drop'"
                            />
                            <p class="text-xs text-muted-foreground">
                                Supported formats: XLSX, XLS, CSV (Max: 10MB)
                                {{ form.file_original_name ? ' - Leave empty to keep current file' : '' }}
                            </p>
                        </div>
                        
                        <!-- Form submission buttons -->
                        <div class="flex gap-4 pt-4">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="flex items-center gap-2"
                            >
                                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                {{ form.processing ? 'Updating...' : 'Update Form' }}
                            </Button>
                            
                            <Button
                                type="button"
                                variant="outline"
                                :disabled="form.processing"
                                @click="$inertia.visit(route('forms.index'))"
                            >
                                Cancel
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>