<script setup lang="ts">
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Upload, X, File } from 'lucide-vue-next';

// Component props interface
interface Props {
    modelValue?: File | null;
    accept?: string;
    maxSize?: number; // in MB
    placeholder?: string;
    error?: string;
}

// Component emits interface
interface Emits {
    'update:modelValue': [file: File | null];
}

const props = withDefaults(defineProps<Props>(), {
    accept: '.pdf,.doc,.docx,.jpg,.jpeg,.png',
    maxSize: 10,
    placeholder: 'Choose a file or drag and drop',
});

const emit = defineEmits<Emits>();

// Component state
const isDragOver = ref(false);
const fileInput = ref<HTMLInputElement>();

// Computed properties
const selectedFile = computed(() => props.modelValue);
const maxSizeInBytes = computed(() => props.maxSize * 1024 * 1024);

// File handling methods
const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0] || null;
    validateAndEmitFile(file);
};

const handleDrop = (event: DragEvent) => {
    event.preventDefault();
    isDragOver.value = false;
    const file = event.dataTransfer?.files[0] || null;
    validateAndEmitFile(file);
};

const validateAndEmitFile = (file: File | null) => {
    if (file && file.size > maxSizeInBytes.value) {
        alert(`File size must be less than ${props.maxSize}MB`);
        return;
    }
    emit('update:modelValue', file);
};

// UI interaction methods
const openFileDialog = () => {
    fileInput.value?.click();
};

const removeFile = () => {
    emit('update:modelValue', null);
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const handleDragOver = (event: DragEvent) => {
    event.preventDefault();
    isDragOver.value = true;
};

const handleDragLeave = () => {
    isDragOver.value = false;
};
</script>

<template>
    <!-- File upload container -->
    <div class="space-y-2">
        <!-- Hidden file input -->
        <input
            ref="fileInput"
            type="file"
            :accept="accept"
            @change="handleFileSelect"
            class="hidden"
        />
        
        <!-- File upload area -->
        <div
            v-if="!selectedFile"
            @click="openFileDialog"
            @drop="handleDrop"
            @dragover="handleDragOver"
            @dragleave="handleDragLeave"
            :class="[
                'border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-colors',
                isDragOver ? 'border-primary bg-primary/5' : 'border-muted-foreground/25 hover:border-primary/50',
                error ? 'border-destructive' : ''
            ]"
        >
            <Upload class="mx-auto h-8 w-8 text-muted-foreground mb-2" />
            <p class="text-sm text-muted-foreground mb-1">{{ placeholder }}</p>
            <p class="text-xs text-muted-foreground">Max file size: {{ maxSize }}MB</p>
        </div>
        
        <!-- Selected file display -->
        <div v-else class="flex items-center justify-between p-3 border rounded-lg bg-muted/50">
            <div class="flex items-center space-x-2">
                <File class="h-4 w-4 text-muted-foreground" />
                <span class="text-sm font-medium">{{ selectedFile.name }}</span>
                <span class="text-xs text-muted-foreground">
                    ({{ (selectedFile.size / 1024 / 1024).toFixed(2) }}MB)
                </span>
            </div>
            <Button
                type="button"
                variant="ghost"
                size="sm"
                @click="removeFile"
                class="h-6 w-6 p-0"
            >
                <X class="h-3 w-3" />
            </Button>
        </div>
        
        <!-- Error message -->
        <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
    </div>
</template>