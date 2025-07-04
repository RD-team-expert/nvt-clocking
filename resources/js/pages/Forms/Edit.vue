<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import InputError from '@/components/InputError.vue'
import FileUpload from '@/components/ui/file-upload/FileUpload.vue'
import { LoaderCircle, ArrowLeft } from 'lucide-vue-next'
import type { BreadcrumbItem } from '@/types'

// Props & breadcrumbs
interface Form {
  id: number
  name: string
  date: string
  file_path: string | null
  file_original_name: string | null
  file_url: string | null
  created_at: string
}
interface Props {
  form: Form
}
const props = defineProps<Props>()
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Forms', href: '/forms' },
  { title: 'Edit', href: `/forms/${props.form.id}/edit` },
]

// Inertia form
const form = useForm({
  name: props.form.name,
  date: props.form.date,
  file: null as File | null,
  _method: 'PUT',
})

// Front-end only file error
const fileError = ref<string | null>(null)

// Validate on every change
function validateFile(event: Event) {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0] ?? form.file

  if (!file) {
    fileError.value = 'Please select a file.'
    form.file = null
    return
  }

  const name = file.name.toLowerCase()
  const parts = name.split('.')
  const ext = parts.pop()
  const allowed = ['csv', 'xls', 'xlsx']
  const doubleExt = /\.(?:csv|xls|xlsx)\.(?:csv|xls|xlsx)$/i

  if (!ext || !allowed.includes(ext) || doubleExt.test(name)) {
    fileError.value = 'Invalid file type. Only CSV, XLS, XLSX allowed.'
    form.file = null
  } else {
    fileError.value = null
    form.file = file
  }
}

// Download template handler
function downloadCsv() {
  window.location.href = route('forms.downloadCsv')
}

// Submit
function submit() {
  form.post(route('forms.update', props.form.id), {
    forceFormData: true,
  })
}
</script>

<template>
  <Head title="Edit Form" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <!-- Back + Page Title -->
      <div class="flex items-center gap-4">
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
          <p class="text-muted-foreground">
            Update your form information below.
          </p>
        </div>
      </div>

      <!-- Card -->
      <Card class="max-w-2xl">
        <!-- Header w/ Download Button -->
        <CardHeader class="flex justify-between items-center">
          <div>
            <CardTitle>Edit Form Submission</CardTitle>
            <CardDescription>
              Modify your name, date, and optionally update the file.
            </CardDescription>
          </div>
          <Button variant="outline" size="sm" @click="downloadCsv">
            Download Template
          </Button>
        </CardHeader>

        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Name -->
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

            <!-- Date -->
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



            <!-- File Upload w/ Validation -->
            <div class="space-y-2">
              <Label>
                {{ props.form.file_original_name
                  ? 'Replace File (Optional)'
                  : 'File Upload (Optional)' }}
              </Label>
              <FileUpload
                v-model="form.file"
                :error="fileError ?? form.errors.file"
                accept=".csv,.xls,.xlsx"
                @change="validateFile"
                :max-size="10"
                :placeholder="
                  props.form.file_original_name
                    ? 'Choose a new file to replace current one'
                    : 'Choose a file or drag and drop'
                "
              />
              <p class="text-xs text-muted-foreground">
                Supported formats: XLSX, XLS, CSV (Max: 10MB)
                {{
                  props.form.file_original_name
                    ? ' – Leave empty to keep current file'
                    : ''
                }}
              </p>
            </div>

            <!-- Actions -->
            <div class="flex gap-4 pt-4">
              <Button
                type="submit"
                :disabled="form.processing"
                class="flex items-center gap-2"
              >
                <LoaderCircle
                  v-if="form.processing"
                  class="h-4 w-4 animate-spin"
                />
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
