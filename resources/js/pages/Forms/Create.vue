<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import FileUpload from '@/components/ui/file-upload/FileUpload.vue';
import { LoaderCircle } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

// Breadcrumb navigation setup
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Forms',
        href: '/forms',
    },
    {
        title: 'Create',
        href: '/forms/create',
    },
];

// Form state management
const form = useForm({
    name: '',
    date: '',
    file: null as File | null,
});

// Form submission handler
const submit = () => {
    form.post(route('forms.store'), {
        forceFormData: true, // Required for file uploads
        onSuccess: () => {
            // Form will redirect to forms.index on success
        },
    });
};
</script>

<template>
    <Head title="Create Form" />

    <!-- Main layout with breadcrumbs -->
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Page header section -->
            <div class="flex flex-col space-y-2">
                <h1 class="text-2xl font-semibold tracking-tight">Create New Form</h1>
                <p class="text-muted-foreground">Fill out the form below to submit your information.</p>
            </div>
            
            <!-- Form card container -->
            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Form Submission</CardTitle>
                    <CardDescription>
                        Please provide your name, select a date, and optionally upload a file.
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
                        
                        <!-- File upload field -->
                        <div class="space-y-2">
                            <Label>File Upload (Optional)</Label>
                            <FileUpload
                                v-model="form.file"
                                :error="form.errors.file"
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                :max-size="10"
                                placeholder="Choose a file or drag and drop"
                            />
                            <p class="text-xs text-muted-foreground">
                                Supported formats: PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 10MB)
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
                                {{ form.processing ? 'Submitting...' : 'Submit Form' }}
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